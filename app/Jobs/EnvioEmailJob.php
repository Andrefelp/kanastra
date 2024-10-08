<?php

namespace App\Jobs;

use App\Services\JobFailureService;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Boleto;
use App\Services\EnvioEmailService;
use Illuminate\Support\Facades\Log;

class EnvioEmailJob implements ShouldQueue
{
    protected $boleto;

    public function __construct(Boleto $boleto)
    {
        $this->boleto = $boleto;
    }

    public function handle()
    {
        try {
            $envioEmailService = new EnvioEmailService($this->boleto);
            $resultado = $envioEmailService->envioEmail();
            Log::info($resultado);
        } catch (\Exception $e) {
            (new JobFailureService(__CLASS__, $e->getMessage(), $this->boleto->uuid, 'uuid'))->createJobFailure();
            Log::error('Erro ao enviar e-mail: ' . $e->getMessage());
        }
    }
}
