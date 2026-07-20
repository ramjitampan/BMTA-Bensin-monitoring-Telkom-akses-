<?php

use App\Services\DashboardService;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PerjalananController;
use App\Http\Controllers\Auth\LoginController;

// ── Auth ──
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ── Public ──
Route::get('/', function (DashboardService $dashboardService) {
    $data = $dashboardService->getDashboardData();
    return view('welcome', $data);
});

// ── Protected (perlu login) ──
Route::middleware('auth')->group(function () {
    Route::resource('kendaraan', KendaraanController::class)->except(['show']);
    Route::resource('pegawai', PegawaiController::class)->except(['show']);
    Route::get('/perjalanan/export/excel', [PerjalananController::class, 'exportExcel'])
        ->name('perjalanan.export.excel');
    Route::resource('perjalanan', PerjalananController::class)->except(['show']);
    Route::get('/deteksi-anomali', function () {
        return redirect()->route('perjalanan.index', ['filter' => 'anomali']);
    })->name('anomali.index');
});
