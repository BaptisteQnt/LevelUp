<?php

use App\Http\Controllers\API\StatsController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware(['api', 'auth'])
    ->name('api.')
    ->group(function () {
        Route::get('stats', [StatsController::class, 'index'])->name('stats');
        Route::get('games/{game}/rating', [StatsController::class, 'gameRating'])->name('games.rating');
    });
