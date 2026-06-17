<?php

use App\Http\Controllers\PartidoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PartidoController::class, 'index'])->name('home');
Route::get('/api/refresh', [PartidoController::class, 'refresh'])->name('refresh');
