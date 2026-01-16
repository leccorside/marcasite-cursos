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
            'password' => ['nullable', 'confirmed', 'string', 'min:6'],
            'foto_perfil' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'password.confirmed' => 'As senhas não conferem.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'foto_perfil.image' => 'O arquivo deve ser uma imagem.',
            'foto_perfil.max' => 'A imagem não pode ser maior que 2MB.',
        ]);

        $user->name = $validated['name'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('foto_perfil')) {
            // Remover foto antiga
            if ($user->foto_perfil) {
                \Storage::disk('public')->delete($user->foto_perfil);
            }
            $user->foto_perfil = $request->file('foto_perfil')->store('usuarios/fotos', 'public');
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
