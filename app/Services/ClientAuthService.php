<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecuperarSenhaMail;
use App\Mail\RecuperarLoginMail;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClientAuthService
{
    /**
     * Login do cliente via email_hash
     */
    public function login(array $credentials, $remember = false)
    {
        $emailHash = hash('sha256', $credentials['email']);
        $client = Client::where('email_hash', $emailHash)->first();

        if (!$client) {
            return false;
        }

        if (!Hash::check($credentials['password'], $client->password)) {
            return false;
        }

        if (!$client->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Sua conta está bloqueada. Entre em contato com a administração.'],
            ]);
        }

        Auth::guard('client')->login($client, $remember);

        return true;
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function sendResetLink(string $email): bool
    {
        $emailHash = hash('sha256', $email);
        $client = Client::where('email_hash', $emailHash)->first();

        if (!$client) {
            return false;
        }

        $decryptedEmail = $client->decrypted_email;

        if (!$decryptedEmail) {
            return false;
        }

        $url = route('client.login');
        Mail::to($decryptedEmail)->send(new RecuperarSenhaMail($url));

        return true;
    }

    public function recoverUsername(string $email): ?string
    {
        $emailHash = hash('sha256', $email);
        $client = Client::where('email_hash', $emailHash)->first();

        if (!$client) {
            return null;
        }

        $decryptedEmail = $client->decrypted_email;

        if (!$decryptedEmail) {
            return null;
        }

        $url = route('client.login');
        Mail::to($decryptedEmail)->send(new RecuperarLoginMail($client->decrypted_email ?? 'cliente', $url));

        return $client->decrypted_email;
    }
}