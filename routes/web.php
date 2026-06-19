<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PerjalananController;

Route::resource('kendaraan', KendaraanController::class);
Route::resource('pegawai', PegawaiController::class);
Route::resource('perjalanan', PerjalananController::class);