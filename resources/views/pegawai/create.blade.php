@extends('layout.app')

@section('title', 'Tambah Pegawai')
@section('meta_description', 'Isi data pegawai pengemudi kendaraan operasional baru PT. Telkom Akses Binjai.')

@section('content')

{{-- ══════════════════════════════════════════ --}}
{{-- HERO                                     --}}
{{-- ══════════════════════════════════════════ --}}
<div class="hero-bg-pegawai relative w-full overflow-hidden">

    <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full border-[48px] border-white/[.05] pointer-events-none"></div>
    <div class="absolute top-6 right-1/3 w-44 h-44 rounded-full border-[28px] border-white/[.04] pointer-events-none"></div>
    <div class="absolute -bottom-12 left-1/4 w-56 h-56 rounded-full border-[36px] border-white/[.03] pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-8">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h1 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                    Tambah Pegawai
                </h1>
                <p class="mt-2 max-w-xl text-sm text-white/70">
                    Isi data pegawai pengemudi kendaraan operasional baru.
                </p>
            </div>

            <a href="{{ route('pegawai.index') }}"
               class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>

        </div>

    </div>

</div>

{{-- ══════════════════════════════════════════ --}}
{{-- FORM                                     --}}
{{-- ══════════════════════════════════════════ --}}
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

    {{-- Global validation errors --}}
    @if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl px-5 py-4">
        <p class="text-red-700 text-sm font-semibold mb-1">
            <svg class="inline w-4 h-4 -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Terdapat {{ $errors->count() }} kesalahan input:
        </p>
        <ul class="list-disc list-inside text-red-600 text-xs space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 sm:p-8">

        <form action="{{ route('pegawai.store') }}" method="POST">
            @csrf

            {{-- Row 1: Nama + Jabatan --}}
            <div class="grid-2">
                <div>
                    <label class="label" for="nama">
                        Nama <span class="label-required">*</span>
                    </label>
                    <input
                        type="text" id="nama" name="nama"
                        value="{{ old('nama') }}"
                        placeholder="Nama lengkap pegawai"
                        required
                        class="field @error('nama') field-error @enderror">
                    @error('nama')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="jabatan">Jabatan</label>
                    <input
                        type="text" id="jabatan" name="jabatan"
                        value="{{ old('jabatan') }}"
                        placeholder="Contoh: Staff"
                        class="field @error('jabatan') field-error @enderror">
                    @error('jabatan')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Row 2: Divisi + No HP --}}
            <div class="grid-2 mt-5">
                <div>
                    <label class="label" for="divisi">Divisi</label>
                    <input
                        type="text" id="divisi" name="divisi"
                        value="{{ old('divisi') }}"
                        placeholder="Contoh: Operasional"
                        class="field @error('divisi') field-error @enderror">
                    @error('divisi')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="no_hp">No HP</label>
                    <input
                        type="text" id="no_hp" name="no_hp"
                        value="{{ old('no_hp') }}"
                        placeholder="Contoh: 081234567890"
                        class="field @error('no_hp') field-error @enderror">
                    @error('no_hp')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Simpan Pegawai
                </button>
                <a href="{{ route('pegawai.index') }}" class="btn-secondary">
                    Batal
                </a>
            </div>

        </form>

    </div>

</div>

@endsection
