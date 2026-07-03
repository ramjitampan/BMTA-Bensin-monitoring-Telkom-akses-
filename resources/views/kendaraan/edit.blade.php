<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kendaraan — PT Telkom Akses Binjai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; box-sizing: border-box; }
        body { background: #0d0d0d; min-height: 100vh; color: #f9fafb; }

        /* Pill nav (matches screenshot) */
        .nav-pill {
            display: inline-flex; align-items: center;
            padding: 0.45rem 1.1rem; border-radius: 9999px;
            font-size: 0.82rem; font-weight: 500; color: #9ca3af;
            text-decoration: none; transition: background .15s, color .15s;
            white-space: nowrap;
        }
        .nav-pill:hover { background: #1f2937; color: #f9fafb; }
        .nav-pill.active { background: #1f2937; color: #f9fafb; }
        .nav-pill.primary { background: #CC0000; color: #fff; font-weight: 600; }
        .nav-pill.primary:hover { background: #a80000; }

        /* Form fields */
        .field {
            display: block; width: 100%;
            height: 2.75rem;
            border: 1.5px solid #2d2d2d; border-radius: 0.625rem;
            background: #1a1a1a; padding: 0 0.9rem;
            font-size: 0.875rem; color: #f9fafb;
            transition: border-color .15s, box-shadow .15s;
            appearance: none;
        }
        .field::placeholder { color: #4b5563; }
        .field:focus { outline: none; border-color: #CC0000; box-shadow: 0 0 0 3px rgba(204,0,0,.12); }
        .field-error { border-color: #ef4444 !important; background: #1f1010; }
        select.field { cursor: pointer; }
        select.field option { background: #1a1a1a; color: #f9fafb; }

        .label {
            display: block;
            font-size: 0.68rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em;
            color: #6b7280; margin-bottom: 0.4rem;
        }
        .label-required { color: #ef4444; margin-left: 0.15rem; }
        .error-msg { margin-top: 0.3rem; font-size: 0.72rem; color: #f87171; }
        .hint-msg  { margin-top: 0.3rem; font-size: 0.72rem; color: #4b5563; }

        /* Card */
        .card {
            background: #141414; border-radius: 1rem;
            border: 1px solid #1f1f1f;
            padding: 1.5rem;
        }
        .card-title {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.1em; color: #CC0000;
            padding-bottom: 1rem; margin-bottom: 1.25rem;
            border-bottom: 1px solid #1f1f1f;
        }
        .card-title svg { width: 13px; height: 13px; flex-shrink: 0; }

        /* Alerts */
        .alert { border-radius: 0.625rem; padding: 0.75rem 0.9rem; font-size: 0.75rem; line-height: 1.65; }
        .alert-red    { background: #1f0a0a; border: 1px solid #7f1d1d; color: #fca5a5; }
        .alert-amber  { background: #1a1500; border: 1px solid #78350f; color: #fbbf24; }

        /* Badges */
        .badge {
            display: inline-flex; align-items: center; gap: 0.3rem;
            font-size: 0.7rem; font-weight: 600;
            padding: 0.3rem 0.85rem; border-radius: 9999px;
        }
        .badge-r2 { background: #0f1a3a; color: #60a5fa; border: 1px solid #1e3a6e; }
        .badge-r4 { background: #1a0f2e; color: #a78bfa; border: 1px solid #3b1f6e; }

        /* Buttons */
        .btn-primary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: #CC0000; color: #fff;
            font-size: 0.875rem; font-weight: 600;
            padding: 0.65rem 1.375rem; border-radius: 0.75rem;
            border: none; cursor: pointer; transition: background .15s, transform .1s;
        }
        .btn-primary:hover  { background: #a80000; }
        .btn-primary:active { transform: scale(.98); }

        .btn-secondary {
            display: inline-flex; align-items: center; gap: 0.375rem;
            background: #1a1a1a; color: #9ca3af;
            font-size: 0.875rem; font-weight: 500;
            padding: 0.65rem 1rem; border-radius: 0.75rem;
            border: 1.5px solid #2d2d2d; cursor: pointer;
            text-decoration: none; transition: background .15s, color .15s;
        }
        .btn-secondary:hover { background: #222; color: #f9fafb; }

        .btn-danger {
            display: inline-flex; align-items: center; gap: 0.375rem;
            background: #1a1a1a; color: #f87171;
            font-size: 0.875rem; font-weight: 500;
            padding: 0.65rem 1rem; border-radius: 0.75rem;
            border: 1.5px solid #3f1515; cursor: pointer;
            transition: background .15s;
        }
        .btn-danger:hover { background: #1f0d0d; }

        /* Plat nomor display */
        .plat-box {
            display: inline-block;
            background: #0d0d0d; border: 2px solid #2d2d2d;
            border-radius: 0.5rem; padding: 0.25rem 0.75rem;
            font-weight: 700; font-size: 0.95rem; color: #f9fafb;
            letter-spacing: 0.08em;
        }

        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        @media (max-width: 560px) {
            .grid-2 { grid-template-columns: 1fr; }
            .btn-primary, .btn-secondary, .btn-danger { width: 100%; justify-content: center; }
        }

        /* Divider */
        .divider { height: 1px; background: #1f1f1f; margin: 1.5rem 0; }

        /* Page header */
        .page-eyebrow {
            font-size: 0.65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.12em;
            color: #4b5563; margin-bottom: 0.25rem;
        }
        .page-title {
            font-size: 1.5rem; font-weight: 800;
            color: #f9fafb; margin: 0;
        }
        .page-title span { color: #CC0000; }
    </style>
</head>
<body>

<main style="max-width:52rem;margin:0 auto;padding:2rem 1.25rem 4rem;">

    {{-- Page header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:2rem;">
        <div>
            <p class="page-eyebrow">ARMADA KENDARAAN</p>
            <h1 class="page-title">Edit <span>Kendaraan</span></h1>
            <div style="width:2.5rem;height:3px;background:#CC0000;border-radius:2px;margin-top:.5rem;"></div>
        </div>
        {{-- Kendaraan badge --}}
        <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
            <div class="plat-box">{{ $kendaraan->plat_nomor }}</div>
            <div>
                <p style="margin:0;font-size:.85rem;font-weight:600;color:#f9fafb;">{{ $kendaraan->merk }}</p>
                <p style="margin:.1rem 0 0;font-size:.72rem;color:#6b7280;">{{ $kendaraan->tahun }}</p>
            </div>
            <span class="badge {{ $kendaraan->jenis === 'R2' ? 'badge-r2' : 'badge-r4' }}">
                {{ $kendaraan->jenis === 'R2' ? '🏍 R2' : '🚗 R4' }}
            </span>
        </div>
    </div>

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

    {{-- FORM --}}
    <form action="{{ route('kendaraan.update', $kendaraan) }}" method="POST" id="formKendaraan">
    @csrf
    @method('PUT')

        <div class="card" style="margin-bottom:1rem;">
            <div class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Identitas Kendaraan
            </div>

            <div class="grid-2" style="margin-bottom:1rem;">
                <div>
                    <label class="label" for="plat_nomor">Plat Nomor<span class="label-required">*</span></label>
                    <input
                        id="plat_nomor" type="text" name="plat_nomor"
                        value="{{ old('plat_nomor', $kendaraan->plat_nomor) }}"
                        placeholder="cth: BK 1234 AB"
                        style="text-transform:uppercase;"
                        oninput="this.value=this.value.toUpperCase()"
                        class="field {{ $errors->has('plat_nomor') ? 'field-error' : '' }}">
                    @error('plat_nomor')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="merk">Merk / Model<span class="label-required">*</span></label>
                    <input
                        id="merk" type="text" name="merk"
                        value="{{ old('merk', $kendaraan->merk) }}"
                        placeholder="cth: Honda Vario 125"
                        class="field {{ $errors->has('merk') ? 'field-error' : '' }}">
                    @error('merk')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid-2">
                <div>
                    <label class="label" for="jenis">Jenis Kendaraan<span class="label-required">*</span></label>
                    <select id="jenis" name="jenis" class="field {{ $errors->has('jenis') ? 'field-error' : '' }}">
                        <option value="">— Pilih jenis —</option>
                        <option value="R2" {{ old('jenis', $kendaraan->jenis) === 'R2' ? 'selected' : '' }}>R2 — Roda Dua (Motor)</option>
                        <option value="R4" {{ old('jenis', $kendaraan->jenis) === 'R4' ? 'selected' : '' }}>R4 — Roda Empat (Mobil)</option>
                    </select>
                    @error('jenis')<p class="error-msg">{{ $message }}</p>@enderror
                    <p class="hint-msg">Menentukan threshold efisiensi BBM</p>
                </div>
                <div>
                    <label class="label" for="tahun">Tahun Kendaraan<span class="label-required">*</span></label>
                    <input
                        id="tahun" type="number" name="tahun"
                        value="{{ old('tahun', $kendaraan->tahun) }}"
                        min="1900" max="{{ date('Y') }}"
                        placeholder="cth: 2022"
                        class="field {{ $errors->has('tahun') ? 'field-error' : '' }}">
                    @error('tahun')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Warning --}}
        <div class="alert alert-amber" style="margin-bottom:1.25rem;">
            ⚠ Mengubah <strong>Jenis Kendaraan</strong> akan mempengaruhi perhitungan threshold efisiensi pada semua data perjalanan kendaraan ini.
        </div>

        {{-- Action buttons --}}
        <div style="display:flex;justify-content:space-between;align-items:center;gap:.75rem;flex-wrap:wrap;">
            <div style="display:flex;gap:.625rem;flex-wrap:wrap;">
                <a href="{{ route('kendaraan.index') }}" class="btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:.85rem;height:.85rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Batal
                </a>
                <button type="button" class="btn-danger" onclick="confirmDelete()">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:.85rem;height:.85rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus
                </button>
            </div>
            <button type="submit" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:.95rem;height:.95rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Simpan Perubahan
            </button>
        </div>

    </form>

    {{-- Hidden delete form --}}
    <form id="formDelete" action="{{ route('kendaraan.destroy', $kendaraan) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

</main>

{{-- FOOTER --}}
<footer style="background:#0a0a0a;border-top:1px solid #1a1a1a;">
    <div style="max-width:52rem;margin:0 auto;padding:1.75rem 1.25rem;">
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:1.25rem;">
            {{-- Logo placeholder (swap with your actual logo img tag) --}}
            <div style="display:flex;flex-direction:column;align-items:flex-start;gap:.25rem;">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div style="width:2rem;height:2rem;border-radius:50%;background:#CC0000;display:flex;align-items:center;justify-content:center;">
                        <span style="color:#fff;font-weight:900;font-size:.65rem;">TA</span>
                    </div>
                    <p style="color:#f9fafb;font-weight:700;font-size:.875rem;margin:0;">TelkomAkses</p>
                </div>
            </div>
            <div style="width:1px;background:#1f1f1f;align-self:stretch;"></div>
            <div>
                <p style="color:#f3f4f6;font-size:.85rem;font-weight:500;margin:0 0 .25rem;">Bensin Monitoring</p>
                <p style="font-size:.75rem;line-height:1.6;margin:0;color:#4b5563;">PT Telkom Akses — Sistem Pemantauan BBM</p>
            </div>
        </div>
        <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid #111;display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:.5rem;font-size:.7rem;color:#374151;">
            <p style="margin:0;">&copy; {{ date('Y') }} PT Telkom Akses Binjai. All rights reserved.</p>
            <p style="margin:0;">v1.0 &middot; TIF-2954/2026</p>
        </div>
    </div>
</footer>

<script>
function confirmDelete() {
    const plat = '{{ $kendaraan->plat_nomor }}';
    if (confirm('Hapus kendaraan ' + plat + '?\n\nSemua data perjalanan yang terkait akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('formDelete').submit();
    }
}
</script>
</body>
</html>