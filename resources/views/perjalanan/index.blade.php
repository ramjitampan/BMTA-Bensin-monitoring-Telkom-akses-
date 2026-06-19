<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Perjalanan BBM — PT Telkom Akses Binjai</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        telkom: { red: '#CC0000', dark: '#A30000' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen text-sm">

    {{-- Navbar --}}
    <div class="navbar bg-[#CC0000] text-white shadow-lg sticky top-0 z-50 px-4">
        <div class="flex-1 flex items-center gap-3">
            <div class="avatar placeholder">
                <div class="bg-white text-[#CC0000] rounded-full w-9 font-black text-xs flex items-center justify-center">TA</div>
            </div>
            <div>
                <p class="font-bold text-sm leading-tight">Telkom Akses Binjai</p>
                <p class="text-xs opacity-75 leading-tight">Sistem Informasi BBM Kendaraan</p>
            </div>
        </div>
        <div class="flex-none">
            <a href="{{ route('perjalanan.create') }}" class="btn btn-sm bg-white text-[#CC0000] border-none hover:bg-gray-100 font-bold">
                + Tambah Data BBM
            </a>
        </div>
    </div>

    <div class="max-w-screen-2xl mx-auto p-4">

        {{-- Flash --}}
        @if(session('success'))
        <div class="alert alert-success mb-4 text-sm">
            <span>✓ {{ session('success') }}</span>
        </div>
        @endif
        @if(session('warning'))
        <div class="alert alert-warning mb-4 text-sm">
            <span>⚠ {{ session('warning') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-error mb-4 text-sm">
            <span>✕ {{ session('error') }}</span>
        </div>
        @endif

        {{-- Stat Cards --}}
        @php
            $totalAnomali     = $perjalanans->where('status_efisiensi', 'anomali')->count();
            $totalFraudTinggi = $perjalanans->where('fraud_score', '>', 50)->count();
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card bg-white shadow-sm border border-gray-100">
                <div class="card-body p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Total Perjalanan</p>
                    <p class="text-3xl font-black text-gray-800">{{ $perjalanans->count() }}</p>
                    <p class="text-xs text-gray-400">trip tercatat</p>
                </div>
            </div>
            <div class="card bg-white shadow-sm border border-gray-100">
                <div class="card-body p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Total Biaya BBM</p>
                    <p class="text-xl font-black text-gray-800">Rp {{ number_format($perjalanans->sum('jumlah_biaya'), 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400">{{ number_format($perjalanans->sum('vol_liter'), 1, ',', '.') }} liter total</p>
                </div>
            </div>
            <div class="card bg-white shadow-sm border border-gray-100">
                <div class="card-body p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Data Anomali</p>
                    <p class="text-3xl font-black {{ $totalAnomali > 0 ? 'text-[#CC0000]' : 'text-gray-800' }}">{{ $totalAnomali }}</p>
                    <p class="text-xs text-gray-400">dari {{ $perjalanans->count() }} trip</p>
                </div>
            </div>
            <div class="card bg-white shadow-sm border border-gray-100">
                <div class="card-body p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Fraud Risk Tinggi</p>
                    <p class="text-3xl font-black {{ $totalFraudTinggi > 0 ? 'text-[#CC0000]' : 'text-gray-800' }}">{{ $totalFraudTinggi }}</p>
                    <p class="text-xs text-gray-400">perlu tindak lanjut</p>
                </div>
            </div>
        </div>

        {{-- Section: Rekap Per Pegawai --}}
        <div class="flex items-center gap-2 mb-3">
            <div class="w-1 h-5 bg-[#CC0000] rounded-full"></div>
            <h2 class="font-bold text-xs text-gray-500 uppercase tracking-widest">Rekap Efisiensi Per Pegawai</h2>
        </div>
        <div class="card bg-white shadow-sm border border-gray-100 mb-6 overflow-x-auto">
            <table class="table table-sm w-full">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th class="text-center">Total Trip</th>
                        <th class="text-center">Anomali</th>
                        <th class="text-center">Total Jarak</th>
                        <th class="text-center">Total Biaya BBM</th>
                        <th class="text-center">Rata-rata Efisiensi <span class="normal-case font-normal text-gray-400">(excl. anomali)</span></th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapPegawai as $rekap)
                    <tr class="hover:bg-gray-50 border-b border-gray-50">
                        <td class="text-gray-400 text-xs">{{ $loop->iteration }}</td>
                        <td class="font-semibold text-gray-800">{{ $rekap['nama'] }}</td>
                        <td class="text-center">{{ $rekap['total_perjalanan'] }}x</td>
                        <td class="text-center">
                            @if($rekap['total_anomali'] > 0)
                                <span class="badge badge-sm badge-error gap-1">{{ $rekap['total_anomali'] }} anomali</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($rekap['total_jarak'], 0, ',', '.') }} km</td>
                        <td class="text-center">Rp {{ number_format($rekap['total_biaya'], 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($rekap['avg_efisiensi'] !== null)
                                <span class="font-bold">{{ number_format($rekap['avg_efisiensi'], 2, ',', '.') }}</span>
                                <span class="text-gray-400 text-xs">km/L</span>
                            @else
                                <span class="text-gray-300 text-xs italic">data tidak cukup</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php $s = $rekap['status'] ?? 'anomali'; @endphp
                            @if($s === 'balance')
                                <span class="badge badge-sm badge-success">Balance ✓</span>
                            @elseif($s === 'boros')
                                <span class="badge badge-sm badge-warning">Boros ⚠</span>
                            @elseif($s === 'anomali')
                                <span class="badge badge-sm badge-error">Anomali ⛔</span>
                            @else
                                <span class="text-gray-400 text-xs">{{ $s }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-300">Belum ada data perjalanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Section: Detail Perjalanan --}}
        <div class="flex items-center gap-2 mb-3">
            <div class="w-1 h-5 bg-[#CC0000] rounded-full"></div>
            <h2 class="font-bold text-xs text-gray-500 uppercase tracking-widest">Detail Semua Perjalanan</h2>
        </div>
        <div class="card bg-white shadow-sm border border-gray-100 overflow-x-auto">
            <table class="table table-xs w-full">
                <thead class="text-xs text-gray-500 uppercase tracking-wide">
                    <tr class="border-b border-gray-200">
                        <th rowspan="2" class="bg-gray-50 align-middle">No</th>
                        <th rowspan="2" class="bg-gray-50 align-middle">Tanggal</th>
                        <th rowspan="2" class="bg-gray-50 align-middle">Pegawai</th>
                        <th rowspan="2" class="bg-gray-50 align-middle">Tujuan</th>
                        <th rowspan="2" class="bg-gray-50 align-middle">Kendaraan</th>
                        <th rowspan="2" class="bg-gray-50 align-middle">No Pol</th>
                        <th colspan="3" class="bg-blue-50 text-blue-600 text-center border-b border-blue-100">Odometer</th>
                        <th rowspan="2" class="bg-gray-50 align-middle">Vol (L)</th>
                        <th colspan="3" class="bg-amber-50 text-amber-600 text-center border-b border-amber-100">Bon BBM</th>
                        <th rowspan="2" class="bg-gray-50 align-middle">Foto</th>
                        <th rowspan="2" class="bg-gray-50 align-middle">Efisiensi</th>
                        <th rowspan="2" class="bg-gray-50 align-middle">Status</th>
                        <th colspan="2" class="bg-red-50 text-red-600 text-center border-b border-red-100">Fraud Detection</th>
                        <th rowspan="2" class="bg-gray-50 align-middle">Aksi</th>
                    </tr>
                    <tr class="border-b-2 border-gray-200">
                        <th class="bg-blue-50 text-blue-500">KM Lama</th>
                        <th class="bg-blue-50 text-blue-500">KM Baru</th>
                        <th class="bg-blue-50 text-blue-500">Jarak</th>
                        <th class="bg-amber-50 text-amber-500">No Bon</th>
                        <th class="bg-amber-50 text-amber-500">Harga/L</th>
                        <th class="bg-amber-50 text-amber-500">Jumlah</th>
                        <th class="bg-red-50 text-red-400">Risk</th>
                        <th class="bg-red-50 text-red-400">Flags</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($perjalanans as $p)
                    @php
                        $score = $p->fraud_score ?? 0;
                        $risk  = $score > 50 ? 'tinggi'
                               : ($score > 30 ? 'mencurigakan'
                               : ($score > 0  ? 'perhatian' : 'aman'));
                        $flags = $p->fraud_flags ?? [];
                        $rowBg = match($risk) {
                            'tinggi'       => 'bg-red-50',
                            'mencurigakan' => 'bg-orange-50',
                            default        => '',
                        };
                    @endphp
                    <tr class="hover border-b border-gray-50 {{ $rowBg }}">
                        <td class="text-gray-400">{{ $loop->iteration }}</td>
                        <td class="whitespace-nowrap">{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td class="font-medium text-gray-800 whitespace-nowrap">{{ $p->pegawai->nama ?? '-' }}</td>
                        <td class="min-w-[120px]">
                            {{ $p->tujuan }}
                            @if($p->uraian)
                                <br><span class="text-gray-400">{{ $p->uraian }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $p->kendaraan->tipe ?? '-' }}</td>
                        <td class="text-center font-mono">{{ $p->kendaraan->nomor_polisi ?? $p->kendaraan->plat_nomor ?? '-' }}</td>
                        <td class="text-center font-mono">{{ number_format($p->km_lama, 0, ',', '.') }}</td>
                        <td class="text-center font-mono">{{ number_format($p->km_baru, 0, ',', '.') }}</td>
                        <td class="text-center font-mono font-bold">{{ number_format($p->jarak, 0, ',', '.') }} km</td>
                        <td class="text-center">{{ number_format($p->vol_liter, 2, ',', '.') }}</td>
                        <td class="text-center text-gray-400 font-mono">{{ $p->no_bon ?? '—' }}</td>
                        <td class="text-center">Rp {{ number_format($p->harga_per_liter, 0, ',', '.') }}</td>
                        <td class="text-center font-bold">Rp {{ number_format($p->jumlah_biaya, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($p->foto_bon)
                                <a href="{{ asset('storage/' . $p->foto_bon) }}" target="_blank" class="link link-primary text-xs">Lihat</a>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="text-center font-bold">
                            {{ number_format($p->efisiensi, 2, ',', '.') }}
                            <span class="text-gray-400 font-normal">km/L</span>
                        </td>
                        <td class="text-center">
                            @if($p->status_efisiensi === 'balance')
                                <span class="badge badge-sm badge-success">Balance</span>
                            @elseif($p->status_efisiensi === 'boros')
                                <span class="badge badge-sm badge-warning">Boros</span>
                            @else
                                <span class="badge badge-sm badge-error">Anomali</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($risk === 'aman')
                                <span class="badge badge-sm badge-success">Aman ✓</span>
                            @elseif($risk === 'perhatian')
                                <span class="badge badge-sm badge-warning">Perhatian</span>
                            @elseif($risk === 'mencurigakan')
                                <span class="badge badge-sm badge-warning text-orange-600">Curiga ⚠</span>
                            @else
                                <span class="badge badge-sm badge-error">Tinggi ⛔</span>
                            @endif
                            <br><span class="text-gray-300 text-xs">skor {{ $score }}</span>
                        </td>
                        <td class="min-w-[140px]">
                            @if(count($flags) > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($flags as $flag)
                                    @php
                                        $label = match($flag) {
                                            'nominal_bon_tidak_ganjil'             => '❌ Bon genap',
                                            'no_bon_duplikat'                      => '❌ Bon duplikat',
                                            'odometer_mundur'                      => '❌ Odo mundur',
                                            'jarak_melebihi_batas_harian'          => '⚠ Jarak >batas',
                                            'efisiensi_terlalu_tinggi_vs_historis' => '⚠ Eff tinggi',
                                            'efisiensi_terlalu_rendah_vs_historis' => '⚠ Eff rendah',
                                            'efisiensi_di_luar_batas_mutlak'       => '⚠ Eff abnormal',
                                            default => $flag,
                                        };
                                    @endphp
                                    <span class="badge badge-xs badge-error badge-outline" title="{{ $flag }}">{{ $label }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="text-center whitespace-nowrap">
                            <a href="{{ route('perjalanan.edit', $p->id) }}" class="btn btn-xs btn-ghost text-blue-500">Edit</a>
                            <form action="{{ route('perjalanan.destroy', $p->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-ghost text-red-400"
                                    onclick="return confirm('Yakin hapus data ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="19" class="text-center py-10 text-gray-300">
                            Belum ada data.
                            <a href="{{ route('perjalanan.create') }}" class="link text-[#CC0000]">Tambah sekarang →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold text-gray-600 border-t-2 border-gray-200">
                        <td colspan="9" class="text-right py-3 px-4">Total keseluruhan:</td>
                        <td class="text-center py-3">{{ number_format($perjalanans->sum('vol_liter'), 2, ',', '.') }} L</td>
                        <td colspan="2"></td>
                        <td class="text-center py-3">Rp {{ number_format($perjalanans->sum('jumlah_biaya'), 0, ',', '.') }}</td>
                        <td colspan="6"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    <footer class="text-center text-xs text-gray-300 py-6 mt-4">
        PT Telkom Akses Binjai &mdash; Sistem Informasi Pengelolaan Biaya BBM Kendaraan Operasional
    </footer>

</body>
</html>