<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransportadoraController;
use App\Http\Controllers\ClienteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas para API Transportadoras
// Route::get('/transportadoras', [TransportadoraController::class, 'index']);
// Route::get('/transportadoras/{id}', [TransportadoraController::class, 'show']);
// Route::post('/transportadoras', [TransportadoraController::class, 'store']);
// Route::put('/transportadoras/{id}', [TransportadoraController::class, 'update']);
// Route::delete('/transportadoras/{id}', [TransportadoraController::class, 'destroy']);

// Rotas para API Clientes
Route::get('/clientes/{cpfcnpj}', [ClienteController::class, 'findByDocument']);