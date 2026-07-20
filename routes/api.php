<?php

use App\Http\Controllers\Api\KendaraanApiController;
use App\Http\Controllers\Api\PegawaiApiController;
use App\Http\Controllers\Api\PerjalananApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('perjalanan')->group(function (): void {
    Route::get('/', [PerjalananApiController::class, 'index']);
    Route::get('/rekap', [PerjalananApiController::class, 'rekap']);
    Route::post('/', [PerjalananApiController::class, 'store']);
    Route::get('/{perjalanan}', [PerjalananApiController::class, 'show']);
});

Route::prefix('pegawai')->group(function (): void {
    Route::get('/', [PegawaiApiController::class, 'index']);
    Route::get('/{pegawai}', [PegawaiApiController::class, 'show']);
});

Route::prefix('kendaraan')->group(function (): void {
    Route::get('/', [KendaraanApiController::class, 'index']);
    Route::get('/{kendaraan}', [KendaraanApiController::class, 'show']);
});
