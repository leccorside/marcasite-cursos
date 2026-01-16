<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Listar usuários com paginação
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Busca por nome ou email
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por tipo
        if ($request->has('tipo') && $request->tipo) {
            $query->where('tipo', $request->tipo);
        }

        // Ordenação
        $query->orderBy('name', 'asc');

        // Paginação
        $perPage = $request->get('per_page', 9);
        $users = $query->paginate($perPage);

        return response()->json([
            'data' => $users->items(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'per_page' => $users->perPage(),
            'total' => $users->total(),
        ]);
    }

    /**
     * Criar um novo usuário
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'tipo' => 'required|in:admin,aluno',
            'ativo' => 'boolean',
            'foto_perfil' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'tipo.required' => 'O tipo de usuário é obrigatório.',
            'foto_perfil.image' => 'O arquivo deve ser uma imagem.',
            'foto_perfil.max' => 'A imagem não pode ser maior que 2MB.',
        ]);

        $data = $validated;
        $data['password'] = Hash::make($validated['password']);
        $data['ativo'] = $request->boolean('ativo', true);

        if ($request->hasFile('foto_perfil')) {
            $data['foto_perfil'] = $request->file('foto_perfil')->store('usuarios/fotos', 'public');
        }

        $user = User::create($data);

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'data' => $user
        ], 201);
    }

    /**
     * Atualizar um usuário
     */
    public function update(Request $request, User $usuario): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'tipo' => 'required|in:admin,aluno',
            'ativo' => 'boolean',
            'foto_perfil' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.min' => 'A nova senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'A confirmação da nova senha não confere.',
            'tipo.required' => 'O tipo de usuário é obrigatório.',
            'foto_perfil.image' => 'O arquivo deve ser uma imagem.',
            'foto_perfil.max' => 'A imagem não pode ser maior que 2MB.',
        ]);

        $data = $validated;
        
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        } else {
            unset($data['password']);
        }

        $data['ativo'] = $request->boolean('ativo');

        if ($request->hasFile('foto_perfil')) {
            // Remover foto antiga
            if ($usuario->foto_perfil) {
                \Storage::disk('public')->delete($usuario->foto_perfil);
            }
            $data['foto_perfil'] = $request->file('foto_perfil')->store('usuarios/fotos', 'public');
        }

        $usuario->update($data);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'data' => $usuario
        ]);
    }

    /**
     * Excluir um usuário (soft delete)
     */
    public function destroy(User $usuario): JsonResponse
    {
        // Não permitir que o usuário logado se delete
        if ($usuario->id === auth()->id()) {
            return response()->json([
                'message' => 'Você não pode excluir sua própria conta.',
            ], 422);
        }

        // Se for aluno, verificar se tem inscrições ativas
        if ($usuario->aluno) {
            $inscricoesAtivas = $usuario->aluno->inscricoes()
                ->whereIn('status', ['pendente', 'pago'])
                ->count();

            if ($inscricoesAtivas > 0) {
                return response()->json([
                    'message' => 'Não é possível excluir o usuário. Existem inscrições ativas associadas a este aluno.',
                ], 422);
            }
            
            $usuario->aluno->delete();
        }

        // Remover foto de perfil se existir
        if ($usuario->foto_perfil) {
            \Storage::disk('public')->delete($usuario->foto_perfil);
        }

        $usuario->delete();

        return response()->json([
            'message' => 'Usuário excluído com sucesso',
        ]);
    }
}
