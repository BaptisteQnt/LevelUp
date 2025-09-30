<?php

use App\Http\Controllers\API\AuthTokenController;
use App\Http\Controllers\API\StatsController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::post('auth/token', [AuthTokenController::class, 'store'])->name('auth.token.store');

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('auth/token', [AuthTokenController::class, 'destroy'])->name('auth.token.destroy');

        Route::get('stats', [StatsController::class, 'index'])->name('stats');
        Route::get('games/{game}/rating', [StatsController::class, 'gameRating'])->name('games.rating');
    });
});
