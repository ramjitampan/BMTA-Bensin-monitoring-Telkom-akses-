<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data BBM — PT Telkom Akses Binjai</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen text-sm">

    {{-- Navbar --}}
    <div class="navbar bg-[#CC0000] text-white shadow-lg px-4">
        <div class="flex-1 flex items-center gap-3">
            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center font-black text-xs text-[#CC0000]">TA</div>
            <div>
                <p class="font-bold text-sm leading-tight">Telkom Akses Binjai</p>
                <p class="text-xs opacity-75 leading-tight">Tambah Data Perjalanan & BBM</p>
            </div>
        </div>
        <div class="flex-none">
            <a href="{{ route('perjalanan.index') }}" class="btn btn-sm btn-ghost text-white border border-white border-opacity-40">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="max-w-3xl mx-auto p-4">

        {{-- Error summary --}}
        @if($errors->any())
        <div class="alert alert-error mb-4">
            <div>
                <p class="font-bold">⚠ {{ $errors->count() }} kesalahan input:</p>
                <ul class="list-disc list-inside text-sm mt-1 space-y-0.5">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form action="{{ route('perjalanan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- ── INFORMASI PERJALANAN ──────────────────────── --}}
        <div class="card bg-white shadow-sm border border-gray-100 mb-4">
            <div class="card-body p-5">
                <h3 class="text-xs font-bold uppercase tracking-widest text-[#CC0000] border-b border-gray-100 pb-2 mb-4">
                    Informasi Perjalanan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Tanggal <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                            class="input input-bordered input-sm w-full {{ $errors->has('tanggal') ? 'input-error' : '' }}">
                        @error('tanggal')
                            <label class="label py-0"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Pegawai <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select name="pegawai_id" class="select select-bordered select-sm w-full {{ $errors->has('pegawai_id') ? 'select-error' : '' }}">
                            <option value="">— Pilih pegawai —</option>
                            @foreach($pegawais as $pg)
                                <option value="{{ $pg->id }}" {{ old('pegawai_id') == $pg->id ? 'selected' : '' }}>
                                    {{ $pg->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('pegawai_id')
                            <label class="label py-0"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Kendaraan <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select id="kendaraan_id" name="kendaraan_id"
                            class="select select-bordered select-sm w-full {{ $errors->has('kendaraan_id') ? 'select-error' : '' }}">
                            <option value="">— Pilih kendaraan —</option>
                            @foreach($kendaraans as $k)
                                <option value="{{ $k->id }}"
                                    data-km="{{ $kmTerakhir[$k->id] ?? '' }}"
                                    {{ old('kendaraan_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nomor_polisi ?? $k->plat_nomor }} — {{ $k->merk ?? '' }} ({{ $k->tipe }})
                                </option>
                            @endforeach
                        </select>
                        @error('kendaraan_id')
                            <label class="label py-0"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                        <p id="hint-km" class="text-xs text-blue-500 mt-1" style="display:none"></p>
                    </div>

                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Tujuan Perjalanan <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <input type="text" name="tujuan" value="{{ old('tujuan') }}"
                            placeholder="cth: Kantor Regional Medan"
                            class="input input-bordered input-sm w-full {{ $errors->has('tujuan') ? 'input-error' : '' }}">
                        @error('tujuan')
                            <label class="label py-0"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>
                </div>

                <div class="form-control">
                    <label class="label py-1">
                        <span class="label-text text-xs font-semibold text-gray-500 uppercase tracking-wide">Uraian Kegiatan</span>
                        <span class="label-text-alt text-gray-400">opsional</span>
                    </label>
                    <textarea name="uraian" rows="2" placeholder="Keterangan tambahan..."
                        class="textarea textarea-bordered textarea-sm w-full resize-none">{{ old('uraian') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── ODOMETER ──────────────────────────────────── --}}
        <div class="card bg-white shadow-sm border border-gray-100 mb-4">
            <div class="card-body p-5">
                <h3 class="text-xs font-bold uppercase tracking-widest text-[#CC0000] border-b border-gray-100 pb-2 mb-4">
                    Data Odometer
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                KM Awal (Odometer Lama) <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <input type="number" id="km_lama" name="km_lama" value="{{ old('km_lama') }}"
                            min="0" step="1" placeholder="cth: 12500"
                            class="input input-bordered input-sm w-full {{ $errors->has('km_lama') ? 'input-error' : '' }}">
                        @error('km_lama')
                            <label class="label py-0"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                        <label class="label py-0">
                            <span class="label-text-alt text-gray-400">Baca dari odometer <strong>sebelum</strong> berangkat</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                KM Akhir (Odometer Baru) <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <input type="number" id="km_baru" name="km_baru" value="{{ old('km_baru') }}"
                            min="0" step="1" placeholder="cth: 12687"
                            class="input input-bordered input-sm w-full {{ $errors->has('km_baru') ? 'input-error' : '' }}">
                        @error('km_baru')
                            <label class="label py-0"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                        <label class="label py-0">
                            <span class="label-text-alt text-gray-400">Baca dari odometer <strong>setelah</strong> tiba</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── BON BBM ───────────────────────────────────── --}}
        <div class="card bg-white shadow-sm border border-gray-100 mb-4">
            <div class="card-body p-5">
                <h3 class="text-xs font-bold uppercase tracking-widest text-[#CC0000] border-b border-gray-100 pb-2 mb-4">
                    Data Bon BBM Pertamina
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Nominal Bon (Rp) <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <input type="number" id="jumlah_biaya" name="jumlah_biaya"
                            value="{{ old('jumlah_biaya') }}" min="1000" step="1000"
                            placeholder="cth: 52000"
                            class="input input-bordered input-sm w-full {{ $errors->has('jumlah_biaya') ? 'input-error' : '' }}">
                        @error('jumlah_biaya')
                            <label class="label py-0"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                        <p id="nominal-check" class="text-xs mt-1.5 font-semibold"></p>
                        <div class="mt-2 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 text-xs text-amber-700">
                            <p class="font-bold mb-0.5">⚠ Aturan Bon Lapangan</p>
                            <p>Nominal harus <strong>kelipatan Rp1.000</strong> — bukan kelipatan bulat Rp10.000</p>
                            <p class="text-green-600 mt-0.5">✓ Rp 51.000 · 52.000 · 127.000 · 101.000</p>
                            <p class="text-red-500">✕ Rp 10.000 · 20.000 · 50.000 · 100.000</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Harga per Liter (Rp) <span class="text-red-500">*</span>
                                </span>
                            </label>
                            {{-- step="1" agar browser tidak menolak nilai seperti 10000, 12900, 13500 --}}
                            <input type="number" id="harga_per_liter" name="harga_per_liter"
                                value="{{ old('harga_per_liter', 10000) }}" min="1" step="1"
                                placeholder="cth: 10000"
                                class="input input-bordered input-sm w-full {{ $errors->has('harga_per_liter') ? 'input-error' : '' }}">
                            @error('harga_per_liter')
                                <label class="label py-0"><span class="label-text-alt text-error">{{ $message }}</span></label>
                            @enderror
                            <label class="label py-0">
                                <span class="label-text-alt text-gray-400">Sesuai harga Pertamina saat pengisian</span>
                            </label>
                        </div>

                        {{-- Volume BBM readonly — dihitung otomatis dari Nominal ÷ Harga --}}
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Volume BBM (L)
                                </span>
                                <span class="label-text-alt text-blue-400 font-semibold">dihitung otomatis</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="vol_liter_preview" readonly
                                    placeholder="—"
                                    class="input input-bordered input-sm w-full bg-blue-50 text-blue-700 font-bold cursor-not-allowed pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-blue-400 font-semibold pointer-events-none">L</span>
                            </div>
                            <label class="label py-0">
                                <span class="label-text-alt text-gray-400">= Nominal Bon ÷ Harga per Liter</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-xs font-semibold text-gray-500 uppercase tracking-wide">No. Bon / Struk</span>
                            <span class="label-text-alt text-gray-400">opsional</span>
                        </label>
                        <input type="text" name="no_bon" value="{{ old('no_bon') }}"
                            placeholder="cth: TXN-20240601-001"
                            class="input input-bordered input-sm w-full {{ $errors->has('no_bon') ? 'input-error' : '' }}">
                        @error('no_bon')
                            <label class="label py-0"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                        <label class="label py-0">
                            <span class="label-text-alt text-gray-400">Untuk cek duplikasi bon</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-xs font-semibold text-gray-500 uppercase tracking-wide">Foto Bon / Struk</span>
                            <span class="label-text-alt text-gray-400">opsional, maks 2MB</span>
                        </label>
                        <input type="file" name="foto_bon" accept="image/jpg,image/jpeg,image/png"
                            class="file-input file-input-bordered file-input-sm w-full {{ $errors->has('foto_bon') ? 'file-input-error' : '' }}">
                        @error('foto_bon')
                            <label class="label py-0"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                        <label class="label py-0">
                            <span class="label-text-alt text-gray-400">Format JPG/PNG — untuk verifikasi audit</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── PREVIEW KALKULASI ─────────────────────────── --}}
        <div id="preview-box" class="card bg-blue-50 border border-blue-200 mb-4" style="display:none">
            <div class="card-body p-4">
                <p class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-3">🔢 Preview Kalkulasi Otomatis</p>
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white rounded-lg p-3 text-center shadow-sm">
                        <p class="text-xs text-gray-400 mb-1">Jarak Tempuh</p>
                        <p class="text-lg font-black text-gray-800" id="pv-jarak">—</p>
                    </div>
                    <div class="bg-white rounded-lg p-3 text-center shadow-sm">
                        <p class="text-xs text-gray-400 mb-1">Volume BBM</p>
                        <p class="text-lg font-black text-gray-800" id="pv-vol">—</p>
                    </div>
                    <div id="pv-eff-card" class="bg-white rounded-lg p-3 text-center shadow-sm">
                        <p class="text-xs text-gray-400 mb-1">Efisiensi</p>
                        <p class="text-lg font-black text-gray-800" id="pv-eff">—</p>
                    </div>
                </div>
                <p id="pv-catatan" class="text-xs text-amber-600 mt-2"></p>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="btn btn-sm text-white border-none hover:opacity-90 font-bold px-6"
                style="background:#CC0000">
                💾 Simpan Data Perjalanan
            </button>
            <a href="{{ route('perjalanan.index') }}" class="btn btn-sm btn-ghost text-gray-400">
                ← Batal
            </a>
        </div>

        </form>
    </div>

    <footer class="text-center text-xs text-gray-300 py-6 mt-4">
        PT Telkom Akses Binjai &mdash; Sistem Informasi Pengelolaan Biaya BBM Kendaraan Operasional
    </footer>

    <script>
    const kmData = {
        @foreach($kendaraans as $k)
        {{ $k->id }}: {{ $kmTerakhir[$k->id] ?? 'null' }},
        @endforeach
    };

    const selKendaraan   = document.getElementById('kendaraan_id');
    const inpKmLama      = document.getElementById('km_lama');
    const inpKmBaru      = document.getElementById('km_baru');
    const inpBiaya       = document.getElementById('jumlah_biaya');
    const inpHarga       = document.getElementById('harga_per_liter');
    const volPreview     = document.getElementById('vol_liter_preview');
    const hintKm         = document.getElementById('hint-km');
    const nomCheck       = document.getElementById('nominal-check');
    const previewBox     = document.getElementById('preview-box');

    // ── Kendaraan change ──────────────────────────────────────
    selKendaraan.addEventListener('change', function () {
        const km = kmData[this.value];
        if (km) {
            hintKm.textContent = `ℹ Odometer terakhir: ${km.toLocaleString('id-ID')} km — KM Awal harus ≥ angka ini.`;
            hintKm.style.display = 'block';
            if (!inpKmLama.value) inpKmLama.value = km;
        } else {
            hintKm.style.display = 'none';
        }
        hitungPreview();
    });

    // ── Validasi nominal bon ──────────────────────────────────
    // Tolak HANYA jika kelipatan bulat 10.000
    // Valid  : 51.000, 52.000, 127.000, 101.000
    // Tidak  : 10.000, 20.000, 50.000, 100.000
    inpBiaya.addEventListener('input', function () {
        const val = parseFloat(this.value);
        nomCheck.textContent = '';
        if (!val || val < 1000) { updateVolPreview(); return; }

        if (val % 1000 !== 0) {
            nomCheck.textContent = '✕ Harus kelipatan Rp1.000';
            nomCheck.style.color = '#dc2626';
        } else if (val % 10000 === 0) {
            nomCheck.textContent = `✕ Rp ${val.toLocaleString('id-ID')} — nominal bulat, tidak valid!`;
            nomCheck.style.color = '#dc2626';
        } else {
            nomCheck.textContent = `✓ Rp ${val.toLocaleString('id-ID')} — nominal valid ✓`;
            nomCheck.style.color = '#16a34a';
        }
        updateVolPreview();
        hitungPreview();
    });

    // ── Update field volume readonly ──────────────────────────
    function updateVolPreview() {
        const biaya = parseFloat(inpBiaya.value);
        const harga = parseFloat(inpHarga.value);
        if (biaya > 0 && harga > 0) {
            volPreview.value = (biaya / harga).toFixed(2);
        } else {
            volPreview.value = '';
        }
    }

    // ── Preview kalkulasi (jarak, vol, efisiensi) ─────────────
    function hitungPreview() {
        const kmL   = parseFloat(inpKmLama.value);
        const kmB   = parseFloat(inpKmBaru.value);
        const biaya = parseFloat(inpBiaya.value);
        const harga = parseFloat(inpHarga.value);
        const jarak = kmB - kmL;
        const vol   = harga > 0 ? biaya / harga : 0;
        const eff   = vol > 0 ? jarak / vol : 0;

        if (!(jarak > 0) && !(vol > 0)) { previewBox.style.display = 'none'; return; }
        previewBox.style.display = 'block';

        document.getElementById('pv-jarak').textContent = jarak > 0 ? `${jarak.toLocaleString('id-ID')} km` : '—';
        document.getElementById('pv-vol').textContent   = vol > 0   ? `${vol.toFixed(2).replace('.', ',')} L` : '—';

        const pvEff  = document.getElementById('pv-eff');
        const pvCard = document.getElementById('pv-eff-card');
        if (eff > 0) {
            pvEff.textContent = `${eff.toFixed(2).replace('.', ',')} km/L`;
            pvCard.style.borderLeft = eff > 20 || eff < 2 ? '3px solid #ef4444'
                                    : eff < 5             ? '3px solid #f59e0b'
                                    : '3px solid #22c55e';
        } else {
            pvEff.textContent = '—';
        }

        const notes = [];
        const kmTerakhir = kmData[selKendaraan.value];
        if (kmTerakhir && !isNaN(kmL) && kmL < kmTerakhir)
            notes.push(`⚠ KM Awal (${kmL.toLocaleString('id-ID')}) lebih kecil dari odometer terakhir (${kmTerakhir.toLocaleString('id-ID')} km) — akan ditolak!`);
        if (jarak > 600)
            notes.push(`⚠ Jarak ${jarak.toLocaleString('id-ID')} km tampak sangat jauh untuk 1 perjalanan.`);
        document.getElementById('pv-catatan').innerHTML = notes.join('<br>');
    }

    // ── Event listeners ───────────────────────────────────────
    inpKmLama.addEventListener('input', hitungPreview);
    inpKmBaru.addEventListener('input', hitungPreview);
    inpHarga.addEventListener('input', function () {
        updateVolPreview();
        hitungPreview();
    });

    // ── Init saat halaman load ────────────────────────────────
    window.addEventListener('DOMContentLoaded', () => {
        if (selKendaraan.value) selKendaraan.dispatchEvent(new Event('change'));
        if (inpBiaya.value)    inpBiaya.dispatchEvent(new Event('input'));
        updateVolPreview();
        hitungPreview();
    });
    </script>

</body>
</html>