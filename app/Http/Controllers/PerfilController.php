<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    /**
     * Atualizar perfil do usuário logado
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Se for aluno, atualizar o nome no modelo Aluno também
        if ($user->aluno) {
            $user->aluno->update(['nome' => $validated['name']]);
        }

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'user' => $user->load('aluno'),
        ]);
    }
}
