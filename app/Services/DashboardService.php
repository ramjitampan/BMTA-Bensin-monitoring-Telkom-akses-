<?php

namespace App\Services;

use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\Perjalanan;
use Illuminate\Support\Facades\Cache;

/**
 * Menyediakan data statistik dashboard dengan cache 5 menit.
 */
class DashboardService
{
    public function getDashboardData(): array
    {
        return Cache::remember('dashboard_stats', 300, function () {
            $totalPegawai     = Pegawai::count();
            $totalKendaraan   = Kendaraan::count();
            $totalPerjalanan  = Perjalanan::count();
            $totalBBM         = Perjalanan::sum('jumlah_biaya');
            $totalLiter       = Perjalanan::sum('vol_liter');
            $rataEfisiensi    = round(Perjalanan::avg('efisiensi') ?? 0, 2);

            return compact(
                'totalPegawai',
                'totalKendaraan',
                'totalPerjalanan',
                'totalBBM',
                'totalLiter',
                'rataEfisiensi'
            );
        });
    }

    public function forgetCache(): void
    {
        Cache::forget('dashboard_stats');
    }
}
