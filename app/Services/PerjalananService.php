<?php

namespace App\Services;

use App\Models\Kendaraan;
use App\Models\Perjalanan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PerjalananService
{
    public function __construct(
        private EfisiensiService $efisiensiService,
        private ValidasiService $validasiService,
        private TimelineService $timelineService,
        private FraudService $fraudService
    ) {}

    public function getAll(?string $filter = null): Collection
    {
        $query = Perjalanan::with('pegawai', 'kendaraan')
            ->orderByVehicleTimeline();

        $perjalanans = $query->get();

        if ($filter === 'anomali') {
            $perjalanans = $perjalanans->filter(function (Perjalanan $p): bool {
                $flags = $p->fraud_flags ?? [];
                return ($flags['status_anomali'] ?? 'Normal') === 'Anomali';
            });
        }

        return $perjalanans;
    }

    public function getPaginated(?string $filter = null): LengthAwarePaginator
    {
        return Perjalanan::with('pegawai', 'kendaraan')
            ->orderByVehicleTimeline()
            ->when($filter === 'anomali', fn($q) => $q->where('fraud_flags->status_anomali', 'Anomali'))
            ->paginate(15);
    }

    public function getChartData(Collection $perjalanans): array
    {
        $chartColors = ['#3b82f6','#ef4444','#10b981','#f59e0b','#8b5cf6','#ec4899','#14b8a6','#f97316','#6366f1','#84cc16'];
        $chartData   = [];

        $allDates = $perjalanans->pluck('tanggal')->unique()->sort()->values();

        $perjalanans->groupBy('kendaraan_id')->each(function ($trips) use (&$chartData, $chartColors, $allDates) {
            $kendaraan = $trips->first()->kendaraan;
            $byDate    = $trips->keyBy(fn($t) => $t->tanggal->format('Y-m-d'));
            $i         = count($chartData);

            $chartData[] = [
                'label' => $kendaraan->plat_nomor ?? $kendaraan->nomor_polisi ?? '-',
                'merk'  => $kendaraan->merk,
                'color' => $chartColors[$i % count($chartColors)],
                'points' => $allDates->map(function ($date) use ($byDate) {
                    $key = $date->format('Y-m-d');
                    $t   = $byDate[$key] ?? null;
                    return [
                        'x'        => $date->format('d/m/Y'),
                        'y'        => $t?->efisiensi,
                        'fullDate' => $date->format('d/m/Y'),
                        'status'   => $t?->status_efisiensi,
                        'pegawai'  => $t?->pegawai?->nama ?? '-',
                        'jarak'    => $t?->jarak,
                        'volume'   => $t?->vol_liter,
                        'tujuan'   => $t?->tujuan,
                    ];
                }),
            ];
        });

        return $chartData;
    }

    public function getRekapPegawai(Collection $perjalanans): Collection
    {
        return $perjalanans
            ->groupBy('pegawai_id')
            ->map(function ($data) {
                $avg  = $data->avg('efisiensi') ?? 0.0;
                $tipe = $data->first()->kendaraan->jenis ?? 'R4';
                return [
                    'nama'             => $data->first()->pegawai->nama ?? '-',
                    'total_perjalanan' => $data->count(),
                    'total_anomali'    => $data->where('status_efisiensi', 'anomali')->count(),
                    'total_jarak'      => $data->sum('jarak'),
                    'total_biaya'      => $data->sum('jumlah_biaya'),
                    'avg_efisiensi'    => $avg,
                    'status'           => $this->efisiensiService->tentukanStatus($avg, $tipe),
                ];
            })
            ->sortBy('avg_efisiensi');
    }

    public function buildPayload(array $validated, Request $request, ?Perjalanan $existing = null): array
    {
        $excludeId = $existing?->id;

        $jarak     = $this->efisiensiService->hitungJarak((float) $validated['km_lama'], (float) $validated['km_baru']);
        $volLiter  = $this->efisiensiService->hitungVolumeLiter((float) $validated['jumlah_biaya'], (float) $validated['harga_per_liter']);
        $efisiensi = $this->efisiensiService->hitungEfisiensi($jarak, $volLiter);

        $kendaraan = Kendaraan::find($validated['kendaraan_id']);
        $tipe      = $kendaraan->jenis ?? 'R4';
        $bbm       = $this->efisiensiService->inferBBM((float) $validated['harga_per_liter']);
        $status    = $this->efisiensiService->tentukanStatus($efisiensi, $tipe, $bbm);

        $statusReason = $this->efisiensiService->generateStatusReason($efisiensi, $tipe, $status, $bbm);

        $verifikasiResult = $this->fraudService->hitungIndikasiVerifikasi([
            ...$validated,
            'jarak'     => $jarak,
            'efisiensi' => $efisiensi,
        ], $excludeId, $tipe);

        $anomaliResult = $this->fraudService->hitungAnomali(
            $jarak,
            $volLiter,
            $efisiensi,
            $tipe,
            $bbm,
            $verifikasiResult['indikasi'],
            $status,
        );

        $timeline = $this->timelineService->validasiTimeline(
            (float) $validated['km_lama'],
            (float) $validated['km_baru'],
            (int) $validated['kendaraan_id'],
            $excludeId,
            $validated['tanggal']
        );

        $fraudScore = match($anomaliResult['status_anomali']) {
            'Perlu Verifikasi' => 50,
            'Anomali'          => ($status === 'balance') ? 50 : 90,
            default => ($status === 'anomali') ? 50 : 10,
        };

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

        $fotoPath = $existing?->foto_bon;
        if ($request->hasFile('foto_bon')) {
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto_bon')->store('foto_bon', 'public');
        }

        return [
            ...$validated,
            'jarak'            => $jarak,
            'vol_liter'        => $volLiter,
            'efisiensi'        => $efisiensi,
            'status_efisiensi' => $status,
            'status_reason'    => $statusReason,
            'foto_bon'         => $fotoPath,
            'fraud_score'      => $fraudScore,
            'fraud_flags'      => $fraudFlags,
        ];
    }

    public function buildFlashMessage(array $payload): array
    {
        $flags = $payload['fraud_flags'] ?? [];
        $statusAnomali = $flags['status_anomali'] ?? 'Normal';

        if ($statusAnomali === 'Anomali') {
            return [
                'warning',
                'Data disimpan. Status: Anomali. Data perjalanan perlu diverifikasi lebih lanjut.',
            ];
        }

        if ($statusAnomali === 'Perlu Verifikasi') {
            return [
                'warning',
                'Data disimpan. Status: Perlu Verifikasi. Terdapat indikasi yang perlu diperiksa.',
            ];
        }

        return ['success', 'Data perjalanan berhasil disimpan.'];
    }

    public function getRekapData(\Illuminate\Http\Request $request): array
    {
        $perjalanans = Perjalanan::with(['pegawai', 'kendaraan'])
            ->when($request->filled('tanggal_dari'), fn($q) => $q->whereDate('tanggal', '>=', $request->tanggal_dari))
            ->when($request->filled('tanggal_sampai'), fn($q) => $q->whereDate('tanggal', '<=', $request->tanggal_sampai))
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
                    'status'           => $this->efisiensiService->tentukanStatus($e),
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
                    'status'           => $this->efisiensiService->tentukanStatus($e, $t),
                ];
            })
            ->sortBy('efisiensi')
            ->values();

        return [
            'statistik' => [
                'total_perjalanan'  => $perjalanans->count(),
                'total_pengeluaran' => round($perjalanans->sum('jumlah_biaya'), 2),
                'total_liter'       => round($totalLiter, 2),
                'total_jarak'       => round($totalJarak, 2),
                'rata_efisiensi'    => $totalLiter > 0 ? round($totalJarak / $totalLiter, 2) : 0,
            ],
            'rekap_pegawai'  => $rekapPegawai,
            'rekap_kendaraan' => $rekapKendaraan,
        ];
    }
}
