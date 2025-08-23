<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReclutaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/reclutados', [ReclutaController::class, 'human']);  // Vista HTML
