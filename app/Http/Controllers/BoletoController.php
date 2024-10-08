<?php

namespace App\Http\Controllers;
use App\Http\Requests\ImportacaoBoletosRequest;
use App\Models\Boleto;
use App\Repositories\ClienteRepository;
use App\Services\BoletoService;
use App\Services\ClienteService;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\Cliente;

class BoletoController extends Controller
{
    protected $clienteService;

    public function __construct(ClienteService $clienteService, BoletoService $boletoService)
    {
        $this->clienteService = $clienteService;
        $this->boletoService = $boletoService;
    }

    public function importacaoBoletos(ImportacaoBoletosRequest $request): JsonResponse
    {
        try{
            $file = $request->file('file');

            $handle = fopen($file->getRealPath(), 'r');

            // pula o header e analise se a próxima linha é nula
            fgetcsv($handle);
            if (fgetcsv($handle) == null) {
                fclose($handle);
                return response()->json(['message' => 'O arquivo só possui uma linha.'], 422);
            }

            // caso não seja nula, reinicia o ponteiro e segue o processo
            rewind($handle);
            fgetcsv($handle);

            // 0 => nome            => name
            // 1 => documento       => governmentId
            // 2 => email           => email
            // 3 => valor           => debtAmount
            // 4 => data_vencimento => debtDueDate
            // 5 => uuid            => debtID
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $cliente = $this->clienteService->findOrCreate(
                    [
                        'nome'      => $row[0],
                        'documento' => $row[1],
                        'email'     => $row[2]
                    ]);

                $boleto = $this->boletoService->findOrCreate(
                    [
                        'uuid'              => $row[5],
                        'cliente_id'        => $cliente->id,
                        'valor'             => $row[3],
                        'data_vencimento'   => $row[4],
                    ]);
            }

            fclose($handle);

            return response()->json(
                ['message' => 'Sucesso!']
            );
        } catch (\InvalidArgumentException | \Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
