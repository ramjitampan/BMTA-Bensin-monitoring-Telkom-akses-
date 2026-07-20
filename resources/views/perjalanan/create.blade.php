@extends('layout.app')

@section('title', 'Tambah Data BBM — PT Telkom Akses Binjai')

@push('styles')
@vite('resources/Tema/perjalanan/create.css')
@endpush

@section('content')
<main style="max-width:44rem;margin:0 auto;padding:1.5rem 1.25rem 3rem;">

    {{-- ── Global validation errors ── --}}
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

    {{-- ── Progress bar ── --}}
    <div class="progress-track" style="margin-bottom:0.875rem;">
        <div class="progress-fill" id="progressFill" style="width:33.33%"></div>
    </div>

    {{-- ── Step wizard ── --}}
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
    <form action="{{ route('perjalanan.store') }}" method="POST" enctype="multipart/form-data" id="mainForm">
    @csrf

    {{-- ════════════════════════════════════════════
         STEP 1 — INFORMASI PERJALANAN
    ════════════════════════════════════════════ --}}
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
                        value="{{ old('tanggal', date('Y-m-d')) }}"
                        class="field {{ $errors->has('tanggal') ? 'field-error' : '' }}">
                    @error('tanggal')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="pegawai_id">Pegawai<span class="label-required">*</span></label>
                    <select id="pegawai_id" name="pegawai_id" class="field {{ $errors->has('pegawai_id') ? 'field-error' : '' }}">
                        <option value="">— Pilih pegawai —</option>
                        @foreach($pegawais as $pg)
                            <option value="{{ $pg->id }}" {{ old('pegawai_id') == $pg->id ? 'selected' : '' }}>
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
                                data-km="{{ $kmTerakhir[$k->id] ?? '' }}"
                                {{ old('kendaraan_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->plat_nomor }} — {{ $k->merk }} ({{ $k->jenis }})
                            </option>
                        @endforeach
                    </select>
                    @error('kendaraan_id')<p class="error-msg">{{ $message }}</p>@enderror
                    <span id="hintKendaraan" class="chip chip-blue" style="display:none;"></span>
                </div>
                <div>
                    <label class="label" for="tujuan">Tujuan Perjalanan<span class="label-required">*</span></label>
                    <input
                        id="tujuan" type="text" name="tujuan"
                        value="{{ old('tujuan') }}"
                        placeholder="cth: Kantor Regional Medan"
                        class="field {{ $errors->has('tujuan') ? 'field-error' : '' }}">
                    @error('tujuan')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="label" for="uraian">Uraian Kegiatan<span class="label-badge">(opsional)</span></label>
                <textarea id="uraian" name="uraian" rows="2" placeholder="Keterangan tambahan..." class="field">{{ old('uraian') }}</textarea>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.75rem;flex-wrap:wrap;">
            <a href="{{ route('perjalanan.index') }}" class="btn-secondary">← Batal</a>
            <button type="button" class="btn-primary" onclick="goStep(2)">
                Lanjut
                <svg xmlns="http://www.w3.org/2000/svg" style="width:.875rem;height:.875rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════
         STEP 2 — ODOMETER
    ════════════════════════════════════════════ --}}
    <div class="section is-hidden" id="sec2">

        <div class="card" style="margin-bottom:1rem;">
            <div class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                Data Odometer
            </div>

            <div class="grid-2" style="margin-bottom:1rem;">
                <div>
                    <label class="label" for="km_lama">KM Awal<span class="label-required">*</span></label>
                    <input
                        id="km_lama" type="number" name="km_lama"
                        value="{{ old('km_lama') }}"
                        min="0" step="1" placeholder="cth: 12500"
                        class="field {{ $errors->has('km_lama') ? 'field-error' : '' }}">
                    @error('km_lama')<p class="error-msg">{{ $message }}</p>@enderror
                    <p class="hint-msg">Baca odometer <strong>sebelum</strong> berangkat</p>
                </div>
                <div>
                    <label class="label" for="km_baru">KM Akhir<span class="label-required">*</span></label>
                    <input
                        id="km_baru" type="number" name="km_baru"
                        value="{{ old('km_baru') }}"
                        min="0" step="1" placeholder="cth: 12687"
                        class="field {{ $errors->has('km_baru') ? 'field-error' : '' }}">
                    @error('km_baru')<p class="error-msg">{{ $message }}</p>@enderror
                    <p class="hint-msg">Baca odometer <strong>setelah</strong> tiba</p>
                </div>
            </div>

            {{-- Hint odometer terakhir (populated by JS) --}}
            <div id="hintOdometerBox" class="alert alert-blue" style="display:none;font-size:.75rem;"></div>
        </div>

        {{-- Preview kalkulasi step 2 --}}
        <div class="preview-wrap" id="previewStep2" style="margin-bottom:1rem;">
            <p style="font-size:.68rem;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.08em;margin:0;">🔍 Preview Kalkulasi</p>
            <div class="preview-grid">
                <div class="pv-card">
                    <div class="pv-label">Jarak Tempuh</div>
                    <div class="pv-value" id="pv2-jarak">—</div>
                </div>
                <div class="pv-card">
                    <div class="pv-label">Volume BBM</div>
                    <div class="pv-value" id="pv2-vol">—</div>
                </div>
                <div class="pv-card" id="pv2-effCard">
                    <div class="pv-label">Efisiensi</div>
                    <div class="pv-value" id="pv2-eff">—</div>
                </div>
            </div>

        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.75rem;flex-wrap:wrap;">
            <button type="button" class="btn-secondary" onclick="goStep(1)">← Kembali</button>
            <button type="button" class="btn-primary" onclick="goStep(3)">
                Lanjut
                <svg xmlns="http://www.w3.org/2000/svg" style="width:.875rem;height:.875rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════
         STEP 3 — BON BBM
    ════════════════════════════════════════════ --}}
    <div class="section is-hidden" id="sec3">

        <div class="card" style="margin-bottom:1rem;">
            <div class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                Data Bon BBM Pertamina
            </div>

            <div class="grid-2" style="margin-bottom:1rem;">
                {{-- Kolom kiri: nominal + validasi + aturan --}}
                <div>
                    <label class="label" for="jumlah_biaya">Nominal Bon (Rp)<span class="label-required">*</span></label>
                    <input
                        id="jumlah_biaya" type="number" name="jumlah_biaya"
                        value="{{ old('jumlah_biaya') }}"
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

                {{-- Kolom kanan: harga + volume --}}
                <div style="display:flex;flex-direction:column;gap:1rem;">
                    <div>
                        <label class="label" for="harga_per_liter">Harga per Liter (Rp)<span class="label-required">*</span></label>
                        <input
                            id="harga_per_liter" type="number" name="harga_per_liter"
                            value="{{ old('harga_per_liter', 10000) }}"
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
                            <input
                                id="vol_liter_preview" type="number"
                                readonly placeholder="—"
                                class="field field-readonly"
                                style="padding-right:2rem;">
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
                        value="{{ old('no_bon') }}"
                        placeholder="cth: TXN-20240601-001"
                        class="field {{ $errors->has('no_bon') ? 'field-error' : '' }}">
                    @error('no_bon')<p class="error-msg">{{ $message }}</p>@enderror
                    <p class="hint-msg">Untuk deteksi duplikasi bon</p>
                </div>
                <div>
                    <label class="label" for="foto_bon">Foto Bon / Struk<span class="label-badge">(opsional, maks 2MB)</span></label>
                    <input
                        id="foto_bon" type="file" name="foto_bon"
                        accept="image/jpg,image/jpeg,image/png"
                        class="field" style="height:auto;padding:.5rem .75rem;cursor:pointer;">
                    @error('foto_bon')<p class="error-msg">{{ $message }}</p>@enderror
                    <p class="hint-msg">Format JPG/PNG — untuk verifikasi audit</p>
                </div>
            </div>
        </div>

        {{-- Preview kalkulasi step 3 --}}
        <div class="preview-wrap" id="previewStep3" style="margin-bottom:1rem;">
            <p style="font-size:.68rem;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.08em;margin:0;">🔍 Preview Kalkulasi</p>
            <div class="preview-grid">
                <div class="pv-card">
                    <div class="pv-label">Jarak Tempuh</div>
                    <div class="pv-value" id="pv3-jarak">—</div>
                </div>
                <div class="pv-card">
                    <div class="pv-label">Volume BBM</div>
                    <div class="pv-value" id="pv3-vol">—</div>
                </div>
                <div class="pv-card" id="pv3-effCard">
                    <div class="pv-label">Efisiensi</div>
                    <div class="pv-value" id="pv3-eff">—</div>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.75rem;flex-wrap:wrap;">
            <button type="button" class="btn-secondary" onclick="goStep(2)">← Kembali</button>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                <button type="submit" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Simpan Data Perjalanan
                </button>
                <a href="{{ route('perjalanan.index') }}" class="btn-secondary">← Batal</a>
            </div>
        </div>
    </div>

    </form>
</main>

@endsection

@push('scripts')
<script>
/* ── Data km terakhir dari Laravel ── */
const KM_DATA = {
    @foreach($kendaraans as $k)
    {{ $k->id }}: {{ $kmTerakhir[$k->id] ?? 'null' }},
    @endforeach
};

/* ══════════════════════════════════════════════
   STATE
══════════════════════════════════════════════ */
let currentStep = 1;

/* ══════════════════════════════════════════════
   WIZARD — navigasi antar step
══════════════════════════════════════════════ */
function goStep(n) {
    document.getElementById('sec' + currentStep).classList.add('is-hidden');
    currentStep = n;
    document.getElementById('sec' + n).classList.remove('is-hidden');

    updateWizard(n);
    updatePreview();
}

function updateWizard(active) {
    const totalSteps = 3;
    document.getElementById('progressFill').style.width = (active / totalSteps * 100) + '%';

    for (let i = 1; i <= totalSteps; i++) {
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
   HINT — odometer terakhir kendaraan terpilih
══════════════════════════════════════════════ */
function updateHint() {
    const kendaraanId = document.getElementById('kendaraan_id').value;
    const km          = KM_DATA[kendaraanId];

    const chip = document.getElementById('hintKendaraan');
    if (km) {
        chip.textContent = 'Odometer terakhir: ' + Number(km).toLocaleString('id-ID') + ' km';
        chip.style.display = 'inline-flex';
    } else {
        chip.style.display = 'none';
    }

    const box    = document.getElementById('hintOdometerBox');
    const kmLama = document.getElementById('km_lama');
    if (km) {
        box.innerHTML = 'ℹ Odometer terakhir kendaraan ini: <strong>' + Number(km).toLocaleString('id-ID') + ' km</strong>.';
        box.style.display = 'block';
        if (!kmLama.value) {
            kmLama.value = km;
        }
    } else {
        box.style.display = 'none';
    }
}

/* ══════════════════════════════════════════════
   VALIDASI NOMINAL — real-time chip feedback
══════════════════════════════════════════════ */
function updateNominalChip() {
    const v   = parseInt(document.getElementById('jumlah_biaya').value, 10);
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
   VOLUME — nominal ÷ harga (untuk field readonly)
══════════════════════════════════════════════ */
function updateVolume() {
    const nominal = parseFloat(document.getElementById('jumlah_biaya').value);
    const harga   = parseFloat(document.getElementById('harga_per_liter').value);
    const field   = document.getElementById('vol_liter_preview');
    field.value   = (nominal > 0 && harga > 0) ? (nominal / harga).toFixed(2) : '';
}

/* ══════════════════════════════════════════════
   PREVIEW — tampilkan kalkulasi di UI
══════════════════════════════════════════════ */
function updatePreview() {
    const kmLama  = parseFloat(document.getElementById('km_lama').value);
    const kmBaru  = parseFloat(document.getElementById('km_baru').value);
    const nominal = parseFloat(document.getElementById('jumlah_biaya').value);
    const harga   = parseFloat(document.getElementById('harga_per_liter').value);

    const jarak  = (!isNaN(kmLama) && !isNaN(kmBaru)) ? (kmBaru - kmLama) : 0;
    const volume = (harga > 0 && nominal > 0) ? (nominal / harga) : 0;
    const efisiensi = (volume > 0 && jarak > 0) ? (jarak / volume) : 0;

    const shouldShow = (jarak > 0 || volume > 0);

    renderPreviewBlock('previewStep2', 'pv2', jarak, volume, efisiensi, shouldShow);
    renderPreviewBlock('previewStep3', 'pv3', jarak, volume, efisiensi, shouldShow);
}

function renderPreviewBlock(wrapperId, prefix, jarak, volume, efisiensi, show) {
    const wrap = document.getElementById(wrapperId);
    if (!wrap) return;

    if (!show) {
        wrap.classList.remove('is-visible');
        return;
    }
    wrap.classList.add('is-visible');

    const jarakEl    = document.getElementById(prefix + '-jarak');
    const volEl      = document.getElementById(prefix + '-vol');
    const effEl      = document.getElementById(prefix + '-eff');
    const effCardEl  = document.getElementById(prefix + '-effCard');
    if (!jarakEl) return;

    jarakEl.textContent = jarak > 0 ? jarak.toLocaleString('id-ID') + ' km' : '—';
    volEl.textContent   = volume > 0 ? volume.toFixed(2) + ' L' : '—';

    if (efisiensi > 0) {
        effEl.textContent = efisiensi.toFixed(2) + ' km/L';
        if (efisiensi > 20 || efisiensi < 2) {
            effCardEl.style.borderColor = '#ef4444';
            effEl.style.color = '#ef4444';
        } else if (efisiensi < 5) {
            effCardEl.style.borderColor = '#f59e0b';
            effEl.style.color = '#d97706';
        } else {
            effCardEl.style.borderColor = '#22c55e';
            effEl.style.color = '#16a34a';
        }
    } else {
        effEl.textContent = '—';
        effCardEl.style.borderColor = '#dbeafe';
        effEl.style.color = '#1f2937';
    }
}

/* ══════════════════════════════════════════════
   EVENT LISTENERS
══════════════════════════════════════════════ */
document.getElementById('kendaraan_id').addEventListener('change', function () {
    updateHint();
    updatePreview();
});

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

/* ══════════════════════════════════════════════
   INIT — restore old() Laravel setelah validasi gagal
══════════════════════════════════════════════ */
window.addEventListener('DOMContentLoaded', function () {
    @if(old('jumlah_biaya'))
        goStep(3);
    @elseif(old('km_lama') || old('km_baru'))
        goStep(2);
    @else
        updateWizard(1);
    @endif

    const sel = document.getElementById('kendaraan_id');
    if (sel.value) {
        updateHint();
    }

    updateVolume();
    updateNominalChip();
    updatePreview();
});
</script>
@endpush