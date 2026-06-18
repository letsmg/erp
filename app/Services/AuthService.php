<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecuperarSenhaMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthService
{
    private function validateGeographicAccess(): void
    {
        $ip = request()->ip();

        if ($ip === '127.0.0.1' || $ip === '::1') {
            return;
        }

        try {
            $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}?fields=status,countryCode,message");

            if ($response->successful() && $response->json('status') === 'success') {
                $countryCode = $response->json('countryCode');

                if ($countryCode !== 'BR') {
                    logger()->warning("Tentativa de login bloqueada: IP estrangeiro detectado", [
                        'ip' => $ip,
                        'pais' => $countryCode,
                        'email' => request('email'),
                    ]);

                    throw ValidationException::withMessages([
                        'email' => ['Acesso negado: Este sistema não aceita logins fora do Brasil.'],
                    ]);
                }
            }
        } catch (\Exception $e) {
            logger()->error("Falha no serviço de verificação de IP: " . $e->getMessage());
        }
    }

    /**
     * Login via email_hash (converte email para hash e busca)
     */
    public function login(array $credentials, $remember = false, bool $isClientAuth = false)
    {
        $this->validateGeographicAccess();

        $emailHash = hash('sha256', $credentials['email']);
        $user = User::where('email_hash', $emailHash)->first();

        if (!$user) {
            return false;
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return false;
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Sua conta está bloqueada. Entre em contato com a administração.'],
            ]);
        }

        if ($isClientAuth) {
            if (!$user->isClient()) {
                return false;
            }
        } else {
            if ($user->isClient()) {
                return false;
            }
        }

        Auth::login($user, $remember);

        $user->update(['last_login_ip' => request()->ip()]);

        return true;
    }

    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Envia link de reset para o email (converte para hash e busca)
     */
    public function sendResetLink(string $email): bool
    {
        $emailHash = hash('sha256', $email);
        $user = User::where('email_hash', $emailHash)->first();

        if (!$user) {
            return false;
        }

        $decryptedEmail = $user->decrypted_email;

        if (!$decryptedEmail) {
            return false;
        }

        $url = route('login');
        Mail::to($decryptedEmail)->send(new RecuperarSenhaMail($url));

        return true;
    }
}