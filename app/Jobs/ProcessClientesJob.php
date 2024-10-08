<?php

namespace App\Jobs;

use App\Services\ClienteService;
use App\Services\JobFailureService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessClientesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $batch;

    public function __construct(array $batch)
    {
        $this->batch = $batch;
    }

    public function handle(ClienteService $clienteService)
    {
        foreach ($this->batch as $index => $row) {
            try {
                $cliente = $clienteService->findOrCreate([
                    'nome'      => $row[0],
                    'documento' => $row[1],
                    'email'     => $row[2],
                ]);

                ProcessBoletoJob::dispatch($row, $cliente->id);
            } catch (\Exception $e) {
                $docIdentificador = $row[1] . $row[0] . $index+1;
                $identificadorType = 'documento - nome - linha csv';

                (new JobFailureService(__CLASS__, $e->getMessage(), $docIdentificador, $identificadorType))->createJobFailure();
                Log::error('Erro ao processar cliente: ' . $e->getMessage());
            }
        }
    }
}
