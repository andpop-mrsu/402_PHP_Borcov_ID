<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/api/games', [GameController::class, 'index']);
Route::get('/api/games/{id}', [GameController::class, 'show']);
Route::post('/api/games', [GameController::class, 'store']);
Route::post('/api/step/{id}', [GameController::class, 'addStep']);
Route::get('/', function () {
    return view('calculator');
});