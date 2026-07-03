<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\Perjalanan;
use App\Services\OllamaService;
use Illuminate\View\View;

class AIController extends Controller
{
    public function index(OllamaService $ollama): View
    {
        $perjalanans = Perjalanan::with(['pegawai', 'kendaraan'])
            ->latest('tanggal')
            ->latest('id')
            ->take(25)
            ->get();

        $totalKendaraan = Kendaraan::count();
        $totalPegawai = Pegawai::count();
        $totalPerjalanan = Perjalanan::count();
        $rataEfisiensi = round((float) (Perjalanan::avg('efisiensi') ?? 0), 2);
        $totalPenggunaanBbm = round((float) Perjalanan::sum('vol_liter'), 2);

        $hasil = 'Data belum cukup untuk dianalisis.';
        $isOllamaConnected = false;

        if ($perjalanans->isNotEmpty() && $totalKendaraan > 0) {
            $ringkasanData = $perjalanans->map(function (Perjalanan $perjalanan): string {
                $tanggal = $perjalanan->tanggal
                    ? $perjalanan->tanggal->format('d-m-Y')
                    : '-';

                return implode("\n", [
                    'Tanggal : ' . $tanggal,
                    'Pegawai : ' . ($perjalanan->pegawai->nama ?? '-'),
                    'Plat Kendaraan : ' . ($perjalanan->kendaraan->plat_nomor ?? '-'),
                    'Jarak Tempuh : ' . number_format((float) $perjalanan->jarak, 2, ',', '.') . ' km',
                    'Volume BBM : ' . number_format((float) $perjalanan->vol_liter, 2, ',', '.') . ' liter',
                    'Efisiensi : ' . number_format((float) $perjalanan->efisiensi, 2, ',', '.') . ' km/l',
                    'Biaya : Rp ' . number_format((float) $perjalanan->jumlah_biaya, 0, ',', '.'),
                ]);
            })->implode("\n\n");

            $prompt = <<<PROMPT
Kamu adalah AI Analyst PT Telkom Akses Binjai.

Peranmu adalah analis kendaraan operasional.

Aturan:

- Selalu gunakan Bahasa Indonesia.
- Jangan menjawab bahasa Inggris.
- Jangan mengarang data.
- Gunakan hanya data yang diberikan.
- Jika data tidak cukup katakan data belum cukup.
- Maksimal 250 kata.

Yang harus dianalisis:

1. Kendaraan paling boros.
2. Kendaraan paling efisien.
3. Kendaraan yang perlu perhatian.
4. Kemungkinan penyebab.
5. Rekomendasi.

Jawaban menggunakan markdown sederhana.

Jumlah kendaraan terdaftar: {$totalKendaraan} unit.
Jumlah pegawai terdaftar: {$totalPegawai} orang.

Data perjalanan terbaru:

{$ringkasanData}
PROMPT;

            $hasil = $ollama->generate($prompt);
            $isOllamaConnected = $hasil !== 'Ollama sedang tidak aktif. Jalankan \'ollama serve\'.';
        }

        return view('dashboardAnalyst', [
            'hasil' => blank($hasil) ? 'Tidak ada analisis.' : $hasil,
            'isOllamaConnected' => $isOllamaConnected,
            'totalKendaraan' => $totalKendaraan,
            'totalPegawai' => $totalPegawai,
            'totalPerjalanan' => $totalPerjalanan,
            'rataEfisiensi' => $rataEfisiensi,
            'totalPenggunaanBbm' => $totalPenggunaanBbm,
        ]);
    }
}
