<?php

use App\Services\DashboardService;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PerjalananController;

Route::get('/', function (DashboardService $dashboardService) {
    $data = $dashboardService->getDashboardData();
    return view('welcome', $data);
});

Route::resource('kendaraan', KendaraanController::class)->except(['show']);
Route::resource('pegawai', PegawaiController::class)->except(['show']);
Route::get('/perjalanan/export/excel', [PerjalananController::class, 'exportExcel'])
    ->name('perjalanan.export.excel');
Route::resource('perjalanan', PerjalananController::class);
Route::get('/deteksi-anomali', function () {
    return redirect()->route('perjalanan.index', ['filter' => 'anomali']);
})->name('anomali.index');
