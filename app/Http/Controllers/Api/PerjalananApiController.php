<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Perjalanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerjalananApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perjalanans = Perjalanan::with(['pegawai', 'kendaraan'])
            ->when($request->filled('pegawai_id'), fn ($query) => $query->where('pegawai_id', $request->pegawai_id))
            ->when($request->filled('kendaraan_id'), fn ($query) => $query->where('kendaraan_id', $request->kendaraan_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status_efisiensi', $request->status))
            ->when($request->filled('tanggal_dari'), fn ($query) => $query->whereDate('tanggal', '>=', $request->tanggal_dari))
            ->when($request->filled('tanggal_sampai'), fn ($query) => $query->whereDate('tanggal', '<=', $request->tanggal_sampai))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate((int) $request->input('per_page', 15));

        return response()->json([
            'message' => 'Data perjalanan berhasil diambil.',
            'data' => $perjalanans->getCollection()
                ->map(fn (Perjalanan $perjalanan) => $this->formatPerjalanan($perjalanan))
                ->values(),
            'meta' => [
                'current_page' => $perjalanans->currentPage(),
                'last_page' => $perjalanans->lastPage(),
                'per_page' => $perjalanans->perPage(),
                'total' => $perjalanans->total(),
            ],
        ]);
    }

    public function rekap(Request $request): JsonResponse
    {
        $perjalanans = Perjalanan::with(['pegawai', 'kendaraan'])
            ->when($request->filled('tanggal_dari'), fn ($query) => $query->whereDate('tanggal', '>=', $request->tanggal_dari))
            ->when($request->filled('tanggal_sampai'), fn ($query) => $query->whereDate('tanggal', '<=', $request->tanggal_sampai))
            ->get();

        $totalJarak = $perjalanans->sum('jarak');
        $totalLiter = $perjalanans->sum('vol_liter');

        $rekapPegawai = $perjalanans
            ->groupBy('pegawai_id')
            ->map(function ($data) {
                $totalJarak = $data->sum('jarak');
                $totalLiter = $data->sum('vol_liter');
                $efisiensi = $totalLiter > 0 ? round($totalJarak / $totalLiter, 2) : 0;

                return [
                    'pegawai_id' => $data->first()->pegawai_id,
                    'nama' => $data->first()->pegawai->nama ?? '-',
                    'total_perjalanan' => $data->count(),
                    'total_jarak' => round($totalJarak, 2),
                    'total_liter' => round($totalLiter, 2),
                    'total_pengeluaran' => round($data->sum('jumlah_biaya'), 2),
                    'efisiensi' => $efisiensi,
                    'status' => Perjalanan::tentukanStatus($efisiensi),
                ];
            })
            ->sortBy('efisiensi')
            ->values();

        $rekapKendaraan = $perjalanans
            ->groupBy('kendaraan_id')
            ->map(function ($data) {
                $totalJarak = $data->sum('jarak');
                $totalLiter = $data->sum('vol_liter');
                $efisiensi = $totalLiter > 0 ? round($totalJarak / $totalLiter, 2) : 0;
                $tipe = $data->first()->kendaraan->tipe ?? 'R4';

                return [
                    'kendaraan_id' => $data->first()->kendaraan_id,
                    'plat_nomor' => $data->first()->kendaraan->plat_nomor ?? '-',
                    'tipe' => $tipe,
                    'total_perjalanan' => $data->count(),
                    'total_jarak' => round($totalJarak, 2),
                    'total_liter' => round($totalLiter, 2),
                    'total_pengeluaran' => round($data->sum('jumlah_biaya'), 2),
                    'efisiensi' => $efisiensi,
                    'status' => Perjalanan::tentukanStatus($efisiensi, $tipe),
                ];
            })
            ->sortBy('efisiensi')
            ->values();

        return response()->json([
            'message' => 'Rekap monitoring berhasil diambil.',
            'data' => [
                'statistik' => [
                    'total_perjalanan' => $perjalanans->count(),
                    'total_pengeluaran' => round($perjalanans->sum('jumlah_biaya'), 2),
                    'total_liter' => round($totalLiter, 2),
                    'total_jarak' => round($totalJarak, 2),
                    'rata_efisiensi' => $totalLiter > 0 ? round($totalJarak / $totalLiter, 2) : 0,
                ],
                'rekap_pegawai' => $rekapPegawai,
                'rekap_kendaraan' => $rekapKendaraan,
            ],
        ]);
    }

    public function show(Perjalanan $perjalanan): JsonResponse
    {
        $perjalanan->load(['pegawai', 'kendaraan']);

        return response()->json([
            'message' => 'Detail perjalanan berhasil diambil.',
            'data' => $this->formatPerjalanan($perjalanan),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'tanggal' => 'required|date',
            'tujuan' => 'required|string|max:255',
            'uraian' => 'nullable|string|max:255',
            'km_lama' => 'required|numeric|min:0',
            'km_baru' => 'required|numeric|gt:km_lama',
            'jumlah_biaya' => 'required|numeric|min:1000',
            'harga_per_liter' => 'required|numeric|min:1',
            'no_bon' => 'nullable|string|max:100',
            'foto_bon' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if (!Perjalanan::isNominalGanjil($validated['jumlah_biaya'])) {
            return $this->validationError('jumlah_biaya', 'Nominal bon harus ganjil-ribuan sesuai aturan Pertamina. Contoh: Rp51.000, Rp101.000.');
        }

        if (!empty($validated['no_bon']) && Perjalanan::isDuplicateBon($validated['no_bon'], $validated['kendaraan_id'])) {
            return $this->validationError('no_bon', 'Nomor bon ini sudah pernah diinput untuk kendaraan ini. Kemungkinan bon duplikat.');
        }

        $odometerTerakhir = Perjalanan::getOdometerTerakhir($validated['kendaraan_id']);
        if ($odometerTerakhir !== null && $validated['km_lama'] < $odometerTerakhir) {
            return $this->validationError(
                'km_lama',
                "KM awal ({$validated['km_lama']}) lebih kecil dari odometer terakhir kendaraan ini ({$odometerTerakhir} km). Odometer tidak bisa mundur."
            );
        }

        $jarak = $validated['km_baru'] - $validated['km_lama'];
        $volLiter = round($validated['jumlah_biaya'] / $validated['harga_per_liter'], 2);
        $efisiensi = $volLiter > 0 ? round($jarak / $volLiter, 2) : 0;

        $kendaraan = Kendaraan::find($validated['kendaraan_id']);
        $tipe = $kendaraan->tipe ?? 'R4';
        $status = Perjalanan::tentukanStatus($efisiensi, $tipe);

        $fraudResult = Perjalanan::hitungFraudScore([
            ...$validated,
            'jarak' => $jarak,
            'efisiensi' => $efisiensi,
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_bon')) {
            $fotoPath = $request->file('foto_bon')->store('foto_bon', 'public');
        }

        $perjalanan = Perjalanan::create([
            ...$validated,
            'jarak' => $jarak,
            'vol_liter' => $volLiter,
            'foto_bon' => $fotoPath,
            'efisiensi' => $efisiensi,
            'status_efisiensi' => $status,
            'fraud_score' => $fraudResult['score'],
            'fraud_flags' => $fraudResult['flags'],
        ])->load(['pegawai', 'kendaraan']);

        return response()->json([
            'message' => 'Data perjalanan berhasil disimpan melalui API.',
            'risk' => $fraudResult['risk'],
            'fraud_score' => $fraudResult['score'],
            'fraud_flags' => $fraudResult['flags'],
            'data' => $this->formatPerjalanan($perjalanan),
        ], 201);
    }

    private function validationError(string $field, string $message): JsonResponse
    {
        return response()->json([
            'message' => 'Validasi gagal.',
            'errors' => [
                $field => [$message],
            ],
        ], 422);
    }

    private function formatPerjalanan(Perjalanan $perjalanan): array
    {
        return [
            'id' => $perjalanan->id,
            'tanggal' => optional($perjalanan->tanggal)->toDateString(),
            'pegawai' => [
                'id' => $perjalanan->pegawai_id,
                'nama' => $perjalanan->pegawai->nama ?? null,
            ],
            'kendaraan' => [
                'id' => $perjalanan->kendaraan_id,
                'plat_nomor' => $perjalanan->kendaraan->plat_nomor ?? null,
                'tipe' => $perjalanan->kendaraan->tipe ?? null,
            ],
            'tujuan' => $perjalanan->tujuan,
            'uraian' => $perjalanan->uraian,
            'odometer' => [
                'km_lama' => $perjalanan->km_lama,
                'km_baru' => $perjalanan->km_baru,
                'jarak' => $perjalanan->jarak,
            ],
            'bbm' => [
                'vol_liter' => $perjalanan->vol_liter,
                'harga_per_liter' => $perjalanan->harga_per_liter,
                'jumlah_biaya' => $perjalanan->jumlah_biaya,
                'no_bon' => $perjalanan->no_bon,
                'foto_bon' => $perjalanan->foto_bon,
                'foto_bon_url' => $perjalanan->foto_bon ? Storage::url($perjalanan->foto_bon) : null,
            ],
            'monitoring' => [
                'efisiensi' => $perjalanan->efisiensi,
                'status_efisiensi' => $perjalanan->status_efisiensi,
                'fraud_score' => $perjalanan->fraud_score,
                'fraud_flags' => $perjalanan->fraud_flags ?? [],
            ],
            'created_at' => optional($perjalanan->created_at)->toDateTimeString(),
            'updated_at' => optional($perjalanan->updated_at)->toDateTimeString(),
        ];
    }
}
