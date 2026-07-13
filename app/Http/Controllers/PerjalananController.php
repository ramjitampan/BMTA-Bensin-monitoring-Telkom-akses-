<?php

namespace App\Http\Controllers;

use App\Exports\PerjalananMonthlyExport;
use App\Models\Perjalanan;
use App\Models\Pegawai;
use App\Models\Kendaraan;
use App\Services\AnomalyDetectionService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PerjalananController extends Controller
{
    // =========================================================
    // INDEX
    // =========================================================

    public function index(AnomalyDetectionService $service, Request $request): View
    {
        $query = Perjalanan::with('pegawai', 'kendaraan')
            ->orderByVehicleTimeline();

        $perjalanans = $query->get();

        // Timpa fraud_score & fraud_flags di setiap model dengan hasil compute terbaru
        $computedResults = $service->getAll();
        $computedById = $computedResults->keyBy(fn(array $r): mixed => $r['perjalanan']->id);
        $perjalanans->each(function (Perjalanan $p) use ($computedById): void {
            $r = $computedById->get($p->id);
            if ($r) {
                $p->fraud_score = $r['fraud_score'];
                $p->fraud_flags = $r['fraud_flags'];
            }
        });

        // Filter by anomali status if requested
        if ($request->filter === 'anomali') {
            $perjalanans = $perjalanans->filter(function (Perjalanan $p): bool {
                $flags = $p->fraud_flags ?? [];
                return ($flags['status_anomali'] ?? 'Normal') === 'Anomali';
            });
        }

        // Data kurva efisiensi per kendaraan
        $chartColors = ['#3b82f6','#ef4444','#10b981','#f59e0b','#8b5cf6','#ec4899','#14b8a6','#f97316','#6366f1','#84cc16'];
        $chartData   = [];

        $allDates = $perjalanans->pluck('tanggal')->unique()->sort()->values();
        $allDateLabels = $allDates->map(fn($d) => $d->format('d/m/Y'));

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

        // Rekap per pegawai dari koleksi yang sudah diambil (tidak query ulang)
        $rekapPegawai = $perjalanans
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
                    'status'           => Perjalanan::tentukanStatus($avg, $tipe),
                ];
            })
            ->sortBy('avg_efisiensi');

        // Prepare JSON data for the validation modal
        $perjalananJson = $service->getPerjalananJson();
        if ($request->filter === 'anomali') {
            $filteredIds = $perjalanans->pluck('id')->toArray();
            $perjalananJson = $perjalananJson->filter(fn(array $item): bool => in_array($item['id'], $filteredIds))->values();
        }

        return view('perjalanan.index', compact('perjalanans', 'chartData', 'rekapPegawai', 'perjalananJson'));
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'digits:4', 'between:2000,2100'],
        ]);

        $bulan = (int) $validated['bulan'];
        $tahun = (int) $validated['tahun'];
        $perjalanans = Perjalanan::with('pegawai', 'kendaraan')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderByVehicleTimeline()
            ->get();

        $filename = sprintf('laporan-perjalanan-%04d-%02d.xlsx', $tahun, $bulan);

        return Excel::download(new PerjalananMonthlyExport($perjalanans, $bulan, $tahun), $filename);
    }

    // =========================================================
    // CREATE
    // =========================================================

    public function create(): View
    {
        $pegawais   = Pegawai::all();
        $kendaraans = Kendaraan::all();

        $kmTerakhir = [];
        foreach ($kendaraans as $kendaraan) {
            $kmTerakhir[$kendaraan->id] = Perjalanan::getOdometerTerakhir($kendaraan->id);
        }

        return view('perjalanan.create', compact('pegawais', 'kendaraans', 'kmTerakhir'));
    }

    // =========================================================
    // STORE
    // =========================================================

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePerjalanan($request);

        $bonError   = $this->validateBon($validated);
        $noBonError = $this->validateNoBon($validated);

        if ($bonError || $noBonError) {
            $errors = array_filter([
                'jumlah_biaya' => $bonError,
                'no_bon'       => $noBonError,
            ]);

            return back()->withErrors($errors)->withInput();
        }

        if (Perjalanan::isDuplicateRecord(
            $validated['tanggal'],
            $validated['kendaraan_id'],
            (float) $validated['km_lama'],
            (float) $validated['km_baru'],
            Perjalanan::hitungVolumeLiter((float) $validated['jumlah_biaya'], (float) $validated['harga_per_liter']),
        )) {
            return back()->with('warning', 'Data ini sudah pernah dicatat. Hindari entry ganda.')
                ->withInput();
        }

        $payload = $this->buildPerjalananPayload($validated, $request);

        Perjalanan::create($payload);

        return redirect()->route('perjalanan.index')
            ->with(...$this->buildFlashMessage($payload));
    }

    // =========================================================
    // EDIT
    // =========================================================

    public function edit(Perjalanan $perjalanan): View
    {
        $pegawais   = Pegawai::all();
        $kendaraans = Kendaraan::all();

        return view('perjalanan.edit', compact('perjalanan', 'pegawais', 'kendaraans'));
    }

    // =========================================================
    // UPDATE
    // =========================================================

    public function update(Request $request, Perjalanan $perjalanan): RedirectResponse
    {
        $validated = $this->validatePerjalanan($request);

        $bonError   = $this->validateBon($validated, $perjalanan->id);
        $noBonError = $this->validateNoBon($validated, $perjalanan->id);

        if ($bonError || $noBonError) {
            $errors = array_filter([
                'jumlah_biaya' => $bonError,
                'no_bon'       => $noBonError,
            ]);

            return back()->withErrors($errors)->withInput();
        }

        if (Perjalanan::isDuplicateRecord(
            $validated['tanggal'],
            $validated['kendaraan_id'],
            (float) $validated['km_lama'],
            (float) $validated['km_baru'],
            Perjalanan::hitungVolumeLiter((float) $validated['jumlah_biaya'], (float) $validated['harga_per_liter']),
            $perjalanan->id,
        )) {
            return back()->with('warning', 'Data ini sudah pernah dicatat untuk kendaraan ini. Hindari duplikasi.')
                ->withInput();
        }

        $payload = $this->buildPerjalananPayload($validated, $request, $perjalanan);

        $perjalanan->update($payload);

        return redirect()->route('perjalanan.index')
            ->with('success', 'Data perjalanan berhasil diupdate.');
    }

    // =========================================================
    // DESTROY
    // =========================================================

    public function destroy(Perjalanan $perjalanan): RedirectResponse
    {
        if ($perjalanan->foto_bon) {
            Storage::disk('public')->delete($perjalanan->foto_bon);
        }

        $perjalanan->delete();

        return redirect()->route('perjalanan.index')
            ->with('success', 'Data perjalanan berhasil dihapus.');
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function validatePerjalanan(Request $request): array
    {
        return $request->validate([
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
    }

    private function validateBon(array $validated, ?int $excludeId = null): ?string
    {
        if (!Perjalanan::isNominalGanjil((float) $validated['jumlah_biaya'])) {
            return 'Nominal bon tidak boleh kelipatan Rp10.000 bulat. Contoh valid: Rp51.000, Rp52.000, Rp127.000.';
        }

        return null;
    }

    private function validateNoBon(array $validated, ?int $excludeId = null): ?string
    {
        if (
            !empty($validated['no_bon']) &&
            Perjalanan::isDuplicateBon($validated['no_bon'], $validated['kendaraan_id'], $excludeId)
        ) {
            return 'Nomor bon ini sudah pernah diinput untuk kendaraan ini. Kemungkinan bon duplikat.';
        }

        return null;
    }

    private function buildPerjalananPayload(
        array      $validated,
        Request    $request,
        ?Perjalanan $existing = null
    ): array {
        $excludeId = $existing?->id;

        $jarak     = Perjalanan::hitungJarak((float) $validated['km_lama'], (float) $validated['km_baru']);
        $volLiter  = Perjalanan::hitungVolumeLiter((float) $validated['jumlah_biaya'], (float) $validated['harga_per_liter']);
        $efisiensi = Perjalanan::hitungEfisiensi($jarak, $volLiter);

        $kendaraan = Kendaraan::find($validated['kendaraan_id']);
        $tipe      = $kendaraan->jenis ?? 'R4';
        $bbm       = Perjalanan::inferBBM((float) $validated['harga_per_liter']);
        $status    = Perjalanan::tentukanStatus($efisiensi, $tipe, $bbm);

        $statusReason = Perjalanan::generateStatusReason($efisiensi, $tipe, $status, $bbm);

        // --- Indikasi Verifikasi (menggantikan fraud detection) ---
        $verifikasiResult = Perjalanan::hitungIndikasiVerifikasi([
            ...$validated,
            'jarak'     => $jarak,
            'efisiensi' => $efisiensi,
        ], $excludeId, $tipe);

        // --- Anomali Detection ---
        $anomaliResult = Perjalanan::hitungAnomali(
            $jarak,
            $volLiter,
            $efisiensi,
            $tipe,
            $bbm,
            $verifikasiResult['indikasi'],
            $status,
        );

        // --- Timeline Validation ---
        $timeline = Perjalanan::validasiTimeline(
            (float) $validated['km_lama'],
            (float) $validated['km_baru'],
            (int) $validated['kendaraan_id'],
            $excludeId,
            $validated['tanggal']
        );

        // Map anomaly status to fraud_score (existing column - kept for backend)
        $fraudScore = match($anomaliResult['status_anomali']) {
            'Perlu Verifikasi' => 50,
            'Anomali'          => ($status === 'balance') ? 50 : 90,
            default => ($status === 'anomali') ? 50 : 10,
        };

        // Build display flags
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

        // Store computed data in fraud_flags (existing column) as JSON
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

        // --- Foto bon ---
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

    private function buildFlashMessage(array $payload): array
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
}
