<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data BBM — PT Telkom Akses Binjai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; box-sizing: border-box; }
        body { background: #f3f4f6; min-height: 100vh; }
        input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; }

        /* ── Field base ── */
        .field {
            display: block; width: 100%;
            height: 2.625rem;
            border: 1.5px solid #e5e7eb; border-radius: 0.625rem;
            background: #fff; padding: 0 0.875rem;
            font-size: 0.875rem; color: #1f2937;
            transition: border-color .15s, box-shadow .15s;
            appearance: none;
        }
        .field::placeholder { color: #9ca3af; }
        .field:focus { outline: none; border-color: #CC0000; box-shadow: 0 0 0 3px rgba(204,0,0,.08); }
        .field-error    { border-color: #ef4444 !important; background: #fff5f5; }
        .field-readonly { background: #eff6ff; color: #2563eb; font-weight: 600; cursor: not-allowed; }
        select.field    { cursor: pointer; }
        textarea.field  { height: auto; padding: 0.625rem 0.875rem; resize: none; }

        /* ── Label ── */
        .label {
            display: block;
            font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.07em;
            color: #6b7280; margin-bottom: 0.375rem;
        }
        .label-badge    { font-size: 0.65rem; font-weight: 500; text-transform: none; letter-spacing: 0; color: #9ca3af; margin-left: 0.25rem; }
        .label-required { color: #ef4444; margin-left: 0.125rem; }

        /* ── Error / hint ── */
        .error-msg { margin-top: 0.25rem; font-size: 0.72rem; color: #ef4444; }
        .hint-msg  { margin-top: 0.3rem;  font-size: 0.72rem; color: #9ca3af; line-height: 1.5; }

        /* ── Card ── */
        .card {
            background: #fff; border-radius: 1rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
            padding: 1.375rem;
        }
        .card-title {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.68rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.1em; color: #CC0000;
            padding-bottom: 0.875rem; margin-bottom: 1.125rem;
            border-bottom: 1px solid #f3f4f6;
        }
        .card-title svg { width: 14px; height: 14px; flex-shrink: 0; }

        /* ── Chip ── */
        .chip {
            display: inline-flex; align-items: center; gap: 0.3rem;
            font-size: 0.7rem; font-weight: 500;
            padding: 0.25rem 0.625rem; border-radius: 9999px; margin-top: 0.375rem;
        }
        .chip-blue   { background: #eff6ff; color: #1d4ed8; }
        .chip-green  { background: #f0fdf4; color: #16a34a; }
        .chip-red    { background: #fef2f2; color: #dc2626; }
        .chip-amber  { background: #fffbeb; color: #d97706; }

        /* ── Alert ── */
        .alert { border-radius: 0.625rem; padding: 0.75rem 0.875rem; font-size: 0.75rem; line-height: 1.65; }
        .alert-amber { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
        .alert-blue  { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
        .alert-red   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
        .alert-green { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

        /* ── Step wizard ── */
        .wizard { display: flex; border-radius: 0.875rem; overflow: hidden; border: 1.5px solid #e5e7eb; background: #fff; }
        .wizard-step {
            flex: 1; display: flex; align-items: center; gap: 0.625rem;
            padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 500;
            color: #9ca3af; cursor: pointer;
            border-right: 1.5px solid #e5e7eb;
            transition: background .2s, color .2s;
        }
        .wizard-step:last-child { border-right: none; }
        .wizard-step.is-active  { background: #CC0000; color: #fff; }
        .wizard-step.is-done    { background: #f0fdf4; color: #16a34a; }
        .wizard-num {
            width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem; font-weight: 700; border: 1.5px solid currentColor;
        }
        .wizard-step.is-active .wizard-num { background: #fff; color: #CC0000; border-color: #fff; }
        .wizard-step.is-done   .wizard-num { background: #16a34a; color: #fff; border-color: #16a34a; }

        /* ── Progress ── */
        .progress-track { height: 3px; background: #e5e7eb; border-radius: 9999px; overflow: hidden; }
        .progress-fill  { height: 100%; background: #CC0000; border-radius: 9999px; transition: width .35s ease; }

        /* ── Preview cards ── */
        .preview-wrap { display: none; border-radius: 0.875rem; border: 1px solid #bfdbfe; background: #eff6ff; padding: 1rem; }
        .preview-wrap.is-visible { display: block; animation: fadeUp .2s ease; }
        .preview-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.625rem; margin-top: 0.75rem; }
        .pv-card {
            background: #fff; border-radius: 0.75rem;
            border: 1.5px solid #dbeafe; padding: 0.75rem;
            text-align: center; transition: border-color .2s;
        }
        .pv-label { font-size: 0.65rem; color: #9ca3af; margin-bottom: 0.25rem; }
        .pv-value { font-size: 1rem; font-weight: 700; color: #1f2937; transition: color .2s; }

        /* ── Foto preview ── */
        .foto-preview { display: none; margin-top: 0.75rem; border-radius: 0.75rem; overflow: hidden; border: 1.5px solid #e5e7eb; max-height: 180px; object-fit: cover; width: 100%; }
        .foto-preview.is-visible { display: block; }

        /* ── Buttons ── */
        .btn-primary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: #CC0000; color: #fff;
            font-size: 0.875rem; font-weight: 600;
            padding: 0.625rem 1.375rem; border-radius: 0.75rem;
            border: none; cursor: pointer; transition: background .15s, transform .1s;
        }
        .btn-primary:hover  { background: #a80000; }
        .btn-primary:active { transform: scale(.98); }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 0.375rem;
            background: #fff; color: #6b7280;
            font-size: 0.875rem; font-weight: 500;
            padding: 0.625rem 1rem; border-radius: 0.75rem;
            border: 1.5px solid #e5e7eb; cursor: pointer;
            text-decoration: none; transition: background .15s;
        }
        .btn-secondary:hover { background: #f9fafb; }

        /* ── Section ── */
        .section { animation: fadeUp .22s ease both; }
        .section.is-hidden { display: none !important; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Grid ── */
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        @media (max-width: 560px) {
            .grid-2 { grid-template-columns: 1fr; }
            .btn-primary, .btn-secondary { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

{{-- ══════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════ --}}
<nav style="background:#CC0000;position:sticky;top:0;z-index:30;box-shadow:0 2px 8px rgba(0,0,0,.18);">
    <div style="max-width:44rem;margin:0 auto;padding:0 1.25rem;height:3.5rem;display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:0.75rem;">
            <div style="width:32px;height:32px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="color:#CC0000;font-weight:900;font-size:.65rem;">TA</span>
            </div>
            <div>
                <p style="color:#fff;font-weight:700;font-size:.875rem;margin:0;">Telkom Akses Binjai</p>
                <p style="color:rgba(255,255,255,.65);font-size:.7rem;margin:0;">Edit Data Perjalanan &amp; BBM</p>
            </div>
        </div>
        <a href="{{ route('perjalanan.index') }}"
           style="color:rgba(255,255,255,.85);font-size:.75rem;border:1px solid rgba(255,255,255,.3);border-radius:.5rem;padding:.35rem .875rem;text-decoration:none;transition:background .15s;"
           onmouseover="this.style.background='rgba(255,255,255,.12)'"
           onmouseout="this.style.background='transparent'">← Kembali</a>
    </div>
</nav>

<main style="max-width:44rem;margin:0 auto;padding:1.5rem 1.25rem 3rem;">

    {{-- Global validation errors --}}
    @if($errors->any())
    <div class="alert alert-red" style="margin-bottom:1.25rem;">
        <p style="font-weight:700;margin:0 0 .375rem;">⚠ {{ $errors->count() }} kesalahan perlu diperbaiki:</p>
        <ul style="margin:0;padding-left:1.25rem;">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Info bar perjalanan yang diedit --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:1rem;padding:1rem 1.375rem;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
        <div>
            <p style="margin:0;font-size:.68rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;">Mengedit perjalanan</p>
            <p style="margin:.25rem 0 0;font-size:.9rem;font-weight:700;color:#111827;">
                {{ $perjalanan->kendaraan->plat_nomor ?? '-' }}
                <span style="font-weight:400;color:#6b7280;">·</span>
                {{ $perjalanan->pegawai->nama ?? '-' }}
            </p>
            <p style="margin:.125rem 0 0;font-size:.78rem;color:#6b7280;">
                {{ \Carbon\Carbon::parse($perjalanan->tanggal)->translatedFormat('d F Y') }}
                · {{ $perjalanan->tujuan }}
            </p>
        </div>
        @php
            $statusColor = match($perjalanan->status_efisiensi ?? '') {
                'normal'   => ['bg' => '#f0fdf4', 'text' => '#16a34a'],
                'anomali'  => ['bg' => '#fef2f2', 'text' => '#dc2626'],
                'rendah'   => ['bg' => '#fffbeb', 'text' => '#d97706'],
                default    => ['bg' => '#f3f4f6', 'text' => '#6b7280'],
            };
        @endphp
        <span style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};font-size:.7rem;font-weight:600;padding:.3rem .875rem;border-radius:9999px;">
            {{ ucfirst($perjalanan->status_efisiensi ?? 'belum dihitung') }}
        </span>
    </div>

    {{-- Progress bar --}}
    <div class="progress-track" style="margin-bottom:.875rem;">
        <div class="progress-fill" id="progressFill" style="width:33.33%"></div>
    </div>

    {{-- Step wizard --}}
    <div class="wizard" style="margin-bottom:1.25rem;">
        <div class="wizard-step is-active" id="wizStep1" onclick="goStep(1)">
            <div class="wizard-num" id="wizNum1">1</div>
            <span>Perjalanan</span>
        </div>
        <div class="wizard-step" id="wizStep2" onclick="goStep(2)">
            <div class="wizard-num" id="wizNum2">2</div>
            <span>Odometer</span>
        </div>
        <div class="wizard-step" id="wizStep3" onclick="goStep(3)">
            <div class="wizard-num" id="wizNum3">3</div>
            <span>Bon BBM</span>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         FORM
    ══════════════════════════════════════════════ --}}
    <form action="{{ route('perjalanan.update', $perjalanan) }}" method="POST" enctype="multipart/form-data" id="mainForm">
    @csrf
    @method('PUT')

    {{-- ════════════════════════════════════
         STEP 1 — INFORMASI PERJALANAN
    ════════════════════════════════════ --}}
    <div class="section" id="sec1">

        <div class="card" style="margin-bottom:1rem;">
            <div class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Informasi Perjalanan
            </div>

            <div class="grid-2" style="margin-bottom:1rem;">
                <div>
                    <label class="label" for="tanggal">Tanggal<span class="label-required">*</span></label>
                    <input
                        id="tanggal" type="date" name="tanggal"
                        value="{{ old('tanggal', $perjalanan->tanggal) }}"
                        class="field {{ $errors->has('tanggal') ? 'field-error' : '' }}">
                    @error('tanggal')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="pegawai_id">Pegawai<span class="label-required">*</span></label>
                    <select id="pegawai_id" name="pegawai_id" class="field {{ $errors->has('pegawai_id') ? 'field-error' : '' }}">
                        <option value="">— Pilih pegawai —</option>
                        @foreach($pegawais as $pg)
                            <option value="{{ $pg->id }}" {{ old('pegawai_id', $perjalanan->pegawai_id) == $pg->id ? 'selected' : '' }}>
                                {{ $pg->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('pegawai_id')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid-2" style="margin-bottom:1rem;">
                <div>
                    <label class="label" for="kendaraan_id">Kendaraan<span class="label-required">*</span></label>
                    <select id="kendaraan_id" name="kendaraan_id" class="field {{ $errors->has('kendaraan_id') ? 'field-error' : '' }}">
                        <option value="">— Pilih kendaraan —</option>
                        @foreach($kendaraans as $k)
                            <option value="{{ $k->id }}"
                                {{ old('kendaraan_id', $perjalanan->kendaraan_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->plat_nomor }} — {{ $k->merk }} ({{ $k->jenis }})
                            </option>
                        @endforeach
                    </select>
                    @error('kendaraan_id')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="tujuan">Tujuan Perjalanan<span class="label-required">*</span></label>
                    <input
                        id="tujuan" type="text" name="tujuan"
                        value="{{ old('tujuan', $perjalanan->tujuan) }}"
                        placeholder="cth: Kantor Regional Medan"
                        class="field {{ $errors->has('tujuan') ? 'field-error' : '' }}">
                    @error('tujuan')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="label" for="uraian">Uraian Kegiatan<span class="label-badge">(opsional)</span></label>
                <textarea id="uraian" name="uraian" rows="2" placeholder="Keterangan tambahan..." class="field">{{ old('uraian', $perjalanan->uraian) }}</textarea>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;gap:.75rem;flex-wrap:wrap;">
            <a href="{{ route('perjalanan.index') }}" class="btn-secondary">← Batal</a>
            <button type="button" class="btn-primary" onclick="goStep(2)">
                Lanjut
                <svg xmlns="http://www.w3.org/2000/svg" style="width:.875rem;height:.875rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    {{-- ════════════════════════════════════
         STEP 2 — ODOMETER
    ════════════════════════════════════ --}}
    <div class="section is-hidden" id="sec2">

        <div class="card" style="margin-bottom:1rem;">
            <div class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                Data Odometer
            </div>

            {{-- Nilai sebelumnya sebagai referensi --}}
            <div class="alert alert-blue" style="margin-bottom:1rem;">
                ℹ Nilai saat ini: KM Awal <strong>{{ number_format($perjalanan->km_lama, 0, ',', '.') }}</strong> · KM Akhir <strong>{{ number_format($perjalanan->km_baru, 0, ',', '.') }}</strong> · Jarak <strong>{{ number_format($perjalanan->jarak, 0, ',', '.') }} km</strong>
            </div>

            <div class="grid-2">
                <div>
                    <label class="label" for="km_lama">KM Awal<span class="label-required">*</span></label>
                    <input
                        id="km_lama" type="number" name="km_lama"
                        value="{{ old('km_lama', $perjalanan->km_lama) }}"
                        min="0" step="1" placeholder="cth: 12500"
                        class="field {{ $errors->has('km_lama') ? 'field-error' : '' }}">
                    @error('km_lama')<p class="error-msg">{{ $message }}</p>@enderror
                    <p class="hint-msg">Baca odometer <strong>sebelum</strong> berangkat</p>
                </div>
                <div>
                    <label class="label" for="km_baru">KM Akhir<span class="label-required">*</span></label>
                    <input
                        id="km_baru" type="number" name="km_baru"
                        value="{{ old('km_baru', $perjalanan->km_baru) }}"
                        min="0" step="1" placeholder="cth: 12687"
                        class="field {{ $errors->has('km_baru') ? 'field-error' : '' }}">
                    @error('km_baru')<p class="error-msg">{{ $message }}</p>@enderror
                    <p class="hint-msg">Baca odometer <strong>setelah</strong> tiba</p>
                </div>
            </div>
        </div>

        {{-- Preview kalkulasi step 2 --}}
        <div class="preview-wrap" id="previewStep2" style="margin-bottom:1rem;">
            <p style="font-size:.68rem;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.08em;margin:0;">🔍 Preview Kalkulasi</p>
            <div class="preview-grid">
                <div class="pv-card"><div class="pv-label">Jarak Tempuh</div><div class="pv-value" id="pv2-jarak">—</div></div>
                <div class="pv-card"><div class="pv-label">Volume BBM</div><div class="pv-value" id="pv2-vol">—</div></div>
                <div class="pv-card" id="pv2-effCard"><div class="pv-label">Efisiensi</div><div class="pv-value" id="pv2-eff">—</div></div>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;gap:.75rem;flex-wrap:wrap;">
            <button type="button" class="btn-secondary" onclick="goStep(1)">← Kembali</button>
            <button type="button" class="btn-primary" onclick="goStep(3)">
                Lanjut
                <svg xmlns="http://www.w3.org/2000/svg" style="width:.875rem;height:.875rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    {{-- ════════════════════════════════════
         STEP 3 — BON BBM
    ════════════════════════════════════ --}}
    <div class="section is-hidden" id="sec3">

        <div class="card" style="margin-bottom:1rem;">
            <div class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                Data Bon BBM Pertamina
            </div>

            {{-- Nilai sebelumnya --}}
            <div class="alert alert-blue" style="margin-bottom:1rem;">
                ℹ Nilai saat ini: <strong>Rp {{ number_format($perjalanan->jumlah_biaya, 0, ',', '.') }}</strong>
                · {{ number_format($perjalanan->vol_liter, 2, ',', '.') }} L
                · Rp {{ number_format($perjalanan->harga_per_liter, 0, ',', '.') }}/L
            </div>

            <div class="grid-2" style="margin-bottom:1rem;">
                <div>
                    <label class="label" for="jumlah_biaya">Nominal Bon (Rp)<span class="label-required">*</span></label>
                    <input
                        id="jumlah_biaya" type="number" name="jumlah_biaya"
                        value="{{ old('jumlah_biaya', $perjalanan->jumlah_biaya) }}"
                        min="1000" step="1000" placeholder="cth: 101000"
                        class="field {{ $errors->has('jumlah_biaya') ? 'field-error' : '' }}">
                    @error('jumlah_biaya')<p class="error-msg">{{ $message }}</p>@enderror
                    <span id="nominalChip" class="chip" style="display:none;"></span>
                    <div class="alert alert-amber" style="margin-top:.625rem;">
                        <strong>⚠ Aturan Bon Lapangan</strong><br>
                        Nominal harus <strong>kelipatan Rp1.000</strong> — bukan kelipatan bulat Rp10.000<br>
                        <span style="color:#16a34a;">✓ Rp 51.000 · 52.000 · 101.000 · 127.000</span><br>
                        <span style="color:#dc2626;">✗ Rp 10.000 · 50.000 · 100.000</span>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:1rem;">
                    <div>
                        <label class="label" for="harga_per_liter">Harga per Liter (Rp)<span class="label-required">*</span></label>
                        <input
                            id="harga_per_liter" type="number" name="harga_per_liter"
                            value="{{ old('harga_per_liter', $perjalanan->harga_per_liter) }}"
                            min="1" step="1" placeholder="cth: 10000"
                            class="field {{ $errors->has('harga_per_liter') ? 'field-error' : '' }}">
                        @error('harga_per_liter')<p class="error-msg">{{ $message }}</p>@enderror
                        <p class="hint-msg">Sesuai harga Pertamina saat pengisian</p>
                    </div>
                    <div>
                        <label class="label" for="vol_liter_preview">
                            Volume BBM (L)
                            <span class="label-badge" style="color:#3b82f6;">dihitung otomatis</span>
                        </label>
                        <div style="position:relative;">
                            <input id="vol_liter_preview" type="number" readonly placeholder="—" class="field field-readonly" style="padding-right:2rem;">
                            <span style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);font-size:.72rem;color:#60a5fa;font-weight:700;pointer-events:none;">L</span>
                        </div>
                        <p class="hint-msg">= Nominal ÷ Harga per Liter</p>
                    </div>
                </div>
            </div>

            <div class="grid-2">
                <div>
                    <label class="label" for="no_bon">No. Bon / Struk<span class="label-badge">(opsional)</span></label>
                    <input
                        id="no_bon" type="text" name="no_bon"
                        value="{{ old('no_bon', $perjalanan->no_bon) }}"
                        placeholder="cth: TXN-20240601-001"
                        class="field {{ $errors->has('no_bon') ? 'field-error' : '' }}">
                    @error('no_bon')<p class="error-msg">{{ $message }}</p>@enderror
                    <p class="hint-msg">Untuk deteksi duplikasi bon</p>
                </div>
                <div>
                    <label class="label" for="foto_bon">
                        Foto Bon / Struk
                        <span class="label-badge">(opsional, maks 2MB)</span>
                    </label>
                    <input
                        id="foto_bon" type="file" name="foto_bon"
                        accept="image/jpg,image/jpeg,image/png"
                        class="field" style="height:auto;padding:.5rem .75rem;cursor:pointer;">
                    @error('foto_bon')<p class="error-msg">{{ $message }}</p>@enderror
                    <p class="hint-msg">Biarkan kosong untuk mempertahankan foto yang ada</p>

                    {{-- Foto existing --}}
                    @if($perjalanan->foto_bon)
                    <div style="margin-top:.75rem;">
                        <p style="font-size:.68rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 .375rem;">Foto saat ini:</p>
                        <img src="{{ Storage::url($perjalanan->foto_bon) }}"
                             alt="Foto bon"
                             style="width:100%;max-height:160px;object-fit:cover;border-radius:.75rem;border:1.5px solid #e5e7eb;">
                        <label style="display:flex;align-items:center;gap:.5rem;margin-top:.5rem;font-size:.75rem;color:#dc2626;cursor:pointer;">
                            <input type="checkbox" name="hapus_foto_bon" value="1" style="accent-color:#dc2626;">
                            Hapus foto yang ada
                        </label>
                    </div>
                    @endif

                    {{-- Preview foto baru --}}
                    <img id="fotoNewPreview" class="foto-preview" alt="Preview foto baru">
                </div>
            </div>
        </div>

        {{-- Preview kalkulasi step 3 --}}
        <div class="preview-wrap" id="previewStep3" style="margin-bottom:1rem;">
            <p style="font-size:.68rem;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.08em;margin:0;">🔍 Preview Kalkulasi</p>
            <div class="preview-grid">
                <div class="pv-card"><div class="pv-label">Jarak Tempuh</div><div class="pv-value" id="pv3-jarak">—</div></div>
                <div class="pv-card"><div class="pv-label">Volume BBM</div><div class="pv-value" id="pv3-vol">—</div></div>
                <div class="pv-card" id="pv3-effCard"><div class="pv-label">Efisiensi</div><div class="pv-value" id="pv3-eff">—</div></div>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;gap:.75rem;flex-wrap:wrap;">
            <button type="button" class="btn-secondary" onclick="goStep(2)">← Kembali</button>
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                <button type="submit" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('perjalanan.index') }}" class="btn-secondary">← Batal</a>
            </div>
        </div>
    </div>

    </form>
</main>

{{-- FOOTER --}}
<footer style="background:#111827;color:#9ca3af;margin-top:2rem;">
    <div style="max-width:44rem;margin:0 auto;padding:1.5rem 1.25rem;">
        <div style="display:flex;flex-wrap:wrap;align-items:flex-start;gap:1.25rem;">
            <div style="display:flex;flex-direction:column;align-items:center;gap:.5rem;">
                <div style="width:2.25rem;height:2.25rem;border-radius:50%;background:#CC0000;display:flex;align-items:center;justify-content:center;">
                    <span style="color:#fff;font-weight:900;font-size:.75rem;">TA</span>
                </div>
                <p style="color:#f9fafb;font-weight:600;font-size:.8rem;margin:0;text-align:center;">PT Telkom Akses</p>
                <p style="font-size:.7rem;margin:0;">Branch Binjai</p>
            </div>
            <div style="width:1px;background:#374151;align-self:stretch;"></div>
            <div>
                <p style="color:#f3f4f6;font-size:.875rem;font-weight:500;margin:0 0 .375rem;">Sistem Informasi Pengelolaan BBM</p>
                <p style="font-size:.78rem;line-height:1.6;margin:0;color:#6b7280;">Monitoring biaya bahan bakar kendaraan operasional<br>dengan deteksi anomali dan fraud detection otomatis.</p>
            </div>
        </div>
        <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid #1f2937;display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:.5rem;font-size:.7rem;">
            <p style="margin:0;">&copy; {{ date('Y') }} PT Telkom Akses Binjai. All rights reserved.</p>
            <p style="margin:0;color:#4b5563;">v1.0 &middot; TIF-2954/2026</p>
        </div>
    </div>
</footer>

{{-- ══════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════ --}}
<script>
/* ── State ── */
let currentStep = 1;

/* ══════════════════════════════════════════════
   WIZARD
══════════════════════════════════════════════ */
function goStep(n) {
    document.getElementById('sec' + currentStep).classList.add('is-hidden');
    currentStep = n;
    document.getElementById('sec' + n).classList.remove('is-hidden');
    updateWizard(n);
    updatePreview();
}

function updateWizard(active) {
    const total = 3;
    document.getElementById('progressFill').style.width = (active / total * 100) + '%';

    for (let i = 1; i <= total; i++) {
        const stepEl = document.getElementById('wizStep' + i);
        const numEl  = document.getElementById('wizNum' + i);
        stepEl.classList.remove('is-active', 'is-done');
        if (i === active) {
            stepEl.classList.add('is-active');
            numEl.textContent = i;
        } else if (i < active) {
            stepEl.classList.add('is-done');
            numEl.textContent = '✓';
        } else {
            numEl.textContent = i;
        }
    }
}

/* ══════════════════════════════════════════════
   VALIDASI NOMINAL
══════════════════════════════════════════════ */
function updateNominalChip() {
    const v    = parseInt(document.getElementById('jumlah_biaya').value, 10);
    const chip = document.getElementById('nominalChip');
    chip.style.display = 'none';
    chip.className = 'chip';

    if (!v || v < 1000) return;

    chip.style.display = 'inline-flex';
    if (v % 1000 !== 0) {
        chip.classList.add('chip-red');
        chip.textContent = '✕ Harus kelipatan Rp1.000';
    } else if (v % 10000 === 0) {
        chip.classList.add('chip-red');
        chip.textContent = '✕ Rp ' + v.toLocaleString('id-ID') + ' — nominal bulat, tidak valid!';
    } else {
        chip.classList.add('chip-green');
        chip.textContent = '✓ Rp ' + v.toLocaleString('id-ID') + ' — valid';
    }
}

/* ══════════════════════════════════════════════
   VOLUME — nominal ÷ harga
══════════════════════════════════════════════ */
function updateVolume() {
    const nominal = parseFloat(document.getElementById('jumlah_biaya').value);
    const harga   = parseFloat(document.getElementById('harga_per_liter').value);
    const field   = document.getElementById('vol_liter_preview');
    field.value   = (nominal > 0 && harga > 0) ? (nominal / harga).toFixed(2) : '';
}

/* ══════════════════════════════════════════════
   PREVIEW — hanya untuk UI
   Backend (Model Perjalanan) adalah source of truth.
══════════════════════════════════════════════ */
function updatePreview() {
    const kmLama  = parseFloat(document.getElementById('km_lama').value);
    const kmBaru  = parseFloat(document.getElementById('km_baru').value);
    const nominal = parseFloat(document.getElementById('jumlah_biaya').value);
    const harga   = parseFloat(document.getElementById('harga_per_liter').value);

    const jarak     = (!isNaN(kmLama) && !isNaN(kmBaru)) ? (kmBaru - kmLama) : 0;
    const volume    = (harga > 0 && nominal > 0) ? (nominal / harga) : 0;
    const efisiensi = (volume > 0 && jarak > 0) ? (jarak / volume) : 0;
    const show      = (jarak > 0 || volume > 0);

    renderPreviewBlock('previewStep2', 'pv2', jarak, volume, efisiensi, show);
    renderPreviewBlock('previewStep3', 'pv3', jarak, volume, efisiensi, show);
}

function renderPreviewBlock(wrapperId, prefix, jarak, volume, efisiensi, show) {
    const wrap = document.getElementById(wrapperId);
    if (!wrap) return;

    if (!show) { wrap.classList.remove('is-visible'); return; }
    wrap.classList.add('is-visible');

    const jarakEl   = document.getElementById(prefix + '-jarak');
    const volEl     = document.getElementById(prefix + '-vol');
    const effEl     = document.getElementById(prefix + '-eff');
    const effCardEl = document.getElementById(prefix + '-effCard');
    if (!jarakEl) return;

    jarakEl.textContent = jarak > 0 ? jarak.toLocaleString('id-ID') + ' km' : '—';
    volEl.textContent   = volume > 0 ? volume.toFixed(2) + ' L' : '—';

    if (efisiensi > 0) {
        effEl.textContent = efisiensi.toFixed(2) + ' km/L';
        if (efisiensi > 20 || efisiensi < 2) {
            effCardEl.style.borderColor = '#ef4444'; effEl.style.color = '#ef4444';
        } else if (efisiensi < 5) {
            effCardEl.style.borderColor = '#f59e0b'; effEl.style.color = '#d97706';
        } else {
            effCardEl.style.borderColor = '#22c55e'; effEl.style.color = '#16a34a';
        }
    } else {
        effEl.textContent = '—';
        effCardEl.style.borderColor = '#dbeafe';
        effEl.style.color = '#1f2937';
    }
}

/* ══════════════════════════════════════════════
   FOTO PREVIEW — tampilkan thumbnail foto baru
══════════════════════════════════════════════ */
function updateFotoPreview() {
    const input   = document.getElementById('foto_bon');
    const preview = document.getElementById('fotoNewPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.add('is-visible');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.classList.remove('is-visible');
    }
}

/* ══════════════════════════════════════════════
   EVENT LISTENERS
══════════════════════════════════════════════ */
document.getElementById('km_lama').addEventListener('input', updatePreview);
document.getElementById('km_baru').addEventListener('input', updatePreview);

document.getElementById('jumlah_biaya').addEventListener('input', function () {
    updateNominalChip();
    updateVolume();
    updatePreview();
});

document.getElementById('harga_per_liter').addEventListener('input', function () {
    updateVolume();
    updatePreview();
});

document.getElementById('foto_bon').addEventListener('change', updateFotoPreview);

/* ══════════════════════════════════════════════
   INIT — restore step setelah validasi Laravel gagal
══════════════════════════════════════════════ */
window.addEventListener('DOMContentLoaded', function () {
    @if($errors->has('jumlah_biaya') || $errors->has('harga_per_liter') || $errors->has('no_bon') || $errors->has('foto_bon'))
        goStep(3);
    @elseif($errors->has('km_lama') || $errors->has('km_baru'))
        goStep(2);
    @else
        updateWizard(1);
    @endif

    updateVolume();
    updateNominalChip();
    updatePreview();
});
</script>
</body>
</html>