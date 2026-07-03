<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\Perjalanan;
use App\Services\OllamaService;
use Illuminate\Http\JsonResponse;

class AIAnalysisController extends Controller
{
    public function index(OllamaService $ollama): JsonResponse
    {
        $perjalanans = Perjalanan::with(['pegawai', 'kendaraan'])
            ->latest('tanggal')
            ->latest('id')
            ->take(25)
            ->get();

        $totalKendaraan = Kendaraan::count();
        $totalPegawai = Pegawai::count();

        if ($perjalanans->isEmpty() || $totalKendaraan === 0) {
            return response()->json([
                'success' => true,
                'message' => 'Analisis berhasil dibuat',
                'analysis' => 'Data belum cukup untuk dianalisis.',
            ], 200);
        }

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

        $analysis = $ollama->generate($prompt);

        return response()->json([
            'success' => true,
            'message' => 'Analisis berhasil dibuat',
            'analysis' => blank($analysis) ? 'Tidak ada analisis.' : $analysis,
        ], 200);
    }
}
