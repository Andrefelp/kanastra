<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportacaoBoletosRequest;
use App\Services\BoletoService;
use App\Services\ClienteService;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessClientesJob;

class BoletoController extends Controller
{
    protected $clienteService;
    protected $boletoService;

    public function __construct(ClienteService $clienteService, BoletoService $boletoService)
    {
        $this->clienteService = $clienteService;
        $this->boletoService = $boletoService;
    }

    public function importacaoBoletos(ImportacaoBoletosRequest $request): JsonResponse
    {
        try {
            $file = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');

            $batchSize = 1000;
            $batch = [];

            // pula o header
            fgetcsv($handle);

            // processa o arquivo em batches de 1000 linhas
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $batch[] = $row;

                if (count($batch) === $batchSize) {
                    ProcessClientesJob::dispatch($batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                ProcessClientesJob::dispatch($batch);
            }

            fclose($handle);

            return response()->json(['message' => 'Processamento enfileirado com sucesso!'], 200);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
