<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardSummaryResource;
use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\Perjalanan;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $data = [
            'jumlah_kendaraan' => Kendaraan::count(),
            'jumlah_pegawai' => Pegawai::count(),
            'jumlah_perjalanan' => Perjalanan::count(),
            'rata_rata_efisiensi' => round((float) (Perjalanan::avg('efisiensi') ?? 0), 2),
            'total_penggunaan_bbm' => round((float) Perjalanan::sum('vol_liter'), 2),
            'total_biaya_bbm' => round((float) Perjalanan::sum('jumlah_biaya'), 2),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => new DashboardSummaryResource($data),
        ], 200);
    }
}
