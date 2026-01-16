<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CursoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'categoria' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'min:0'],
            'vagas_total' => ['required', 'integer', 'min:1'],
            'data_inicio_inscricoes' => ['required', 'date'],
            'data_fim_inscricoes' => ['required', 'date', 'after_or_equal:data_inicio_inscricoes'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            'ativo' => ['nullable', 'boolean'],
            'materiais' => ['nullable', 'array'],
            'materiais.*' => ['file', 'max:10240'], // 10MB por arquivo
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.max' => 'O campo nome não pode ter mais de 255 caracteres.',
            'valor.required' => 'O campo valor é obrigatório.',
            'valor.numeric' => 'O campo valor deve ser um número.',
            'valor.min' => 'O campo valor deve ser no mínimo 0.',
            'valor.max' => 'O campo valor não pode ser maior que 999.999,99.',
            'vagas_total.required' => 'O campo total de vagas é obrigatório.',
            'vagas_total.integer' => 'O campo total de vagas deve ser um número inteiro.',
            'vagas_total.min' => 'O campo total de vagas deve ser no mínimo 1.',
            'vagas_disponiveis.integer' => 'O campo vagas disponíveis deve ser um número inteiro.',
            'vagas_disponiveis.min' => 'O campo vagas disponíveis não pode ser negativo.',
            'data_inicio_inscricoes.required' => 'O campo data de início das inscrições é obrigatório.',
            'data_inicio_inscricoes.date' => 'O campo data de início das inscrições deve ser uma data válida.',
            'data_fim_inscricoes.required' => 'O campo data de fim das inscrições é obrigatório.',
            'data_fim_inscricoes.date' => 'O campo data de fim das inscrições deve ser uma data válida.',
            'data_fim_inscricoes.after_or_equal' => 'A data de fim das inscrições deve ser igual ou posterior à data de início.',
            'thumbnail.image' => 'O arquivo da thumbnail deve ser uma imagem.',
            'thumbnail.mimes' => 'A thumbnail deve ser nos formatos: jpg, jpeg, png ou gif.',
            'ativo.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
