<?php

use Illuminate\Support\Facades\Route;
use App\Models\Pegawai;
use App\Models\Kendaraan;
use App\Models\Perjalanan;

use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PerjalananController;
use App\Http\Controllers\AIController;

Route::get('/', function () {

    $totalPegawai = Pegawai::count();

    $totalKendaraan = Kendaraan::count();

    $totalPerjalanan = Perjalanan::count();

    $totalBBM = Perjalanan::sum('jumlah_biaya');

    $totalLiter = Perjalanan::sum('vol_liter');

    $rataEfisiensi = round(
        Perjalanan::avg('efisiensi') ?? 0,
        2
    );

    return view('welcome', compact(
        'totalPegawai',
        'totalKendaraan',
        'totalPerjalanan',
        'totalBBM',
        'totalLiter',
        'rataEfisiensi'
    ));
});

Route::resource('kendaraan', KendaraanController::class);
Route::resource('pegawai', PegawaiController::class);
Route::get('/perjalanan/export/excel', [PerjalananController::class, 'exportExcel'])
    ->name('perjalanan.export.excel');
Route::resource('perjalanan', PerjalananController::class);
Route::get('/ai', [AIController::class, 'index'])->name('ai.dashboard');
