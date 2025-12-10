<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('api_token')->group(function () {

    Route::prefix('game')->group(function () {
        //create game
        Route::post('/create', [GameController::class, 'create']);

        //join game
        Route::post('/join', [GameController::class, 'join']);

        //state
        Route::get('/{game}/state', [GameController::class, 'state']);

        //play
        Route::post('/{game}/play', [GameController::class, 'play']);

        //result
        Route::get('/game/{game}/result', [GameController::class, 'result']);

    });
});
