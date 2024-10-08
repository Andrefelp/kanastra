<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoletoController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

// php artisan serve --host=0.0.0.0 --port=8001

Route::post('/importacao-boletos-csv',
    [BoletoController::class, 'importacaoBoletos']
)->middleware("auth:sanctum");
