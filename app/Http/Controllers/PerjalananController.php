<?php
// app/Http/Controllers/PerjalananController.php

namespace App\Http\Controllers;

use App\Exports\PerjalananMonthlyExport;
use App\Models\Perjalanan;
use App\Models\Pegawai;
use App\Models\Kendaraan;
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

    public function index(): View
    {
        // Satu query dengan eager-load — hindari N+1
        $perjalanans = Perjalanan::with('pegawai', 'kendaraan')
            ->orderBy('kendaraan_id')
            ->orderBy('tanggal')
            ->get();

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

        return view('perjalanan.index', compact('perjalanans', 'rekapPegawai'));
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
            ->orderBy('tanggal')
            ->orderBy('id')
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

        // Ditampilkan sebagai referensi informatif bagi admin,
        // bukan sebagai batas validasi input.
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

        // Validasi bisnis: bon & duplikasi nomor bon.
        // Validasi historis odometer (odometer_mundur) tidak dilakukan karena
        // admin menginput berdasarkan bon tanpa mengetahui urutan perjalanan.
        $bonError   = $this->validateBon($validated);
        $noBonError = $this->validateNoBon($validated);

        if ($bonError || $noBonError) {
            $errors = array_filter([
                'jumlah_biaya' => $bonError,
                'no_bon'       => $noBonError,
            ]);

            return back()->withErrors($errors)->withInput();
        }

        $payload = $this->buildPerjalananPayload($validated, $request);

        Perjalanan::create($payload);

        return redirect()->route('perjalanan.index')
            ->with(...$this->buildFlashMessage($payload['fraud_score'], $payload['fraud_flags']));
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

        // Validasi bon & duplikasi nomor bon — exclude record yang sedang diedit.
        // Validasi historis odometer (odometer_mundur) tidak dilakukan karena
        // admin menginput berdasarkan bon tanpa mengetahui urutan perjalanan.
        $bonError   = $this->validateBon($validated, $perjalanan->id);
        $noBonError = $this->validateNoBon($validated, $perjalanan->id);

        if ($bonError || $noBonError) {
            $errors = array_filter([
                'jumlah_biaya' => $bonError,
                'no_bon'       => $noBonError,
            ]);

            return back()->withErrors($errors)->withInput();
        }

        // Hitung ulang semua nilai turunan — sama seperti store()
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

    /**
     * Aturan validasi request yang dipakai oleh store() dan update().
     * Dipisahkan agar DRY dan mudah diubah di satu tempat.
     */
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

    /**
     * Validasi nominal bon: harus kelipatan Rp1.000, bukan kelipatan Rp10.000.
     * Mengembalikan pesan error atau null jika valid.
     */
    private function validateBon(array $validated, ?int $excludeId = null): ?string
    {
        if (!Perjalanan::isNominalGanjil((float) $validated['jumlah_biaya'])) {
            return 'Nominal bon tidak boleh kelipatan Rp10.000 bulat. Contoh valid: Rp51.000, Rp52.000, Rp127.000.';
        }

        return null;
    }

    /**
     * Validasi nomor bon tidak duplikat.
     * Mengembalikan pesan error atau null jika valid.
     */
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

    /**
     * Bangun array payload lengkap yang siap disimpan ke database.
     * Menghitung semua nilai turunan: jarak, vol_liter, efisiensi,
     * status_efisiensi, status_reason, fraud_score, fraud_flags.
     *
     * Dipakai oleh store() dan update() — single source of truth.
     *
     * @param  array          $validated  Data yang sudah lolos validasi request
     * @param  Request        $request    Dipakai untuk mengambil file foto_bon
     * @param  Perjalanan|null $existing  Record lama (saat update); null saat create
     */
    private function buildPerjalananPayload(
        array      $validated,
        Request    $request,
        ?Perjalanan $existing = null
    ): array {
        $excludeId = $existing?->id;

        // --- Kalkulasi turunan ---
        $jarak     = Perjalanan::hitungJarak((float) $validated['km_lama'], (float) $validated['km_baru']);
        $volLiter  = Perjalanan::hitungVolumeLiter((float) $validated['jumlah_biaya'], (float) $validated['harga_per_liter']);
        $efisiensi = Perjalanan::hitungEfisiensi($jarak, $volLiter);

        $kendaraan = Kendaraan::find($validated['kendaraan_id']);
        $tipe      = $kendaraan->jenis ?? 'R4';
        $status    = Perjalanan::tentukanStatus($efisiensi, $tipe);

        // Ambil rata-rata historis untuk status_reason
        $statistik    = Perjalanan::getStatistikEfisiensi($validated['kendaraan_id'], $excludeId);
        $statusReason = Perjalanan::generateStatusReason($efisiensi, $tipe, $status, $statistik['avg']);

        // --- Fraud detection ---
        $fraudResult = Perjalanan::hitungFraudScore([
            ...$validated,
            'jarak'     => $jarak,
            'efisiensi' => $efisiensi,
        ], $excludeId);

        // --- Foto bon ---
        $fotoPath = $existing?->foto_bon;
        if ($request->hasFile('foto_bon')) {
            // Hapus foto lama jika ada
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
            'fraud_score'      => $fraudResult['score'],
            'fraud_flags'      => $fraudResult['flags'],
        ];
    }

    /**
     * Bangun argumen flash message berdasarkan hasil fraud detection.
     * Mengembalikan [$flashType, $pesan] yang langsung bisa di-spread ke with().
     */
    private function buildFlashMessage(int $fraudScore, array $fraudFlags): array
    {
        $risk = Perjalanan::interpretRisk($fraudScore);

        $pesan = match ($risk) {
            'aman'         => 'Data perjalanan berhasil disimpan.',
            'perhatian'    => 'Data disimpan. Terdapat catatan minor: ' . implode(', ', $fraudFlags) . '.',
            'mencurigakan' => 'Data disimpan namun terdeteksi ' . count($fraudFlags) . ' indikasi anomali. Harap review.',
            'tinggi'       => 'Data disimpan. PERINGATAN: Skor kecurangan tinggi (' . $fraudScore . '). Segera laporkan ke atasan.',
            default        => 'Data disimpan.',
        };

        $flashType = in_array($risk, ['mencurigakan', 'tinggi'], true) ? 'warning' : 'success';

        return [$flashType, $pesan];
    }
}
