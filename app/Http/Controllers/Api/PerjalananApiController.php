<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PerjalananResource;
use App\Models\Kendaraan;
use App\Models\Perjalanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerjalananApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pegawai_id'      => 'nullable|integer|exists:pegawais,id',
            'kendaraan_id'    => 'nullable|integer|exists:kendaraans,id',
            'status'          => 'nullable|string|in:balance,boros,anomali',
            'status_validasi' => 'nullable|string|in:Normal,Perlu Verifikasi,Anomali',
            'tanggal_dari'    => 'nullable|date',
            'tanggal_sampai'  => 'nullable|date|after_or_equal:tanggal_dari',
            'per_page'        => 'nullable|integer|min:1|max:100',
        ]);

        $perjalanans = Perjalanan::with(['pegawai', 'kendaraan'])
            ->when($validated['pegawai_id'] ?? null, fn ($q, $v) => $q->where('pegawai_id', $v))
            ->when($validated['kendaraan_id'] ?? null, fn ($q, $v) => $q->where('kendaraan_id', $v))
            ->when($validated['status'] ?? null, fn ($q, $v) => $q->where('status_efisiensi', $v))
            ->when($validated['status_validasi'] ?? null, fn ($q, $v) => $q->where('fraud_flags->status_anomali', $v))
            ->when($validated['tanggal_dari'] ?? null, fn ($q, $v) => $q->whereDate('tanggal', '>=', $v))
            ->when($validated['tanggal_sampai'] ?? null, fn ($q, $v) => $q->whereDate('tanggal', '<=', $v))
            ->orderByVehicleTimeline()
            ->paginate(min((int) ($validated['per_page'] ?? 15), 100));

        return response()->json([
            'message' => 'Data perjalanan berhasil diambil.',
            'data'    => PerjalananResource::collection($perjalanans),
            'meta'    => [
                'current_page' => $perjalanans->currentPage(),
                'last_page'    => $perjalanans->lastPage(),
                'per_page'     => $perjalanans->perPage(),
                'total'        => $perjalanans->total(),
            ],
        ]);
    }

    public function rekap(Request $request): JsonResponse
    {
        $perjalanans = Perjalanan::with(['pegawai', 'kendaraan'])
            ->when($request->filled('tanggal_dari'), fn ($q) => $q->whereDate('tanggal', '>=', $request->tanggal_dari))
            ->when($request->filled('tanggal_sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->tanggal_sampai))
            ->get();

        $totalJarak = $perjalanans->sum('jarak');
        $totalLiter = $perjalanans->sum('vol_liter');

        $rekapPegawai = $perjalanans
            ->groupBy('pegawai_id')
            ->map(function ($data) {
                $j = $data->sum('jarak');
                $l = $data->sum('vol_liter');
                $e = $l > 0 ? round($j / $l, 2) : 0;
                return [
                    'pegawai_id'       => $data->first()->pegawai_id,
                    'nama'             => $data->first()->pegawai->nama ?? '-',
                    'total_perjalanan' => $data->count(),
                    'total_jarak'      => round($j, 2),
                    'total_liter'      => round($l, 2),
                    'total_pengeluaran' => round($data->sum('jumlah_biaya'), 2),
                    'efisiensi'        => $e,
                    'status'           => Perjalanan::tentukanStatus($e),
                ];
            })
            ->sortBy('efisiensi')
            ->values();

        $rekapKendaraan = $perjalanans
            ->groupBy('kendaraan_id')
            ->map(function ($data) {
                $j = $data->sum('jarak');
                $l = $data->sum('vol_liter');
                $e = $l > 0 ? round($j / $l, 2) : 0;
                $t = $data->first()->kendaraan->tipe ?? 'R4';
                return [
                    'kendaraan_id'     => $data->first()->kendaraan_id,
                    'plat_nomor'       => $data->first()->kendaraan->plat_nomor ?? '-',
                    'tipe'             => $t,
                    'total_perjalanan' => $data->count(),
                    'total_jarak'      => round($j, 2),
                    'total_liter'      => round($l, 2),
                    'total_pengeluaran' => round($data->sum('jumlah_biaya'), 2),
                    'efisiensi'        => $e,
                    'status'           => Perjalanan::tentukanStatus($e, $t),
                ];
            })
            ->sortBy('efisiensi')
            ->values();

        return response()->json([
            'message' => 'Rekap monitoring berhasil diambil.',
            'data'    => [
                'statistik'      => [
                    'total_perjalanan'  => $perjalanans->count(),
                    'total_pengeluaran' => round($perjalanans->sum('jumlah_biaya'), 2),
                    'total_liter'       => round($totalLiter, 2),
                    'total_jarak'       => round($totalJarak, 2),
                    'rata_efisiensi'    => $totalLiter > 0 ? round($totalJarak / $totalLiter, 2) : 0,
                ],
                'rekap_pegawai'  => $rekapPegawai,
                'rekap_kendaraan' => $rekapKendaraan,
            ],
        ]);
    }

    public function show(Perjalanan $perjalanan): JsonResponse
    {
        $perjalanan->load(['pegawai', 'kendaraan']);

        return response()->json([
            'message' => 'Detail perjalanan berhasil diambil.',
            'data'    => new PerjalananResource($perjalanan),
        ]);
    }

    public function store(Request $request): JsonResponse
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

        if (!Perjalanan::isNominalGanjil((float) $validated['jumlah_biaya'])) {
            return $this->validationError('jumlah_biaya', 'Nominal bon harus ganjil-ribuan sesuai aturan Pertamina. Contoh: Rp51.000, Rp101.000.');
        }

        if (!empty($validated['no_bon']) && Perjalanan::isDuplicateBon($validated['no_bon'], $validated['kendaraan_id'])) {
            return $this->validationError('no_bon', 'Nomor bon ini sudah pernah diinput untuk kendaraan ini. Kemungkinan bon duplikat.');
        }

        $jarak    = Perjalanan::hitungJarak((float) $validated['km_lama'], (float) $validated['km_baru']);
        $volLiter = Perjalanan::hitungVolumeLiter((float) $validated['jumlah_biaya'], (float) $validated['harga_per_liter']);

        if (Perjalanan::isDuplicateRecord($validated['tanggal'], $validated['kendaraan_id'], (float) $validated['km_lama'], (float) $validated['km_baru'], $volLiter)) {
            return response()->json(['message' => 'Data duplikat.'], 409);
        }

        $efisiensi = Perjalanan::hitungEfisiensi($jarak, $volLiter);
        $kendaraan = Kendaraan::find($validated['kendaraan_id']);
        $tipe      = $kendaraan->jenis ?? 'R4';
        $bbm       = Perjalanan::inferBBM((float) $validated['harga_per_liter']);
        $status    = Perjalanan::tentukanStatus($efisiensi, $tipe, $bbm);

        $verifikasiResult = Perjalanan::hitungIndikasiVerifikasi([
            ...$validated, 'jarak' => $jarak, 'efisiensi' => $efisiensi,
        ], null, $tipe);

        $anomaliResult = Perjalanan::hitungAnomali($jarak, $volLiter, $efisiensi, $tipe, $bbm, $verifikasiResult['indikasi'], $status);

        $timeline = Perjalanan::validasiTimeline(
            (float) $validated['km_lama'],
            (float) $validated['km_baru'],
            (int) $validated['kendaraan_id'],
            null,
            $validated['tanggal']
        );

        $displayFlags = [];
        foreach ($verifikasiResult['indikasi'] as $code) {
            $displayFlags[] = match ($code) {
                'no_bon_duplikat'              => 'Bon Duplikat',
                'harga_tidak_wajar'            => 'Harga Tidak Wajar',
                'nominal_bon_kelipatan_bulat'  => 'Harga Tidak Wajar',
                'jarak_melebihi_batas_harian'   => 'Volume Tidak Wajar',
                'efisiensi_di_luar_batas_mutlak' => 'Volume Tidak Wajar',
                default                        => '',
            };
        }
        if (in_array($timeline['status'] ?? 'Logis', ['Tidak Logis'])) {
            $displayFlags[] = 'Timeline Tidak Logis';
        }
        if (in_array($timeline['status'] ?? 'Logis', ['Perlu Verifikasi'])) {
            $displayFlags[] = 'Odometer Mundur';
        }
        $displayFlags = array_values(array_unique(array_filter($displayFlags)));

        $fraudScore = match ($anomaliResult['status_anomali']) {
            'Perlu Verifikasi' => 50,
            'Anomali'          => ($status === 'balance') ? 50 : 90,
            default            => ($status === 'anomali') ? 50 : 10,
        };

        $fraudFlags = [
            'verifikasi_indikasi' => $verifikasiResult['indikasi'],
            'total_bobot'         => $verifikasiResult['total_bobot'],
            'status_anomali'      => $anomaliResult['status_anomali'],
            'hasil_sewajarnya'    => $anomaliResult['hasil_sewajarnya'],
            'deviasi'             => $anomaliResult['deviasi'],
            'keterangan_anomali'  => $anomaliResult['keterangan_anomali'],
            'timeline_status'     => $timeline['status'] ?? 'Logis',
            'alasan_timeline'     => $timeline['alasan'],
            'display_flags'       => $displayFlags,
        ];

        $fotoPath = null;
        if ($request->hasFile('foto_bon')) {
            $fotoPath = $request->file('foto_bon')->store('foto_bon', 'public');
        }

        $perjalanan = Perjalanan::create([
            ...$validated,
            'jarak'            => $jarak,
            'vol_liter'        => $volLiter,
            'foto_bon'         => $fotoPath,
            'efisiensi'        => $efisiensi,
            'status_efisiensi' => $status,
            'fraud_score'      => $fraudScore,
            'fraud_flags'      => $fraudFlags,
        ])->load(['pegawai', 'kendaraan']);

        return response()->json([
            'message'  => 'Data perjalanan berhasil disimpan melalui API.',
            'data'     => new PerjalananResource($perjalanan),
        ], 201);
    }

    private function validationError(string $field, string $message): JsonResponse
    {
        return response()->json([
            'message' => 'Validasi gagal.',
            'errors'  => [$field => [$message]],
        ], 422);
    }
}
