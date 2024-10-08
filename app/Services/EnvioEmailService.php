<?php

namespace App\Services;

use App\Models\Boleto;
use Carbon\Carbon;

class EnvioEmailService
{
    protected $boleto;
    protected $clienteService;

    public function __construct(Boleto $boleto)
    {
        $this->boleto = $boleto;
        $this->clienteService = new ClienteService();
    }
    public function envioEmail()
    {
        $email = $this->clienteService->validateEmail($this->boleto->cliente->email);

        if(!$email){
            throw new \Exception('E-mail inválido!');
        }

        $this->boleto->data_envio_email = Carbon::now();
        $this->boleto->save();

        return 'E-mail enviado com sucesso para o cliente ' . $this->boleto->cliente->nome . ' (uuid=' . $this->boleto->uuid . ')!';
    }
}
