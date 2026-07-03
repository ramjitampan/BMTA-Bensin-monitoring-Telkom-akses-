<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PerjalananResource;
use App\Models\Perjalanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PerjalananController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pegawai_id' => 'nullable|integer|exists:pegawais,id',
            'kendaraan_id' => 'nullable|integer|exists:kendaraans,id',
            'tanggal_dari' => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
        ]);

        $query = Perjalanan::with(['pegawai', 'kendaraan'])
            ->when(isset($validated['pegawai_id']), fn ($q) => $q->where('pegawai_id', $validated['pegawai_id']))
            ->when(isset($validated['kendaraan_id']), fn ($q) => $q->where('kendaraan_id', $validated['kendaraan_id']))
            ->when(isset($validated['tanggal_dari']), fn ($q) => $q->whereDate('tanggal', '>=', $validated['tanggal_dari']))
            ->when(isset($validated['tanggal_sampai']), fn ($q) => $q->whereDate('tanggal', '<=', $validated['tanggal_sampai']))
            ->latest('tanggal')
            ->latest('id');

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => PerjalananResource::collection($query->get()),
        ], 200);
    }

    public function show(Perjalanan $perjalanan): JsonResponse
    {
        $perjalanan->load(['pegawai', 'kendaraan']);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => new PerjalananResource($perjalanan),
        ], 200);
    }
}
