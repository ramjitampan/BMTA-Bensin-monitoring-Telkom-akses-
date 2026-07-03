{{--
  resources/views/perjalanan/index.blade.php
  Pakai layout global: resources/views/layouts/app.blade.php
  Navbar, Footer, Background sudah dari app.blade.php — tidak perlu tulis ulang.
--}}
@extends('layout.app')

@section('title', 'Data Perjalanan BBM')

{{-- ============================================================
     CSS KHUSUS HALAMAN PERJALANAN SAJA
     (navbar/footer/body sudah di app.blade.php, tidak perlu di sini)
     ============================================================ --}}
@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&display=swap');

/* ── WRAPPER ── */
.main-content {
  max-width: 1280px;
  margin: 0 auto;
  padding: 32px 24px;
}

/* ── PAGE HEADER ── */
.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 28px;
}
.page-eyebrow {
  font-size: 10px; font-weight: 700;
  letter-spacing: 0.18em; text-transform: uppercase;
  color: #9ca3af; margin: 0 0 4px;
}
.page-title {
  font-size: 22px; font-weight: 800;
  color: #111827; letter-spacing: -0.03em; margin: 0;
}
.btn-add-mobile {
  display: none;
  align-items: center; gap: 8px;
  background: #8B0000; color: #fff;
  font-size: 13px; font-weight: 600;
  padding: 10px 16px; border-radius: 10px;
  text-decoration: none; border: none;
  white-space: nowrap; flex-shrink: 0;
  cursor: pointer;
}
.btn-add-mobile svg { width: 14px; height: 14px; }
.btn-export {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border: none;
  border-radius: 10px;
  background: #15803d;
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  white-space: nowrap;
  cursor: pointer;
}
.btn-export:hover { background: #166534; }

/* ── STAT CARDS ── */
.stat-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-bottom: 32px;
}
.stat-card {
  background: #fff; border-radius: 14px;
  border: 1px solid #f0f0f0;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  padding: 20px 20px 16px;
  position: relative; overflow: hidden;
  transition: transform 0.15s, box-shadow 0.15s;
}
.stat-card::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: linear-gradient(90deg, #8B0000, #ef4444);
  transform: scaleX(0); transform-origin: left; transition: transform 0.3s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(139,0,0,0.1); }
.stat-card:hover::before { transform: scaleX(1); }
.stat-label {
  font-size: 10px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.16em;
  color: #9ca3af; margin: 0 0 10px;
}
.stat-value {
  font-size: 28px; font-weight: 800;
  letter-spacing: -0.04em; color: #7f1d1d;
  margin: 0 0 4px; line-height: 1;
}
.stat-value.danger { color: #dc2626; }
.stat-sub  { font-size: 11px; color: #9ca3af; margin: 0; }
.stat-rp   { font-size: 19px; font-weight: 800; letter-spacing: -0.02em; color: #7f1d1d; margin: 0 0 4px; line-height: 1.2; }

/* ── SECTION DIVIDER ── */
.section-divider {
  display: flex; align-items: center; gap: 10px; margin-bottom: 14px;
}
.divider-line-v { width: 3px; height: 18px; background: #8B0000; border-radius: 3px; flex-shrink: 0; }
.divider-label  { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.18em; color: #6b7280; }
.divider-line-h { flex: 1; height: 1px; background: #f0f0f0; }

/* ── TABLE CARD ── */
.table-card {
  background: #fff; border-radius: 16px;
  border: 1px solid #f0f0f0;
  box-shadow: 0 1px 6px rgba(0,0,0,0.05);
  overflow: hidden; margin-bottom: 28px;
}
.table-scroll { overflow-x: auto; max-height: 520px; overflow-y: auto; }
.table-scroll::-webkit-scrollbar        { width: 4px; height: 4px; }
.table-scroll::-webkit-scrollbar-track  { background: #fafafa; }
.table-scroll::-webkit-scrollbar-thumb  { background: #fca5a5; border-radius: 4px; }
.table-scroll::-webkit-scrollbar-thumb:hover { background: #8B0000; }

/* ── REKAP TABLE ── */
.rekap-tbl { width: 100%; border-collapse: collapse; font-size: 12px; }
.rekap-tbl thead { background: #fafafa; }
.rekap-tbl th {
  padding: 12px 14px; text-align: left;
  font-size: 9.5px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.12em; color: #9ca3af;
  border-bottom: 1px solid #f0f0f0; white-space: nowrap;
}
.rekap-tbl th.center { text-align: center; }
.rekap-tbl td { padding: 12px 14px; border-bottom: 1px solid #fafafa; vertical-align: middle; }
.rekap-tbl tbody tr:hover td { background: rgba(139,0,0,0.02); }
.rekap-tbl tbody tr:last-child td { border-bottom: none; }

/* ── DETAIL TABLE ── */
.detail-tbl { width: 100%; border-collapse: collapse; font-size: 11.5px; }
.detail-tbl thead th {
  padding: 10px 12px; font-size: 9px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.1em; white-space: nowrap; position: sticky; background: #fafafa;
  border-bottom: 1px solid #f0f0f0; color: #9ca3af; text-align: left; vertical-align: middle;
}
.detail-tbl thead th.top    { top: 0; z-index: 6; }
.detail-tbl thead th.sub    { top: 37px; z-index: 5; }
.detail-tbl thead th.bg-blue  { background: #eff6ff; color: #3b82f6; }
.detail-tbl thead th.bg-amber { background: #fffbeb; color: #d97706; }
.detail-tbl thead th.bg-red   { background: #fef2f2; color: #ef4444; }
.detail-tbl td { padding: 11px 12px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
.detail-tbl tbody tr:hover td  { background: rgba(139,0,0,0.025); }
.detail-tbl tbody tr.row-red   td { background: rgba(220,38,38,0.04); }
.detail-tbl tbody tr.row-amber td { background: rgba(245,158,11,0.04); }

/* ── BADGES ── */
.badge {
  display: inline-flex; align-items: center; gap: 3px;
  padding: 3px 8px; border-radius: 5px;
  font-size: 10px; font-weight: 600;
  white-space: nowrap; border: 1px solid transparent;
}
.badge-green  { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.badge-amber  { background: #fffbeb; color: #b45309; border-color: #fde68a; }
.badge-red    { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
.badge-orange { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
.badge-yellow { background: #fefce8; color: #a16207; border-color: #fef08a; }
@keyframes pulse-badge { 0%,100%{opacity:1} 50%{opacity:0.5} }
.badge-pulse  { animation: pulse-badge 1.8s ease-in-out infinite; }

/* ── FRAUD FLAGS ── */
.flag-chip {
  display: inline-block;
  background: #fef2f2; color: #be123c;
  border: 1px solid #fecdd3; border-radius: 4px;
  padding: 2px 6px; font-size: 9.5px;
  font-family: 'JetBrains Mono', monospace;
  white-space: nowrap; margin: 1px;
}

/* ── RISK BAR ── */
.risk-bar-wrap { display: flex; align-items: center; gap: 5px; }
.risk-bar-bg   { width: 40px; height: 4px; background: #f3f4f6; border-radius: 2px; overflow: hidden; }
.risk-bar-fill { height: 100%; border-radius: 2px; transition: width 0.6s ease; }
.risk-score    { font-size: 9.5px; color: #9ca3af; font-family: 'JetBrains Mono', monospace; }

/* ── TABLE ACTION BUTTONS ── */
.tbl-edit {
  display: inline-block; padding: 5px 10px;
  font-size: 11px; font-weight: 600;
  color: #2563eb; border: 1px solid #bfdbfe; border-radius: 7px;
  text-decoration: none; transition: background 0.1s;
  background: #fff; margin-right: 4px;
}
.tbl-edit:hover { background: #eff6ff; }
.tbl-del {
  padding: 5px 10px; font-size: 11px; font-weight: 600;
  color: #dc2626; border: 1px solid #fecaca; border-radius: 7px;
  background: #fff; cursor: pointer; transition: background 0.1s;
}
.tbl-del:hover { background: #fef2f2; }

/* ── TABLE FOOTER ── */
.tbl-footer td {
  padding: 12px; background: #fafafa;
  font-size: 12px; font-weight: 700;
  color: #374151; border-top: 2px solid #f0f0f0;
}

/* ── FLASH MESSAGES ── */
.flash {
  display: flex; align-items: center; gap: 10px;
  border-radius: 10px; padding: 12px 16px;
  font-size: 13px; margin-bottom: 20px; border: 1px solid;
}
.flash-success { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.flash-warning { background: #fffbeb; color: #92400e; border-color: #fde68a; }
.flash-error   { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
.flash svg     { width: 15px; height: 15px; flex-shrink: 0; }

/* ── EMPTY STATE ── */
.empty-state { padding: 48px 20px; text-align: center; color: #d1d5db; }
.empty-state a { color: #8B0000; text-decoration: underline; }

/* ── MOBILE CARDS ── */
.trip-cards-mobile { display: none; }
.trip-card {
  background: #fff; border-radius: 12px;
  border: 1px solid #f0f0f0;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  padding: 16px; margin-bottom: 10px;
}
.trip-card.risk-high { border-left: 4px solid #dc2626; }
.trip-card.risk-mid  { border-left: 4px solid #f59e0b; }
.trip-card-top {
  display: flex; justify-content: space-between;
  gap: 10px; margin-bottom: 12px;
}
.trip-card-name { font-weight: 700; font-size: 14px; color: #111827; }
.trip-card-sub  { font-size: 11px; color: #6b7280; margin-top: 2px; }
.trip-card-badges {
  display: flex; flex-direction: column;
  align-items: flex-end; gap: 4px; flex-shrink: 0;
}
.trip-card-grid {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 10px 16px; margin-bottom: 12px;
}
.trip-card-field span:first-child { display: block; font-size: 10px; color: #9ca3af; margin-bottom: 2px; }
.trip-card-field span:last-child  { font-size: 12px; font-weight: 600; color: #111827; }
.trip-card-flags { display: flex; flex-wrap: wrap; gap: 3px; margin-bottom: 12px; }
.trip-card-actions {
  display: flex; gap: 8px;
  border-top: 1px solid #f5f5f5; padding-top: 12px;
}
.btn-edit, .btn-del, .btn-photo {
  flex: 1; padding: 9px; text-align: center;
  border-radius: 8px; font-size: 12px; font-weight: 600;
  text-decoration: none; cursor: pointer; border: none;
}
.btn-edit  { color: #2563eb; border: 1px solid #bfdbfe; background: #fff; }
.btn-del   { color: #dc2626; border: 1px solid #fecaca; background: #fff; }
.btn-photo { color: #6b7280; border: 1px solid #f0f0f0; background: #fff; }

/* ── HELPERS ── */
.num      { text-align: right; font-family: 'JetBrains Mono', monospace; }
.center   { text-align: center; }
.muted    { color: #9ca3af; font-size: 10px; }
.fw-bold  { font-weight: 700; }
.text-sm  { font-size: 12px; }

/* ── ANIMATIONS ── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}
.anim-up { animation: fadeUp 0.4s ease both; }
.delay-1 { animation-delay: 0.05s; }
.delay-2 { animation-delay: 0.10s; }
.delay-3 { animation-delay: 0.15s; }
.delay-4 { animation-delay: 0.20s; }

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
  .main-content        { padding: 20px 16px; }
  .page-header         { flex-direction: column; align-items: stretch; }
  .btn-add-mobile      { display: inline-flex; }
  .stat-grid           { grid-template-columns: 1fr 1fr; gap: 10px; }
  .stat-value          { font-size: 24px; }
  .stat-rp             { font-size: 16px; }
  .trip-table-desktop  { display: none !important; }
  .trip-cards-mobile   { display: block; }
}
@media (max-width: 480px) {
  .stat-grid { grid-template-columns: 1fr 1fr; }
}
</style>
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
      <a href="{{ route('perjalanan.create') }}" class="btn-add-mobile">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah
      </a>
    </div>
  </div>

  {{-- ── Stat Cards ── --}}
  @php
    $totalAnomali     = $perjalanans->where('status_efisiensi', 'anomali')->count();
    $totalFraudTinggi = $perjalanans->where('fraud_score', '>', 50)->count();
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
      <p class="stat-label">Data Anomali</p>
      <p class="stat-value {{ $totalAnomali > 0 ? 'danger' : '' }}" data-count="{{ $totalAnomali }}">0</p>
      <p class="stat-sub">dari {{ $perjalanans->count() }} trip</p>
    </div>
    <div class="stat-card anim-up delay-4">
      <p class="stat-label">Fraud Risk Tinggi</p>
      <p class="stat-value {{ $totalFraudTinggi > 0 ? 'danger' : '' }}" data-count="{{ $totalFraudTinggi }}">0</p>
      <p class="stat-sub">perlu tindak lanjut</p>
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
            <th class="top" rowspan="2" style="text-align:center">Status</th>
            <th class="top bg-red" colspan="2" style="text-align:center;border-bottom:1px solid #fecaca">Fraud Detection</th>
            <th class="top" rowspan="2" style="text-align:center">Aksi</th>
          </tr>
          <tr>
            <th class="sub bg-blue">KM Lama</th>
            <th class="sub bg-blue">KM Baru</th>
            <th class="sub bg-blue">Jarak</th>
            <th class="sub bg-amber">No Bon</th>
            <th class="sub bg-amber">Harga/L</th>
            <th class="sub bg-amber">Jumlah</th>
            <th class="sub bg-red">Risk</th>
            <th class="sub bg-red" style="min-width:140px">Flags</th>
          </tr>
        </thead>
        <tbody>
          @forelse($perjalanans as $p)
          @php
            $score  = $p->fraud_score ?? 0;
            $risk   = $score > 50 ? 'high' : ($score > 30 ? 'mid' : ($score > 0 ? 'low' : 'safe'));
            $flags  = $p->fraud_flags ?? [];
            $rowCls = match($risk){ 'high' => 'row-red', 'mid' => 'row-amber', default => '' };
            $barClr = match($risk){ 'high' => '#dc2626', 'mid' => '#f59e0b', 'low' => '#fbbf24', default => '#22c55e' };
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
              <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
                @if($risk === 'safe')
                  <span class="badge badge-green">Aman ✓</span>
                @elseif($risk === 'low')
                  <span class="badge badge-yellow">Perhatian</span>
                @elseif($risk === 'mid')
                  <span class="badge badge-orange">Curiga ⚠</span>
                @else
                  <span class="badge badge-red badge-pulse">Tinggi ⛔</span>
                @endif
                <div class="risk-bar-wrap">
                  <div class="risk-bar-bg">
                    <div class="risk-bar-fill" style="width:{{ min($score,100) }}%;background:{{ $barClr }}"></div>
                  </div>
                  <span class="risk-score">{{ $score }}</span>
                </div>
              </div>
            </td>
            <td>
              @if(count($flags) > 0)
                <div style="display:flex;flex-wrap:wrap">
                  @foreach($flags as $flag)
                  @php
                    $lbl = match($flag){
                      'nominal_bon_tidak_ganjil'             => 'Bon genap',
                      'no_bon_duplikat'                      => 'Bon duplikat',
                      'odometer_mundur'                      => 'Odo mundur',
                      'jarak_melebihi_batas_harian'          => 'Jarak >batas',
                      'efisiensi_terlalu_tinggi_vs_historis' => 'Eff↑',
                      'efisiensi_terlalu_rendah_vs_historis' => 'Eff↓',
                      'efisiensi_di_luar_batas_mutlak'       => 'Eff abnormal',
                      default => $flag,
                    };
                  @endphp
                  <span class="flag-chip">{{ $lbl }}</span>
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
      $score2 = $p->fraud_score ?? 0;
      $risk2  = $score2 > 50 ? 'high' : ($score2 > 30 ? 'mid' : 'safe');
      $flags2 = $p->fraud_flags ?? [];
    @endphp
    <div class="trip-card {{ $risk2 === 'high' ? 'risk-high' : ($risk2 === 'mid' ? 'risk-mid' : '') }}">
      <div class="trip-card-top">
        <div>
          <div class="trip-card-name">{{ $p->pegawai->nama ?? '-' }}</div>
          <div class="trip-card-sub">{{ $p->tanggal->format('d/m/Y') }} · {{ $p->tujuan }}</div>
        </div>
        <div class="trip-card-badges">
          @if($p->status_efisiensi === 'balance')
            <span class="badge badge-green">Balance</span>
          @elseif($p->status_efisiensi === 'boros')
            <span class="badge badge-amber">Boros</span>
          @else
            <span class="badge badge-red">Anomali</span>
          @endif
          @if($risk2 === 'high')
            <span class="badge badge-red badge-pulse">Fraud ⛔</span>
          @elseif($risk2 === 'mid')
            <span class="badge badge-orange">Curiga ⚠</span>
          @endif
        </div>
      </div>
      <div class="trip-card-grid">
        <div class="trip-card-field">
          <span>Kendaraan</span>
          <span>{{ $p->kendaraan->tipe ?? '-' }} — {{ $p->kendaraan->nomor_polisi ?? $p->kendaraan->plat_nomor ?? '-' }}</span>
        </div>
        <div class="trip-card-field">
          <span>Jarak</span>
          <span style="font-family:'JetBrains Mono',monospace">{{ number_format($p->jarak, 0, ',', '.') }} km</span>
        </div>
        <div class="trip-card-field">
          <span>Volume</span>
          <span style="font-family:'JetBrains Mono',monospace">{{ number_format($p->vol_liter, 2, ',', '.') }} L</span>
        </div>
        <div class="trip-card-field">
          <span>Efisiensi</span>
          <span style="font-family:'JetBrains Mono',monospace">{{ number_format($p->efisiensi, 2, ',', '.') }} km/L</span>
        </div>
        <div class="trip-card-field">
          <span>Biaya BBM</span>
          <span style="font-family:'JetBrains Mono',monospace">Rp {{ number_format($p->jumlah_biaya, 0, ',', '.') }}</span>
        </div>
        <div class="trip-card-field">
          <span>No Bon</span>
          <span style="font-family:'JetBrains Mono',monospace;color:#6b7280">{{ $p->no_bon ?? '—' }}</span>
        </div>
      </div>
      @if(count($flags2) > 0)
      <div class="trip-card-flags">
        @foreach($flags2 as $flag)
        @php
          $lbl2 = match($flag){
            'nominal_bon_tidak_ganjil'             => 'Bon genap',
            'no_bon_duplikat'                      => 'Bon duplikat',
            'odometer_mundur'                      => 'Odo mundur',
            'jarak_melebihi_batas_harian'          => 'Jarak >batas',
            'efisiensi_terlalu_tinggi_vs_historis' => 'Eff↑',
            'efisiensi_terlalu_rendah_vs_historis' => 'Eff↓',
            'efisiensi_di_luar_batas_mutlak'       => 'Eff abnormal',
            default => $flag,
          };
        @endphp
        <span class="flag-chip">{{ $lbl2 }}</span>
        @endforeach
      </div>
      @endif
      <div class="trip-card-actions">
        <a href="{{ route('perjalanan.edit', $p->id) }}" class="btn-edit">Edit</a>
        <form action="{{ route('perjalanan.destroy', $p->id) }}" method="POST" style="flex:1;display:flex">
          @csrf
          @method('DELETE')
          <button type="submit" onclick="return confirm('Yakin hapus?')" class="btn-del" style="flex:1">
            Hapus
          </button>
        </form>
        @if($p->foto_bon)
          <a href="{{ asset('storage/' . $p->foto_bon) }}" target="_blank" class="btn-photo">Foto ↗</a>
        @endif
      </div>
    </div>
    @empty
    <div class="empty-state">
      Belum ada data. <a href="{{ route('perjalanan.create') }}">Tambah sekarang →</a>
    </div>
    @endforelse
  </div>

</div>{{-- end .main-content --}}
@endsection

{{-- ============================================================
     JS KHUSUS HALAMAN INI
     (hamburger sudah di app.blade.php, tidak perlu di sini)
     ============================================================ --}}
@push('scripts')
<script>
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

  /* ── RISK BAR ANIMATION ── */
  const barObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const fill = e.target;
      const w    = fill.style.width;
      fill.style.width = '0%';
      setTimeout(() => { fill.style.width = w; }, 200);
      barObs.unobserve(fill);
    });
  }, { threshold: 0.1 });
  document.querySelectorAll('.risk-bar-fill').forEach(b => barObs.observe(b));

})();
</script>
@endpush
