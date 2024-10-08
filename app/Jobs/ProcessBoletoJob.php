<?php

namespace App\Jobs;

use App\Services\BoletoService;
use App\Services\JobFailureService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\GeradorDeBoletoService;

class ProcessBoletoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $row;
    protected $clienteId;

    public function __construct(array $row, $clienteId)
    {
        $this->row = $row;
        $this->clienteId = $clienteId;
    }

    public function handle(BoletoService $boletoService)
    {
        try {
            $boleto = $boletoService->create([
                'uuid' => $this->row[5],
                'cliente_id' => $this->clienteId,
                'valor' => $this->row[3],
                'data_vencimento' => $this->row[4],
            ]);

            if($boleto) {
                $geradorDeBoletoService = new GeradorDeBoletoService($boleto);
                $geradorDeBoletoService->geraBoleto();

                dispatch(new EnvioEmailJob($boleto));
            }
        } catch (\Exception $e) {
            (new JobFailureService(__CLASS__, $e->getMessage(), $this->row[5] ?? $this->clienteId, $this->row[5] ? 'uuid' : 'cliente_id'))->createJobFailure();
            \Log::error('Erro ao processar boleto: ' . $e->getMessage());
        }
    }
}
