@extends('layout.app')

@section('title', 'Edit Kendaraan')

@push('styles')
@vite('resources/Tema/kendaraan/edit.css')
@endpush

@section('content')
<div style="background:#0d0d0d;min-height:100vh;color:#f9fafb;">
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
            <span class="badge badge-r4">🚗 R4 — Roda Empat</span>
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
                    <input type="hidden" name="jenis" value="R4">
                    <div class="field flex items-center gap-2 text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-merah shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <circle cx="5" cy="17" r="2"/><circle cx="19" cy="17" r="2"/>
                            <path stroke-linecap="round" d="M7 17h10M1 17H3V9l3-5h10l4 5h1a1 1 0 011 1v7h-2"/>
                        </svg>
                        <span class="text-zinc-300 font-medium">R4 — Roda Empat (Mobil Operasional)</span>
                    </div>
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

</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    const plat = '{{ $kendaraan->plat_nomor }}';
    if (confirm('Hapus kendaraan ' + plat + '?\n\nSemua data perjalanan yang terkait akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('formDelete').submit();
    }
}
</script>
@endpush