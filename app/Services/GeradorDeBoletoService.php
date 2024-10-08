<?php

namespace App\Services;

use App\Models\Boleto;
use Illuminate\Support\Facades\Log;

class GeradorDeBoletoService
{
    protected $boleto;

    public function __construct(Boleto $boleto)
    {
        $this->boleto = $boleto;
    }
    public function geraBoleto()
    {
        Log::info("Boleto gerado para o cliente {$this->boleto->cliente->nome}, valor: {$this->boleto->valor_formatado}, data de vencimento: {$this->boleto->data_vencimento_formatada} (uuid: {$this->boleto->uuid})");

        return true;
    }
}
