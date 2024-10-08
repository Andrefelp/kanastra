<?php

namespace App\Services;

use App\Models\Cliente;
use App\Rules\DocumentoValido;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClienteService
{
    public function findOrCreate(array $data): Cliente
    {
        $this->validate($data);

        $cliente = Cliente::byDocumento($data['documento'])->first();

        if (!$cliente) {
            $cliente = Cliente::create([
                'nome'      => $data['nome'],
                'documento' => $data['documento'],
                'email'     => $data['email'],
            ]);
        }

        return $cliente;
    }

    private function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'nome'      => 'required|string|max:255',
            'documento' => ['required', new DocumentoValido()],
            'email'     => 'required|email',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
    }
}
