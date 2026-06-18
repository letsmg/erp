<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\JwtService;
use Inertia\Inertia;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    protected AuthService $service;
    protected JwtService $jwtService;

    public function __construct(AuthService $service, JwtService $jwtService)
    {
        $this->service = $service;
        $this->jwtService = $jwtService;
    }

    public function showLogin()
    {
        if (auth()->check()) {
            if (auth()->user()->isStaff()) {
                return redirect()->intended('/dashboard');
            }
            if (auth()->user()->isClient()) {
                return redirect()->route('store.index');
            }
        }

        return Inertia::render('Auth/Login', [
            'userIp' => request()->ip(),
            'status' => session('status'),
        ]);
    }

    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if ($this->service->login($credentials, $request->boolean('remember'))) {
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas ou conta bloqueada.',
        ]);
    }

    public function apiLogin(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth()->attempt($credentials)) {
            $user = auth()->user();

            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conta inativa.',
                ], 401);
            }

            $token = $this->jwtService->generateToken($user);

            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso.',
                'data' => [
                    'token' => $token,
                    'user' => $user,
                ],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Credenciais inválidas.',
        ], 401);
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->service->logout($request);
        return redirect('/');
    }

    public function apiLogout(Request $request): JsonResponse
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso.',
        ], 200);
    }

    /**
     * Mostra formulário de esqueci senha (recupera por username)
     */
    public function showForgotPassword()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    /**
     * Envia link de reset para o email informado
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $result = $this->service->sendResetLink($data['email']);

            if (!$result) {
                return back()->withErrors([
                    'email' => 'E-mail não encontrado em nossa base.',
                ]);
            }

            return back()->with('success', 'Link de recuperação enviado para o e-mail cadastrado!');
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Erro no provedor de e-mail: ' . $e->getMessage()
            ]);
        }
    }
}
