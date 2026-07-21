{{--
  resources/views/perjalanan/index.blade.php
  Pakai layout global: resources/views/layouts/app.blade.php
  Navbar, Footer, Background sudah dari app.blade.php — tidak perlu tulis ulang.
--}}
@extends('layout.app')

@section('title', 'Data Perjalanan BBM')

{{-- ============================================================
     CSS KHUSUS HALAMAN PERJALANAN SAJA
     ============================================================ --}}
@push('styles')
@vite('resources/Tema/perjalanan/perjalanan.css')
@endpush

{{-- ============================================================
     KONTEN HALAMAN
     ============================================================ --}}
@section('content')
<div class="main-content">

  {{-- ── Flash Messages ── --}}
  @if(session('success'))
  <div class="flash flash-success anim-up">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
  </div>
  @endif
  @if(session('warning'))
  <div class="flash flash-warning anim-up">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01"/>
    </svg>
    {{ session('warning') }}
  </div>
  @endif
  @if(session('error'))
  <div class="flash flash-error anim-up">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
      <circle cx="12" cy="12" r="10"/>
      <path d="M15 9l-6 6M9 9l6 6"/>
    </svg>
    {{ session('error') }}
  </div>
  @endif

  {{-- ── Page Header ── --}}
  @php
    $exportDefaultDate = $perjalanans->sortByDesc('tanggal')->first()?->tanggal ?? now();
    $exportStartYear = max(now()->year, $exportDefaultDate->year);
  @endphp
  <div class="page-header">
    <div>
      <p class="page-eyebrow">PT Telkom Akses Binjai</p>
      <h2 class="page-title">Data Perjalanan BBM</h2>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
      <form action="{{ route('perjalanan.export.excel') }}" method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <select name="bulan" required class="form-control" style="width:auto;min-width:130px">
          @foreach(range(1, 12) as $bulan)
            <option value="{{ $bulan }}" @selected((int) request('bulan', $exportDefaultDate->month) === $bulan)>
              {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
            </option>
          @endforeach
        </select>
        <select name="tahun" required class="form-control" style="width:auto;min-width:95px">
          @foreach(range($exportStartYear, $exportStartYear - 5) as $tahun)
            <option value="{{ $tahun }}" @selected((int) request('tahun', $exportDefaultDate->year) === $tahun)>{{ $tahun }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn-export">
          Export Excel
        </button>
      </form>
      <a href="{{ route('perjalanan.create') }}" class="btn-add">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah
      </a>
    </div>
  </div>

  {{-- ── Stat Cards ── --}}
  @php
    $totalAnomali       = $perjalanans->where('status_efisiensi', 'anomali')->count();
    $totalPerluVerifikasi = $perjalanans->filter(function ($p) {
        $flags = $p->fraud_flags ?? [];
        return ($flags['status_anomali'] ?? 'Normal') === 'Perlu Verifikasi';
    })->count();
    $totalStatusAnomali = $perjalanans->filter(function ($p) {
        $flags = $p->fraud_flags ?? [];
        return ($flags['status_anomali'] ?? 'Normal') === 'Anomali';
    })->count();
  @endphp
  <div class="stat-grid">
    <div class="stat-card anim-up delay-1">
      <p class="stat-label">Total Perjalanan</p>
      <p class="stat-value" data-count="{{ $perjalanans->count() }}">0</p>
      <p class="stat-sub">trip tercatat</p>
    </div>
    <div class="stat-card anim-up delay-2">
      <p class="stat-label">Total Biaya BBM</p>
      <p class="stat-rp" data-count-rp="{{ $perjalanans->sum('jumlah_biaya') }}">Rp 0</p>
      <p class="stat-sub">{{ number_format($perjalanans->sum('vol_liter'), 1, ',', '.') }} liter</p>
    </div>
    <div class="stat-card anim-up delay-3">
      <p class="stat-label">Perlu Verifikasi</p>
      <p class="stat-value {{ $totalPerluVerifikasi > 0 ? 'danger' : '' }}" data-count="{{ $totalPerluVerifikasi }}">0</p>
      <p class="stat-sub">dari {{ $perjalanans->count() }} trip</p>
    </div>
    <div class="stat-card anim-up delay-4">
      <p class="stat-label">Anomali</p>
      <p class="stat-value {{ $totalStatusAnomali > 0 ? 'danger' : '' }}" data-count="{{ $totalStatusAnomali }}">0</p>
      <p class="stat-sub">{{ $totalStatusAnomali > 0 ? 'perlu ditindaklanjuti' : 'tidak ada' }}</p>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════════ --}}
  {{-- REKAP PER PEGAWAI                                          --}}
  {{-- ══════════════════════════════════════════════════════════ --}}
  <div class="section-divider">
    <div class="divider-line-v"></div>
    <h2 class="divider-label">Rekap Efisiensi Per Pegawai</h2>
    <div class="divider-line-h"></div>
  </div>

  <div class="table-card">
    <div class="table-scroll">
      <table class="rekap-tbl">
        <thead>
          <tr>
            <th>#</th>
            <th>Nama Pegawai</th>
            <th class="center">Total Trip</th>
            <th class="center">Anomali</th>
            <th class="center">Total Jarak</th>
            <th class="center">Total Biaya BBM</th>
            <th class="center">Rata-rata Efisiensi <span style="font-weight:400;text-transform:none">(excl. anomali)</span></th>
            <th class="center">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rekapPegawai as $rekap)
          <tr>
            <td class="muted text-sm">{{ $loop->iteration }}</td>
            <td class="fw-bold" style="color:#111827">{{ $rekap['nama'] }}</td>
            <td class="center">{{ $rekap['total_perjalanan'] }}×</td>
            <td class="center">
              @if($rekap['total_anomali'] > 0)
                <span class="badge badge-red badge-pulse">{{ $rekap['total_anomali'] }} anomali</span>
              @else
                <span class="muted">—</span>
              @endif
            </td>
            <td class="center" style="font-family:'JetBrains Mono',monospace">
              {{ number_format($rekap['total_jarak'], 0, ',', '.') }} km
            </td>
            <td class="center" style="font-family:'JetBrains Mono',monospace">
              Rp {{ number_format($rekap['total_biaya'], 0, ',', '.') }}
            </td>
            <td class="center">
              @if($rekap['avg_efisiensi'] !== null)
                <span class="fw-bold" style="font-family:'JetBrains Mono',monospace">
                  {{ number_format($rekap['avg_efisiensi'], 2, ',', '.') }}
                </span>
                <span class="muted"> km/L</span>
              @else
                <span class="muted" style="font-style:italic">data tidak cukup</span>
              @endif
            </td>
            <td class="center">
              @php $s = $rekap['status'] ?? 'anomali'; @endphp
              @if($s === 'balance')
                <span class="badge badge-green">Balance ✓</span>
              @elseif($s === 'boros')
                <span class="badge badge-amber">Boros ⚠</span>
              @else
                <span class="badge badge-red badge-pulse">Anomali ⛔</span>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="empty-state">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════════ --}}
  {{-- DETAIL SEMUA PERJALANAN                                    --}}
  {{-- ══════════════════════════════════════════════════════════ --}}
  <div class="section-divider">
    <div class="divider-line-v"></div>
    <h2 class="divider-label">Detail Semua Perjalanan</h2>
    <div class="divider-line-h"></div>
  </div>

  {{-- Desktop Table --}}
  <div class="table-card trip-table-desktop">
    <div class="table-scroll">
      <table class="detail-tbl">
        <thead>
          <tr>
            <th class="top" rowspan="2">#</th>
            <th class="top" rowspan="2">Tanggal</th>
            <th class="top" rowspan="2">Pegawai</th>
            <th class="top" rowspan="2" style="min-width:110px">Tujuan</th>
            <th class="top" rowspan="2">Kendaraan</th>
            <th class="top" rowspan="2">No Pol</th>
            <th class="top bg-blue" colspan="3" style="text-align:center;border-bottom:1px solid #dbeafe">Odometer</th>
            <th class="top" rowspan="2" style="text-align:center">Vol (L)</th>
            <th class="top bg-amber" colspan="3" style="text-align:center;border-bottom:1px solid #fde68a">Bon BBM</th>
            <th class="top" rowspan="2" style="text-align:center">Foto</th>
            <th class="top" rowspan="2" style="text-align:center">Efisiensi</th>
            <th class="top" rowspan="2" style="text-align:center">Status<br>Efisiensi</th>
            <th class="top" rowspan="2" style="text-align:center">Detail<br>Validasi</th>
            <th class="top" rowspan="2" style="text-align:center;min-width:140px">Indikasi</th>
            <th class="top" rowspan="2" style="text-align:center">Aksi</th>
          </tr>
          <tr>
            <th class="sub bg-blue">KM Lama</th>
            <th class="sub bg-blue">KM Baru</th>
            <th class="sub bg-blue">Jarak</th>
            <th class="sub bg-amber">No Bon</th>
            <th class="sub bg-amber">Harga/L</th>
            <th class="sub bg-amber">Jumlah</th>
          </tr>
        </thead>
        <tbody>
          @forelse($perjalanans as $p)
          @php
            $flagsData      = $p->fraud_flags ?? [];
            $statusValidasi = $flagsData['status_anomali'] ?? 'Normal';
            $displayFlags   = $flagsData['display_flags'] ?? [];
            $rowCls         = match($statusValidasi){ 'Anomali' => 'row-red', 'Perlu Verifikasi' => 'row-amber', default => '' };
          @endphp
          <tr class="{{ $rowCls }}">
            <td class="muted" style="font-size:10px">{{ $loop->iteration }}</td>
            <td style="font-family:'JetBrains Mono',monospace;font-size:11px;color:#6b7280;white-space:nowrap">
              {{ $p->tanggal->format('d/m/Y') }}
            </td>
            <td style="font-weight:700;color:#111827;white-space:nowrap">{{ $p->pegawai->nama ?? '-' }}</td>
            <td>
              <span style="font-size:12px;font-weight:500;color:#374151">{{ $p->tujuan }}</span>
              @if($p->uraian)
                <p class="muted" style="margin:2px 0 0;white-space:normal">{{ Str::limit($p->uraian, 45) }}</p>
              @endif
            </td>
            <td class="center">{{ $p->kendaraan->tipe ?? '-' }}</td>
            <td style="font-family:'JetBrains Mono',monospace;text-align:center;font-size:11px">
              {{ $p->kendaraan->nomor_polisi ?? $p->kendaraan->plat_nomor ?? '-' }}
            </td>
            <td class="num">{{ number_format($p->km_lama, 0, ',', '.') }}</td>
            <td class="num">{{ number_format($p->km_baru, 0, ',', '.') }}</td>
            <td class="num fw-bold" style="color:#111827">
              {{ number_format($p->jarak, 0, ',', '.') }}
              <span class="muted" style="font-weight:400">km</span>
            </td>
            <td class="center" style="font-family:'JetBrains Mono',monospace">
              {{ number_format($p->vol_liter, 2, ',', '.') }}
            </td>
            <td class="center muted" style="font-size:11px">{{ $p->no_bon ?? '—' }}</td>
            <td class="num">Rp {{ number_format($p->harga_per_liter, 0, ',', '.') }}</td>
            <td class="num fw-bold" style="color:#111827">
              Rp {{ number_format($p->jumlah_biaya, 0, ',', '.') }}
            </td>
            <td class="center">
              @if($p->foto_bon)
                <a href="{{ asset('storage/' . $p->foto_bon) }}" target="_blank"
                   style="color:#2563eb;font-size:11px;text-decoration:underline">Lihat ↗</a>
              @else
                <span class="muted">—</span>
              @endif
            </td>
            <td class="center">
              <span style="font-family:'JetBrains Mono',monospace;font-weight:700;font-size:12px;color:#111827">
                {{ number_format($p->efisiensi, 2, ',', '.') }}
              </span>
              <span class="muted"> km/L</span>
            </td>
            <td class="center">
              @if($p->status_efisiensi === 'balance')
                <span class="badge badge-green">Balance</span>
              @elseif($p->status_efisiensi === 'boros')
                <span class="badge badge-amber">Boros</span>
              @else
                <span class="badge badge-red badge-pulse">Anomali</span>
              @endif
            </td>
            <td class="center">
              <span class="badge" style="cursor:pointer;background:#f3f4f6;color:#374151;border-color:#d1d5db" onclick="openModal({{ $loop->index }})">Detail</span>
            </td>
            <td>
              @if(count($displayFlags) > 0)
                <div style="display:flex;flex-wrap:wrap">
                  @foreach($displayFlags as $flag)
                  <span class="flag-chip">{{ $flag }}</span>
                  @endforeach
                </div>
              @else
                <span class="muted">—</span>
              @endif
            </td>
            <td style="white-space:nowrap;text-align:center">
              <a href="{{ route('perjalanan.edit', $p->id) }}" class="tbl-edit">Edit</a>
              <form action="{{ route('perjalanan.destroy', $p->id) }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Yakin hapus data ini?')" class="tbl-del">
                  Hapus
                </button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="19" class="empty-state">
              Belum ada data perjalanan.
              <a href="{{ route('perjalanan.create') }}">Tambah sekarang →</a>
            </td>
          </tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr class="tbl-footer">
            <td colspan="9" style="text-align:right;font-weight:400;color:#9ca3af">Total keseluruhan:</td>
            <td style="text-align:center;font-family:'JetBrains Mono',monospace">
              {{ number_format($perjalanans->sum('vol_liter'), 2, ',', '.') }} L
            </td>
            <td colspan="2"></td>
            <td class="num">Rp {{ number_format($perjalanans->sum('jumlah_biaya'), 0, ',', '.') }}</td>
            <td colspan="6"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  {{-- ── Mobile Cards ── --}}
  <div class="trip-cards-mobile">
    @forelse($perjalanans as $p)
    @php
      $flagsData2    = $p->fraud_flags ?? [];
      $statusVal2    = $flagsData2['status_anomali'] ?? 'Normal';
      $displayFlags2 = $flagsData2['display_flags'] ?? [];
      $cardCls       = $statusVal2 === 'Anomali' ? 'is-anomali' : ($statusVal2 === 'Perlu Verifikasi' ? 'is-verifikasi' : '');
    @endphp
    <div class="m-card {{ $cardCls }}">
      <div class="m-card-head">
        <div class="m-card-user">
          <div class="m-card-avatar">{{ strtoupper(substr($p->pegawai->nama ?? '-', 0, 1)) }}</div>
          <div>
            <div class="m-card-name">{{ $p->pegawai->nama ?? '-' }}</div>
            <div class="m-card-date">{{ $p->tanggal->format('d/m/Y') }} · {{ $p->kendaraan->plat_nomor ?? '-' }}</div>
          </div>
        </div>
        <div class="m-card-badges">
          @php
            $valBadge = '<span class="badge" style="background:#f3f4f6;color:#374151;border-color:#d1d5db">Detail</span>';
            $efBadge = match($p->status_efisiensi) {
              'balance' => '<span class="badge badge-green">Balance</span>',
              'boros' => '<span class="badge badge-amber">Boros</span>',
              default => '<span class="badge badge-red">Anomali</span>',
            };
          @endphp
          {!! $valBadge !!}
        </div>
      </div>
      <div class="m-card-body">
        <div class="m-card-row">
          <div class="m-card-field">
            <span class="m-card-label">Tujuan</span>
            <span class="m-card-value">{{ $p->tujuan }}</span>
          </div>
          <div class="m-card-field m-card-num">
            <span class="m-card-label">Jarak</span>
            <span class="m-card-value">{{ number_format($p->jarak, 0, ',', '.') }} km</span>
          </div>
        </div>
        <div class="m-card-row">
          <div class="m-card-field">
            <span class="m-card-label">Volume</span>
            <span class="m-card-value">{{ number_format($p->vol_liter, 2, ',', '.') }} L</span>
          </div>
          <div class="m-card-field m-card-num">
            <span class="m-card-label">Efisiensi</span>
            <span class="m-card-value">{!! $efBadge !!}</span>
          </div>
        </div>
        <div class="m-card-row">
          <div class="m-card-field">
            <span class="m-card-label">Biaya</span>
            <span class="m-card-value" style="font-weight:700">Rp {{ number_format($p->jumlah_biaya, 0, ',', '.') }}</span>
          </div>
          <div class="m-card-field m-card-num">
            <span class="m-card-label">KM</span>
            <span class="m-card-value" style="font-size:11px">{{ number_format($p->km_lama, 0, ',', '.') }} → {{ number_format($p->km_baru, 0, ',', '.') }}</span>
          </div>
        </div>
        @if(count($displayFlags2) > 0)
        <div class="m-card-flags">
          @foreach($displayFlags2 as $flag)
          <span class="flag-chip">{{ $flag }}</span>
          @endforeach
        </div>
        @endif
      </div>
      <div class="m-card-actions">
        <a href="{{ route('perjalanan.edit', $p->id) }}" class="m-btn m-btn-edit">Edit</a>
        <form action="{{ route('perjalanan.destroy', $p->id) }}" method="POST" style="flex:1;display:flex">
          @csrf
          @method('DELETE')
          <button type="submit" onclick="return confirm('Yakin hapus?')" class="m-btn m-btn-del">Hapus</button>
        </form>
        @if($p->foto_bon)
          <a href="{{ asset('storage/' . $p->foto_bon) }}" target="_blank" class="m-btn m-btn-photo">Foto</a>
        @endif
        <a href="javascript:void(0)" class="m-btn m-btn-detail" onclick="openModal({{ $loop->index }})">Detail</a>
      </div>
    </div>
    @empty
    <div class="empty-state">
      Belum ada data. <a href="{{ route('perjalanan.create') }}">Tambah sekarang →</a>
    </div>
    @endforelse
  </div>

{{-- ── Floating Add Button (Mobile) ── --}}
<a href="{{ route('perjalanan.create') }}" class="m-fab" aria-label="Tambah perjalanan">
  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
  </svg>
</a>

{{-- ── Modal Detail Pembuktian ── --}}
<div id="validasiModal" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-header">
      <h3>Detail Pembuktian</h3>
      <button class="modal-close" id="btnCloseModal">&times;</button>
    </div>
    <div class="modal-body" id="validasiModalBody">
    </div>
  </div>
</div>

</div>{{-- end .main-content --}}
@endsection

{{-- ============================================================
     JS KHUSUS HALAMAN INI
     (hamburger sudah di app.blade.php, tidak perlu di sini)
     ============================================================ --}}
@push('scripts')
<script>
const perjalananData = @json($perjalananJson->values());

function openModal(index) {
    const d = perjalananData[index];
    if (!d) { return; }

    let statusBadge;
    if (d.status_validasi === 'Anomali') {
        statusBadge = '<span class="badge badge-red">Anomali</span>';
    } else if (d.status_validasi === 'Perlu Verifikasi') {
        statusBadge = '<span class="badge badge-amber">Perlu Verifikasi</span>';
    } else {
        statusBadge = '<span class="badge badge-green">Normal</span>';
    }

    let efisiensiBadge;
    if (d.status_efisiensi === 'Balance') {
        efisiensiBadge = '<span class="badge badge-green">Balance</span>';
    } else if (d.status_efisiensi === 'Boros') {
        efisiensiBadge = '<span class="badge badge-amber">Boros</span>';
    } else {
        efisiensiBadge = '<span class="badge badge-red">Anomali</span>';
    }

    let timelineHtml = `<span>${d.timeline_status}</span>`;
    if (d.alasan_timeline) {
        timelineHtml += `<p style="margin:4px 0 0;font-size:11px;color:#6b7280">${d.alasan_timeline}</p>`;
    }

    let flagsHtml = '';
    if (d.display_flags && d.display_flags.length > 0) {
        flagsHtml = d.display_flags.map(f => `<span class="flag-chip">${f}</span>`).join(' ');
    } else {
        flagsHtml = '<span class="muted">—</span>';
    }

    const modalHtml = `
        <div class="detail-grid">
            <div class="detail-field">
                <span class="label">Tanggal</span>
                <span class="value mono">${d.tanggal}</span>
            </div>
            <div class="detail-field">
                <span class="label">Pegawai</span>
                <span class="value">${d.pegawai}</span>
            </div>
            <div class="detail-field">
                <span class="label">Nomor Polisi</span>
                <span class="value mono">${d.no_polisi}</span>
            </div>
            <div class="detail-field">
                <span class="label">Tujuan</span>
                <span class="value">${d.tujuan}</span>
            </div>
            <div class="detail-field detail-full">
                <span class="label">Uraian</span>
                <span class="value">${d.uraian}</span>
            </div>
            <div class="detail-field">
                <span class="label">KM Lama</span>
                <span class="value mono">${d.km_lama}</span>
            </div>
            <div class="detail-field">
                <span class="label">KM Baru</span>
                <span class="value mono">${d.km_baru}</span>
            </div>
            <div class="detail-field">
                <span class="label">Jarak Aktual</span>
                <span class="value mono">${d.jarak_aktual}</span>
            </div>
            <div class="detail-field">
                <span class="label">Volume BBM</span>
                <span class="value mono">${d.volume_bbm}</span>
            </div>
            <div class="detail-field">
                <span class="label">Efisiensi</span>
                <span class="value">${d.efisiensi} ${efisiensiBadge}</span>
            </div>
            <div class="detail-field">
                <span class="label">Nilai Sewajarnya</span>
                <span class="value mono">${d.nilai_sewajarnya}</span>
            </div>
            <div class="detail-field">
                <span class="label">Deviasi</span>
                <span class="value mono">${d.deviasi}</span>
            </div>
            <div class="detail-field detail-full">
                <span class="label">Timeline Kendaraan</span>
                <div class="value">${timelineHtml}</div>
            </div>
            <div class="detail-field">
                <span class="label">Detail Validasi</span>
                <span class="value">${statusBadge}</span>
            </div>
            <div class="detail-field">
                <span class="label">Flag</span>
                <div class="value">${flagsHtml}</div>
            </div>
        </div>
        <div class="detail-keterangan">
            <span class="label">Alasan / Pembuktian</span>
            <div class="value">${d.alasan}</div>
        </div>
    `;
    document.getElementById('validasiModalBody').innerHTML = modalHtml;
    document.getElementById('validasiModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('validasiModal');
    const btnClose = document.getElementById('btnCloseModal');

    function close() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    btnClose.addEventListener('click', close);

    modal.addEventListener('click', function (e) {
        if (e.target === this) close();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('open')) close();
    });
});

(function () {
  'use strict';

  /* ── COUNT-UP ── */
  function countUp(el, end, isRp) {
    const dur = 800, t0 = performance.now();
    (function step(now) {
      const p = Math.min((now - t0) / dur, 1);
      const e = 1 - Math.pow(1 - p, 3);
      const v = Math.round(end * e);
      el.textContent = isRp
        ? 'Rp ' + v.toLocaleString('id-ID')
        : v.toLocaleString('id-ID');
      if (p < 1) requestAnimationFrame(step);
    })(t0);
  }

  const statObs = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const card = entry.target;
      const ce   = card.querySelector('[data-count]');
      const rpe  = card.querySelector('[data-count-rp]');
      if (ce)  countUp(ce,  parseInt(ce.dataset.count),    false);
      if (rpe) countUp(rpe, parseInt(rpe.dataset.countRp), true);
      statObs.unobserve(card);
    });
  }, { threshold: 0.2 });
  document.querySelectorAll('.stat-card').forEach(c => statObs.observe(c));

})();
</script>
@endpush
