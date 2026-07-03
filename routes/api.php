<?php

use App\Http\Controllers\Api\PerjalananApiController;
use App\Http\Controllers\Api\V1\AIAnalysisController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\KendaraanController;
use App\Http\Controllers\Api\V1\PegawaiController;
use App\Http\Controllers\Api\V1\PerjalananController;
use Illuminate\Support\Facades\Route;

Route::prefix('perjalanan')->group(function (): void {
    Route::get('/', [PerjalananApiController::class, 'index']);
    Route::get('/rekap', [PerjalananApiController::class, 'rekap']);
    Route::post('/', [PerjalananApiController::class, 'store']);
    Route::get('/{perjalanan}', [PerjalananApiController::class, 'show']);
});

Route::prefix('v1')->group(function (): void {
    Route::get('/kendaraan', [KendaraanController::class, 'index']);
    Route::get('/kendaraan/{kendaraan}', [KendaraanController::class, 'show']);

    Route::get('/pegawai', [PegawaiController::class, 'index']);
    Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show']);

    Route::get('/perjalanan', [PerjalananController::class, 'index']);
    Route::get('/perjalanan/{perjalanan}', [PerjalananController::class, 'show']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/ai-analysis', [AIAnalysisController::class, 'index']);
});
