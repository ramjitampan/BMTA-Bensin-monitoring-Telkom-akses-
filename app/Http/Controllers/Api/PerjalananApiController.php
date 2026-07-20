<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerjalananRequest;
use App\Http\Resources\PerjalananResource;
use App\Models\Perjalanan;
use App\Services\EfisiensiService;
use App\Services\PerjalananService;
use App\Services\ValidasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PerjalananApiController extends Controller
{
    public function __construct(
        private PerjalananService $perjalananService,
        private EfisiensiService $efisiensiService,
        private ValidasiService $validasiService
    ) {}

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
        $data = $this->perjalananService->getRekapData($request);

        return response()->json([
            'message' => 'Rekap monitoring berhasil diambil.',
            'data'    => $data,
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

    public function store(PerjalananRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (!$this->validasiService->isNominalGanjil((float) $validated['jumlah_biaya'])) {
            return $this->validationError('jumlah_biaya', 'Nominal bon harus ganjil-ribuan sesuai aturan Pertamina. Contoh: Rp51.000, Rp101.000.');
        }

        if (!empty($validated['no_bon']) && $this->validasiService->isDuplicateBon($validated['no_bon'], $validated['kendaraan_id'])) {
            return $this->validationError('no_bon', 'Nomor bon ini sudah pernah diinput untuk kendaraan ini. Kemungkinan bon duplikat.');
        }

        $volLiter = $this->efisiensiService->hitungVolumeLiter((float) $validated['jumlah_biaya'], (float) $validated['harga_per_liter']);

        if ($this->validasiService->isDuplicateRecord($validated['tanggal'], $validated['kendaraan_id'], (float) $validated['km_lama'], (float) $validated['km_baru'], $volLiter)) {
            return response()->json(['message' => 'Data duplikat.'], 409);
        }

        $payload = $this->perjalananService->buildPayload($validated, $request);

        $perjalanan = Perjalanan::create($payload)->load(['pegawai', 'kendaraan']);

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
