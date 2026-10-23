<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmpruntController;
use App\Http\Controllers\Api\LivreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route par défaut Sanctum
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);
Route::get('/livres', [LivreController::class, 'index']);

// Routes sécurisées (Nécessite d'être authentifié via Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Gestion des emprunts
    Route::get('/emprunts', [EmpruntController::class, 'index']);
    Route::post('/emprunts', [EmpruntController::class, 'store']);
    Route::patch('/emprunts/{id}/signaler-retour', [EmpruntController::class, 'signalerRetour']);

    // Actions Bibliothécaire
    Route::post('/livres', [LivreController::class, 'store']);
    Route::delete('/livres/{id}', [LivreController::class, 'destroy']);

    Route::patch('/emprunts/{id}/valider', [EmpruntController::class, 'validerPrêt']);
    Route::patch('/emprunts/{id}/refuser', [EmpruntController::class, 'refuserPrêt']);
    Route::patch('/emprunts/{id}/valider-retour', [EmpruntController::class, 'validerRetour']);
});
