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
        $this->validateInicial($data);

        $cliente = Cliente::byDocumento($data['documento'])->first();
        $email = $this->validateEmail($data['email']);

        if($cliente && $email && $cliente->email != $email ){
            $cliente->email = $email;
        } elseif(!$cliente) {
            $this->validateFinal($data);

            $cliente = Cliente::create([
                'nome'      => $data['nome'],
                'documento' => $data['documento'],
                'email'     => $email,
            ]);
        }

        return $cliente;
    }

    public function validateInicial(array $data): void
    {
        $validator = Validator::make($data, [
            'documento' => ['required', new DocumentoValido()],
        ], [
            'documento.required' => 'O campo documento é obrigatório.',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
    }

    public function validateFinal(array $data): void
    {
        $validator = Validator::make($data, [
            'nome'      => 'required|string|max:255',
        ], [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O campo deve ser do tipo String.',
            'nome.max' => 'O campo deve ter no máximo 255 caracteres.',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
    }

    public function validateEmail(string $email)
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ],[
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O campo deve ser um e-mail válido.',
        ]);

        if ($validator->passes()) {
            return $email;
        }

        return null;
    }


}
