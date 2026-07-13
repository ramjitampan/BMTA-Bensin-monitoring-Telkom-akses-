<?php

use App\Http\Controllers\Api\PerjalananApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('perjalanan')->group(function (): void {
    Route::get('/', [PerjalananApiController::class, 'index']);
    Route::get('/rekap', [PerjalananApiController::class, 'rekap']);
    Route::post('/', [PerjalananApiController::class, 'store']);
    Route::get('/{perjalanan}', [PerjalananApiController::class, 'show']);
});
