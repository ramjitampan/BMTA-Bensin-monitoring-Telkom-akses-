<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    public function index()
    {
        $kendaraans = Kendaraan::all();
        return view('kendaraan.index', compact('kendaraans'));
    }

    public function create()
    {
        return view('kendaraan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required|string|unique:kendaraans,plat_nomor',
            'merk'       => 'required|string|max:255',
            'jenis'      => 'required|in:R2,R4',
            'tahun'      => 'required|digits:4|integer|min:1900|max:' . date('Y'),
        ]);

        Kendaraan::create([
            'plat_nomor' => strtoupper($request->plat_nomor),
            'merk'       => $request->merk,
            'jenis'      => $request->jenis,
            'tahun'      => $request->tahun,
        ]);

        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function show(Kendaraan $kendaraan)
    {
        return view('kendaraan.show', compact('kendaraan'));
    }

    public function edit(Kendaraan $kendaraan)
    {
        return view('kendaraan.edit', compact('kendaraan'));
    }

    public function update(Request $request, Kendaraan $kendaraan)
    {
        $request->validate([
            'plat_nomor' => 'required|string|unique:kendaraans,plat_nomor,' . $kendaraan->id,
            'merk'       => 'required|string|max:255',
            'jenis'      => 'required|in:R2,R4',
            'tahun'      => 'required|digits:4|integer|min:1900|max:' . date('Y'),
        ]);

        $kendaraan->update([
            'plat_nomor' => strtoupper($request->plat_nomor),
            'merk'       => $request->merk,
            'jenis'      => $request->jenis,
            'tahun'      => $request->tahun,
        ]);

        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $kendaraan->delete();
        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil dihapus.');
    }
}