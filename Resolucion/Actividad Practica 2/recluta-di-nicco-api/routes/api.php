<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReclutaController;

Route::get('/reclutier', [ReclutaController::class, 'index']);   // JSON legible
Route::post('/recluta', [ReclutaController::class, 'store']);    // Recibe, valida y postea a Firebase
