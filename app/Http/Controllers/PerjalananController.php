<?php

namespace App\Http\Controllers;

use App\Exports\PerjalananMonthlyExport;
use App\Http\Requests\PerjalananRequest;
use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\Perjalanan;
use App\Services\AnomalyDetectionService;
use App\Services\PerjalananService;
use App\Services\EfisiensiService;
use App\Services\ValidasiService;
use App\Services\TimelineService;
use App\Services\DashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PerjalananController extends Controller
{
    public function __construct(
        private PerjalananService $perjalananService,
        private AnomalyDetectionService $anomalyDetectionService,
        private EfisiensiService $efisiensiService,
        private ValidasiService $validasiService,
        private TimelineService $timelineService,
        private DashboardService $dashboardService
    ) {}

    public function index(Request $request): View
    {
        $perjalanans = $this->perjalananService->getAll($request->filter);

        $computedResults = $this->anomalyDetectionService->getAll();
        $computedById = $computedResults->keyBy(fn(array $r): mixed => $r['perjalanan']->id);
        $perjalanans->each(function (Perjalanan $p) use ($computedById): void {
            $r = $computedById->get($p->id);
            if ($r) {
                $p->fraud_score = $r['fraud_score'];
                $p->fraud_flags = $r['fraud_flags'];
            }
        });

        if ($request->filter === 'anomali') {
            $perjalanans = $perjalanans->filter(function (Perjalanan $p): bool {
                $flags = $p->fraud_flags ?? [];
                return ($flags['status_anomali'] ?? 'Normal') === 'Anomali';
            });
        }

        $chartData = $this->perjalananService->getChartData($perjalanans);
        $rekapPegawai = $this->perjalananService->getRekapPegawai($perjalanans);

        $perjalananJson = $this->anomalyDetectionService->getPerjalananJson();
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

    public function create(): View
    {
        $pegawais   = Pegawai::all();
        $kendaraans = Kendaraan::all();

        $kmTerakhir = [];
        foreach ($kendaraans as $kendaraan) {
            $kmTerakhir[$kendaraan->id] = $this->timelineService->getOdometerTerakhir($kendaraan->id);
        }

        return view('perjalanan.create', compact('pegawais', 'kendaraans', 'kmTerakhir'));
    }

    public function store(PerjalananRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $bonError = null;
        if (!$this->validasiService->isNominalGanjil((float) $validated['jumlah_biaya'])) {
            $bonError = 'Nominal bon tidak boleh kelipatan Rp10.000 bulat. Contoh valid: Rp51.000, Rp52.000, Rp127.000.';
        }

        $noBonError = null;
        if (
            !empty($validated['no_bon']) &&
            $this->validasiService->isDuplicateBon($validated['no_bon'], $validated['kendaraan_id'])
        ) {
            $noBonError = 'Nomor bon ini sudah pernah diinput untuk kendaraan ini. Kemungkinan bon duplikat.';
        }

        if ($bonError || $noBonError) {
            $errors = array_filter([
                'jumlah_biaya' => $bonError,
                'no_bon'       => $noBonError,
            ]);

            return back()->withErrors($errors)->withInput();
        }

        if ($this->validasiService->isDuplicateRecord(
            $validated['tanggal'],
            $validated['kendaraan_id'],
            (float) $validated['km_lama'],
            (float) $validated['km_baru'],
            $this->efisiensiService->hitungVolumeLiter((float) $validated['jumlah_biaya'], (float) $validated['harga_per_liter']),
        )) {
            return back()->with('warning', 'Data ini sudah pernah dicatat. Hindari entry ganda.')
                ->withInput();
        }

        $payload = $this->perjalananService->buildPayload($validated, $request);

        Perjalanan::create($payload);

        $this->dashboardService->forgetCache();

        return redirect()->route('perjalanan.index')
            ->with(...$this->perjalananService->buildFlashMessage($payload));
    }

    public function edit(Perjalanan $perjalanan): View
    {
        $pegawais   = Pegawai::all();
        $kendaraans = Kendaraan::all();

        return view('perjalanan.edit', compact('perjalanan', 'pegawais', 'kendaraans'));
    }

    public function update(PerjalananRequest $request, Perjalanan $perjalanan): RedirectResponse
    {
        $validated = $request->validated();

        $bonError = null;
        if (!$this->validasiService->isNominalGanjil((float) $validated['jumlah_biaya'])) {
            $bonError = 'Nominal bon tidak boleh kelipatan Rp10.000 bulat. Contoh valid: Rp51.000, Rp52.000, Rp127.000.';
        }

        $noBonError = null;
        if (
            !empty($validated['no_bon']) &&
            $this->validasiService->isDuplicateBon($validated['no_bon'], $validated['kendaraan_id'], $perjalanan->id)
        ) {
            $noBonError = 'Nomor bon ini sudah pernah diinput untuk kendaraan ini. Kemungkinan bon duplikat.';
        }

        if ($bonError || $noBonError) {
            $errors = array_filter([
                'jumlah_biaya' => $bonError,
                'no_bon'       => $noBonError,
            ]);

            return back()->withErrors($errors)->withInput();
        }

        if ($this->validasiService->isDuplicateRecord(
            $validated['tanggal'],
            $validated['kendaraan_id'],
            (float) $validated['km_lama'],
            (float) $validated['km_baru'],
            $this->efisiensiService->hitungVolumeLiter((float) $validated['jumlah_biaya'], (float) $validated['harga_per_liter']),
            $perjalanan->id,
        )) {
            return back()->with('warning', 'Data ini sudah pernah dicatat untuk kendaraan ini. Hindari duplikasi.')
                ->withInput();
        }

        $payload = $this->perjalananService->buildPayload($validated, $request, $perjalanan);

        $perjalanan->update($payload);

        $this->dashboardService->forgetCache();

        return redirect()->route('perjalanan.index')
            ->with('success', 'Data perjalanan berhasil diupdate.');
    }

    public function destroy(Perjalanan $perjalanan): RedirectResponse
    {
        if ($perjalanan->foto_bon) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($perjalanan->foto_bon);
        }

        $perjalanan->delete();
        $this->dashboardService->forgetCache();

        return redirect()->route('perjalanan.index')
            ->with('success', 'Data perjalanan berhasil dihapus.');
    }
}
