<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use App\Models\Pegawai;
use App\Services\DashboardService;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawais = Pegawai::paginate(15);
        return view('pegawai.index', compact('pegawais'));
    }

    public function create()
    {
        return view('pegawai.create');
    }

    public function store(StorePegawaiRequest $request)
    {
        Pegawai::create([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'divisi' => $request->divisi,
            'no_hp' => $request->no_hp,
        ]);

        app(DashboardService::class)->forgetCache();

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(Pegawai $pegawai)
    {
        return view('pegawai.edit', compact('pegawai'));
    }

    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai)
    {
        $pegawai->update([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'divisi' => $request->divisi,
            'no_hp' => $request->no_hp,
        ]);

        app(DashboardService::class)->forgetCache();

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();
        app(DashboardService::class)->forgetCache();
        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
    }
}
