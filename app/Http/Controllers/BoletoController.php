<?php

namespace App\Http\Controllers;
use App\Http\Requests\ImportacaoBoletosRequest;
use \Illuminate\Http\JsonResponse;

class BoletoController extends Controller
{
    public function importacaoBoletos(ImportacaoBoletosRequest $request): JsonResponse
    {
        return response()->json(
            ['message' => 'Sucesso!']
        );
    }
}
