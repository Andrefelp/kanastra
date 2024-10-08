<?php

namespace App\Services;

use App\Models\Boleto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BoletoService {

    public function findOrCreate(array $data): Boleto {
        $this->validate($data);

        $boleto = Boleto::byUuid($data['uuid'])->first();

        if(!$boleto) {
            $boleto = Boleto::create([
                'uuid'              => $data['uuid'],
                'cliente_id'        => $data['cliente_id'],
                'valor'             => $data['valor'],
                'data_vencimento'   => $data['data_vencimento'],
                'created_at'        => Carbon::now()
            ]);
        }

        return $boleto;
    }

    private function validate(array $data)
    {
        $validator = Validator::make($data, [
            'uuid' => 'required|uuid|unique:boletos,uuid',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date_format:Y-m-d',
        ],[
            'uuid.unique'                   => 'O campo UUID informado já está cadastrado.',
            'uuid.required'                 => 'O campo UUID é obrigatório.',
            'uuid.uuid'                     => 'O campo UUID deve ter um formato válido.',
            'valor.required'                => 'O campo valor é obrigatório.',
            'valor.numeric'                 => 'O campo valor deve ser numérico.',
            'valor.min'                     => 'O valor deve ser maior que 0.00.',
            'data_vencimento.required'      => 'O campo data de vencimento é obrigatório.',
            'data_vencimento.date_format'   => 'O formato da data de vencimento deve ser YYYY-MM-DD.',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
    }

}
