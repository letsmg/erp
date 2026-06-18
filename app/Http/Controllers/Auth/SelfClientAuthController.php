<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SelfClientRequest;
use App\Http\Requests\Auth\SelfClientLoginRequest;
use App\Models\Client;
use App\Models\User;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use App\Services\ClientAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class SelfClientAuthController extends Controller
{
    public function __construct(
        private readonly ClientRepository $repository,
        private readonly ClientService $service,
        private readonly ClientAuthService $clientAuthService,
    ) {}

    public function showLogin(Request $request)
    {
        if (Auth::guard('client')->check()) {
            return redirect()->route('client.dashboard');
        }

        if ($request->has('redirect')) {
            session(['url.intended' => $request->input('redirect')]);
        }

        return Inertia::render('Client/Auth/Login', [
            'userIp' => request()->ip(),
            'status' => session('status'),
        ]);
    }

    public function login(SelfClientLoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if ($this->clientAuthService->login($credentials, $request->boolean('remember'))) {
            $client = Auth::guard('client')->user();

            if (!$client->is_active) {
                Auth::guard('client')->logout();
                return back()->withErrors([
                    'email' => 'Sua conta de cliente está bloqueada. Entre em contato com a administração.',
                ]);
            }

            $intendedUrl = session()->pull('url.intended');

            if ($intendedUrl) {
                return redirect($intendedUrl);
            }

            return redirect()->route('client.dashboard');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas ou conta bloqueada.',
        ]);
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('client.login')
                ->with('error', 'Usuário não encontrado.');
        }

        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('client.login')
                ->with('error', 'Link de verificação inválido.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('client.login')
                ->with('info', 'Email já verificado.');
        }

        $user->markEmailAsVerified();
        $user->update(['is_active' => true]);

        $client = $this->repository->findByUserId($user->id);
        if ($client) {
            $client->update([
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        return redirect()->route('client.login')
            ->with('success', 'Email verificado com sucesso! Sua conta está ativa.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->clientAuthService->logout($request);
        return redirect()->route('store.index');
    }

    public function profile()
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return redirect()->route('client.register.form')
                ->with('error', 'Complete seu cadastro para continuar.');
        }

        $client->load(['addresses' => function ($query) {
            $query->orderBy('is_delivery_address', 'desc');
        }]);

        return inertia('Client/Profile', [
            'client' => $client,
            'addresses' => $client->addresses,
        ]);
    }

    public function showRegistrationForm()
    {
        return inertia('Client/Auth/Register');
    }

    public function edit()
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return redirect()->route('client.register.form')
                ->with('error', 'Complete seu cadastro para continuar.');
        }

        return inertia('Client/Edit', [
            'client' => $client,
        ]);
    }

    public function register(SelfClientRequest $request)
    {
        try {
            $data = $request->validated();
            $clientData = $this->prepareClientRegistrationData($data);

            $client = $this->service->createClientOnly($clientData);

            return redirect()->route('client.login')
                ->with('success', 'Cadastro realizado! Um link de confirmação foi enviado para seu e-mail.');

        } catch (\Exception $e) {
            \Log::error('Client registration error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erro ao realizar cadastro: ' . $e->getMessage());
        }
    }

    public function update(SelfClientRequest $request)
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return back()->with('error', 'Cliente não encontrado.');
        }

        try {
            $data = $request->validated();
            $this->repository->update($client, $data);

            return redirect()->route('client.profile')
                ->with('success', 'Dados atualizados com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar dados: ' . $e->getMessage());
        }
    }

    public function showDeleteForm()
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return back()->with('error', 'Cliente não encontrado.');
        }

        $hasRecentPurchases = $this->service->hasRecentPurchases($client->id, 5);

        return inertia('Client/Delete', [
            'client' => $client,
            'canDelete' => !$hasRecentPurchases,
            'hasRecentPurchases' => $hasRecentPurchases,
        ]);
    }

    public function destroy(Request $request)
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return back()->with('error', 'Cliente não encontrado.');
        }

        try {
            $hasRecentPurchases = $this->service->hasRecentPurchases($client->id, 5);

            if ($hasRecentPurchases) {
                $this->repository->update($client, ['is_active' => false]);
                Auth::guard('client')->logout();

                return redirect()->route('store.index')
                    ->with('success', 'Seu cadastro foi inativado com sucesso.');
            } else {
                $this->repository->delete($client);
                Auth::guard('client')->logout();

                return redirect()->route('store.index')
                    ->with('success', 'Seu cadastro foi excluído com sucesso.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao processar solicitação: ' . $e->getMessage());
        }
    }

    private function prepareClientRegistrationData(array $data): array
    {
        $cleanDocument = preg_replace('/[^0-9]/', '', $data['document_number']);
        $firstName = $data['first_name'] ?? explode(' ', $data['name'] ?? 'Cliente')[0];
        $lastName = $data['last_name'] ?? substr(strstr(($data['name'] ?? 'Cliente'), ' ' ?: ' '), 1) ?: 'Cliente';
        $displayName = $data['display_name'] ?? $data['name'] ?? "{$firstName} {$lastName}";
        $email = $data['email'];
        return [
            'password' => Hash::make($data['password']),
            'first_name_hash' => hash('sha256', $firstName),
            'first_name_encrypted' => Crypt::encryptString($firstName),
            'last_name_hash' => hash('sha256', $lastName),
            'last_name_encrypted' => Crypt::encryptString($lastName),
            'display_name' => $displayName,
            'email_hash' => hash('sha256', $email),
            'email_encrypted' => Crypt::encryptString($email),
            'name' => $displayName,
            'email' => $email,
            'document_type' => strlen($cleanDocument) === 11 ? 'CPF' : 'CNPJ',
            'document_number' => $cleanDocument,
            'document_hash' => hash('sha256', $cleanDocument),
            'document_encrypted' => Crypt::encryptString($cleanDocument),
            'phone1' => $data['phone1'],
            'phone1_hash' => hash('sha256', $data['phone1']),
            'phone1_encrypted' => Crypt::encryptString($data['phone1']),
            'contact1' => $data['contact1'] ?? $displayName,
            'contributor_type' => $data['contributor_type'] ?? 9,
            'is_active' => false,
        ];
    }
}