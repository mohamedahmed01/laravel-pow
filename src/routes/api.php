<?php

use Illuminate\Support\Facades\Route;
use Mohamedahmed01\LaravelPow\Http\Controllers\PowController;

Route::prefix('api/pow')->group(function () {
    Route::get('/challenge', [PowController::class, 'getChallenge']);
    Route::post('/verify', [PowController::class, 'verify']);
});