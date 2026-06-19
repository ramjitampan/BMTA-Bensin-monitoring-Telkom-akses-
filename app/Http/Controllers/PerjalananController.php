<?php

namespace App\Http\Controllers;

use App\Models\Perjalanan;
use App\Models\Pegawai;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerjalananController extends Controller
{
    public function index()
    {
        $perjalanans = Perjalanan::with('pegawai', 'kendaraan')
            ->orderBy('kendaraan_id')
            ->orderBy('tanggal')
            ->get();

        // Rekap per pegawai
        $rekapPegawai = Perjalanan::with('pegawai')
            ->get()
            ->groupBy('pegawai_id')
            ->map(function ($data) {
                $avg = $data->avg('efisiensi');
                return [
                    'nama'             => $data->first()->pegawai->nama ?? '-',
                    'total_perjalanan' => $data->count(),
                    'total_anomali'     => $data->where('status_efisiensi', 'anomali')->count(),
                    'total_jarak'      => $data->sum('jarak'),
                    'total_biaya'      => $data->sum('jumlah_biaya'),
                    'avg_efisiensi'    => $avg,
                    'status'           => Perjalanan::tentukanStatus($avg),
                ];
            })
            ->sortBy('avg_efisiensi');

        return view('perjalanan.index', compact('perjalanans', 'rekapPegawai'));
    }

    public function create()
    {
        $pegawais   = Pegawai::all();
        $kendaraans = Kendaraan::all();

        $kmTerakhir = [];
        foreach ($kendaraans as $kendaraan) {
            $kmTerakhir[$kendaraan->id] = Perjalanan::where('kendaraan_id', $kendaraan->id)
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->value('km_baru');
        }

        return view('perjalanan.create', compact('pegawais', 'kendaraans', 'kmTerakhir'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id'      => 'required|exists:pegawais,id',
            'kendaraan_id'    => 'required|exists:kendaraans,id',
            'tanggal'         => 'required|date',
            'tujuan'          => 'required|string|max:255',
            'uraian'          => 'nullable|string|max:255',
            'km_lama'         => 'required|numeric|min:0',
            'km_baru'         => 'required|numeric|gt:km_lama',
            'jumlah_biaya'    => 'required|numeric|min:1000',
            'harga_per_liter' => 'required|numeric|min:1',
            'no_bon'          => 'nullable|string|max:100',
            'foto_bon'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Validasi nominal bon: harus kelipatan 1.000, bukan kelipatan bulat 10.000
        if (!Perjalanan::isNominalGanjil($validated['jumlah_biaya'])) {
            return back()
                ->withErrors(['jumlah_biaya' => 'Nominal bon tidak boleh kelipatan Rp10.000 bulat. Contoh valid: Rp51.000, Rp52.000, Rp127.000.'])
                ->withInput();
        }

        if (!empty($validated['no_bon']) && Perjalanan::isDuplicateBon($validated['no_bon'], $validated['kendaraan_id'])) {
            return back()
                ->withErrors(['no_bon' => 'Nomor bon ini sudah pernah diinput untuk kendaraan ini. Kemungkinan bon duplikat.'])
                ->withInput();
        }

        $odometerTerakhir = Perjalanan::getOdometerTerakhir($validated['kendaraan_id']);
        if ($odometerTerakhir !== null && $validated['km_lama'] < $odometerTerakhir) {
            return back()
                ->withErrors([
                    'km_lama' => "KM awal ({$validated['km_lama']}) lebih kecil dari odometer terakhir kendaraan ini ({$odometerTerakhir} km). Odometer tidak bisa mundur.",
                ])
                ->withInput();
        }

        $jarak     = $validated['km_baru'] - $validated['km_lama'];
        $volLiter  = round($validated['jumlah_biaya'] / $validated['harga_per_liter'], 2);
        $efisiensi = $volLiter > 0 ? round($jarak / $volLiter, 2) : 0;

        $kendaraan = Kendaraan::find($validated['kendaraan_id']);
        $tipe      = $kendaraan->tipe ?? 'R4';
        $status    = Perjalanan::tentukanStatus($efisiensi, $tipe);

        $fraudResult = Perjalanan::hitungFraudScore([
            ...$validated,
            'jarak'     => $jarak,
            'efisiensi' => $efisiensi,
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_bon')) {
            $fotoPath = $request->file('foto_bon')->store('foto_bon', 'public');
        }

        Perjalanan::create([
            ...$validated,
            'jarak'            => $jarak,
            'vol_liter'        => $volLiter,
            'foto_bon'         => $fotoPath,
            'efisiensi'        => $efisiensi,
            'status_efisiensi' => $status,
            'fraud_score'      => $fraudResult['score'],
            'fraud_flags'      => $fraudResult['flags'],
        ]);

        $pesan = match ($fraudResult['risk']) {
            'aman'         => 'Data perjalanan berhasil disimpan.',
            'perhatian'    => 'Data disimpan. Terdapat catatan minor: ' . implode(', ', $fraudResult['flags']),
            'mencurigakan' => 'Data disimpan namun terdeteksi ' . count($fraudResult['flags']) . ' indikasi anomali. Harap review.',
            'tinggi'       => 'Data disimpan. PERINGATAN: Skor kecurangan tinggi (' . $fraudResult['score'] . '). Segera laporkan ke atasan.',
            default        => 'Data disimpan.',
        };

        $flashType = in_array($fraudResult['risk'], ['mencurigakan', 'tinggi']) ? 'warning' : 'success';

        return redirect()->route('perjalanan.index')->with($flashType, $pesan);
    }

    public function edit(Perjalanan $perjalanan)
    {
        $pegawais   = Pegawai::all();
        $kendaraans = Kendaraan::all();
        return view('perjalanan.edit', compact('perjalanan', 'pegawais', 'kendaraans'));
    }

    public function update(Request $request, Perjalanan $perjalanan)
    {
        $validated = $request->validate([
            'pegawai_id'      => 'required|exists:pegawais,id',
            'kendaraan_id'    => 'required|exists:kendaraans,id',
            'tanggal'         => 'required|date',
            'tujuan'          => 'required|string|max:255',
            'uraian'          => 'nullable|string|max:255',
            'km_lama'         => 'required|numeric|min:0',
            'km_baru'         => 'required|numeric|gt:km_lama',
            'jumlah_biaya'    => 'required|numeric|min:1',
            'harga_per_liter' => 'required|numeric|min:1',
            'no_bon'          => 'nullable|string|max:100',
            'foto_bon'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = $perjalanan->foto_bon;
        if ($request->hasFile('foto_bon')) {
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto_bon')->store('foto_bon', 'public');
        }

        $perjalanan->update([
            ...$validated,
            'foto_bon' => $fotoPath,
        ]);

        return redirect()->route('perjalanan.index')
            ->with('success', 'Data perjalanan berhasil diupdate.');
    }

    public function destroy(Perjalanan $perjalanan)
    {
        if ($perjalanan->foto_bon) {
            Storage::disk('public')->delete($perjalanan->foto_bon);
        }
        $perjalanan->delete();
        return redirect()->route('perjalanan.index')
            ->with('success', 'Data perjalanan berhasil dihapus');
    }
}