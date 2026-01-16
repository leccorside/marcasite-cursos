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
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Login do usuário
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        // Verificar se o usuário existe e está ativo
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // Verificar se o usuário está ativo
        if (!$user->ativo) {
            throw ValidationException::withMessages([
                'email' => ['Sua conta está desativada. Entre em contato com o administrador.'],
            ]);
        }

        try {
            // Gerar token JWT
            if (!$token = JWTAuth::fromUser($user)) {
                throw ValidationException::withMessages([
                    'email' => ['Não foi possível criar o token de autenticação.'],
                ]);
            }
        } catch (JWTException $e) {
            throw ValidationException::withMessages([
                'email' => ['Não foi possível criar o token de autenticação.'],
            ]);
        }

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'user' => $user->load('aluno'),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60, // em segundos
            'redirect' => $user->isAdmin() ? '/admin' : '/',
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

        // Gerar token JWT
        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Cadastro realizado, mas não foi possível gerar o token de autenticação.',
                'user' => $user->load('aluno'),
            ], 201);
        }

        return response()->json([
            'message' => 'Cadastro realizado com sucesso',
            'user' => $user->load('aluno'),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60, // em segundos
            'redirect' => '/',
        ], 201);
    }

    /**
     * Logout do usuário
     */
    public function logout(Request $request)
    {
        try {
            // Invalidar o token JWT
            JWTAuth::invalidate(JWTAuth::getToken());
            
            return response()->json([
                'message' => 'Logout realizado com sucesso',
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Erro ao fazer logout',
            ], 500);
        }
    }

    /**
     * Obter usuário autenticado
     */
    public function user(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json(['user' => null]);
            }

            return response()->json([
                'user' => $user->load('aluno'),
            ]);
        } catch (JWTException $e) {
            return response()->json(['user' => null]);
        }
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
