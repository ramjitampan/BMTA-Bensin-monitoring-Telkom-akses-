<?php

use Illuminate\Support\Facades\Route;
use App\Models\Pegawai;
use App\Models\Kendaraan;
use App\Models\Perjalanan;

use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PerjalananController;


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

Route::resource('kendaraan', KendaraanController::class)->except(['show']);
Route::resource('pegawai', PegawaiController::class)->except(['show']);
Route::get('/perjalanan/export/excel', [PerjalananController::class, 'exportExcel'])
    ->name('perjalanan.export.excel');
Route::resource('perjalanan', PerjalananController::class);
Route::get('/deteksi-anomali', function () {
    return redirect()->route('perjalanan.index', ['filter' => 'anomali']);
})->name('anomali.index');
