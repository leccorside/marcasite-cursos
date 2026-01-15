<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Models\User;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login do usuário
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'user' => $user->load('aluno'),
            'redirect' => $user->isAdmin() ? '/admin' : '/',
            'csrf_token' => csrf_token(),
        ]);
    }

    /**
     * Registro de novo aluno
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        // Criar usuário
        $user = User::create([
            'name' => $data['nome'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tipo' => 'aluno',
            'ativo' => true,
        ]);

        // Criar aluno associado
        $aluno = Aluno::create([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'cpf' => $data['cpf'],
            'user_id' => $user->id,
        ]);

        // Fazer login automaticamente
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Cadastro realizado com sucesso',
            'user' => $user->load('aluno'),
            'redirect' => '/',
            'csrf_token' => csrf_token(),
        ], 201);
    }

    /**
     * Logout do usuário
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout realizado com sucesso',
            'csrf_token' => csrf_token(),
        ]);
    }

    /**
     * Obter usuário autenticado
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['user' => null]);
        }

        return response()->json([
            'user' => $user->load('aluno'),
        ]);
    }

    /**
     * Enviar email de recuperação de senha
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Link de recuperação enviado para seu email.',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Não foi possível enviar o link de recuperação.'],
        ]);
    }

    /**
     * Resetar senha
     */
    public function resetPassword(PasswordResetRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Senha alterada com sucesso.',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Token inválido ou expirado.'],
        ]);
    }
}
