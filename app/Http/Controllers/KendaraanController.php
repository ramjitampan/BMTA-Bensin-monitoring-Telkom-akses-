<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKendaraanRequest;
use App\Http\Requests\UpdateKendaraanRequest;
use App\Models\Kendaraan;
use App\Services\DashboardService;

class KendaraanController extends Controller
{
    public function index()
    {
        $kendaraans = Kendaraan::paginate(15);
        return view('kendaraan.index', compact('kendaraans'));
    }

    public function create()
    {
        return view('kendaraan.create');
    }

    public function store(StoreKendaraanRequest $request)
    {
        Kendaraan::create([
            'plat_nomor' => strtoupper($request->plat_nomor),
            'merk'       => $request->merk,
            'jenis'      => $request->jenis,
            'tahun'      => $request->tahun,
        ]);

        app(DashboardService::class)->forgetCache();
        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Kendaraan $kendaraan)
    {
        return view('kendaraan.edit', compact('kendaraan'));
    }

    public function update(UpdateKendaraanRequest $request, Kendaraan $kendaraan)
    {
        $kendaraan->update([
            'plat_nomor' => strtoupper($request->plat_nomor),
            'merk'       => $request->merk,
            'jenis'      => $request->jenis,
            'tahun'      => $request->tahun,
        ]);

        app(DashboardService::class)->forgetCache();
        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $kendaraan->delete();
        app(DashboardService::class)->forgetCache();
        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil dihapus.');
    }
}
