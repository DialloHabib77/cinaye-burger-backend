<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BurgerController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\CommandeController;
use App\Http\Controllers\API\PaiementController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('burgers', BurgerController::class);
Route::apiResource('clients', ClientController::class);
Route::apiResource('commandes', CommandeController::class);
Route::apiResource('paiements', PaiementController::class);
Route::get('commandes/filtrer', [CommandeController::class, 'filtrer']);
Route::get('commandes/statistiques', [CommandeController::class, 'statistiques']);
Route::get('clients/{client}/commandes', [ClientController::class, 'commandes']);