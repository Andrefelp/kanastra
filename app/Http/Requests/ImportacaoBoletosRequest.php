<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportacaoBoletosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|mimes:csv',
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'É obrigatório inserir um arquivo.',
            'file.mimes' => 'O arquivo deve ser do formato CSV.',
        ];
    }
}
