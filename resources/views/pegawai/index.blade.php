@extends('layout.app')

@section('title', 'Data Pegawai')
@section('meta_description', 'Kelola dan pantau seluruh data pegawai pengemudi kendaraan operasional PT. Telkom Akses Binjai.')

@push('styles')
<style>
    /* ── Palet brand khusus halaman ini (dipakai di hero & tabel gelap) ── */
    .hero-bg-pegawai {
        background: linear-gradient(135deg, #3a0000 0%, #6b0000 40%, #9d0000 75%, #E2001A 100%);
        position: relative;
    }
    .hero-bg-pegawai::after {
        content: '';
        position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }

    .trow { transition: background .15s ease; cursor: default; }
    .trow:hover { background: rgba(255,255,255,0.04); }
    .trow:hover .name-text { color: #ffdada; }
    .av-circle { transition: transform .2s cubic-bezier(.34,1.56,.64,1); }
    .trow:hover .av-circle { transform: scale(1.1); }

    [data-tip] { position: relative; }
    [data-tip]::after {
        content: attr(data-tip);
        position: absolute; bottom: calc(100% + 8px); left: 50%;
        transform: translateX(-50%) translateY(4px) scale(.9);
        background: #111827; color: #f9fafb;
        font-size: 11px; font-weight: 500; padding: 5px 10px;
        border-radius: 8px; white-space: nowrap;
        opacity: 0; pointer-events: none;
        transition: all .18s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,.25);
    }
    [data-tip]:hover::after { opacity: 1; transform: translateX(-50%) translateY(0) scale(1); }

    .modal-wrap {
        display: none;
        position: fixed; inset: 0; z-index: 9999;
        background: rgba(0,0,0,.5);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        align-items: center; justify-content: center; padding: 1rem;
    }
    .modal-wrap.open { display: flex; }
    .modal-box {
        background: #fff;
        border-radius: 24px;
        width: 100%; max-width: 380px;
        padding: 2.25rem 2rem;
        text-align: center;
        box-shadow: 0 25px 60px rgba(0,0,0,.2), 0 0 0 1px rgba(0,0,0,.05);
        transform: scale(.9) translateY(16px);
        opacity: 0;
        transition: transform .28s cubic-bezier(.22,1,.36,1), opacity .28s ease;
    }
    .modal-wrap.open .modal-box { transform: scale(1) translateY(0); opacity: 1; }

    .shimmer-bar {
        height: 3px;
        background: linear-gradient(90deg, #7a000d, #E2001A, #ff4d5e, #E2001A, #7a000d);
        background-size: 200% 100%;
        animation: shimmer 2.2s linear infinite;
    }
    @keyframes shimmer { from { background-position: 200% 0; } to { background-position: -200% 0; } }

    .search-ring:focus-within {
        box-shadow: 0 0 0 3px rgba(226,0,26,.15), 0 1px 3px rgba(0,0,0,.08);
        border-color: #E2001A !important;
    }

    .row-hidden { opacity: 0; transform: translateY(8px); }

    @keyframes fadeUp { from { opacity:0; transform:translateY(16px);} to { opacity:1; transform:translateY(0);} }
    .animate-fade-up { animation: fadeUp .4s cubic-bezier(.22,1,.36,1) both; }
    @keyframes slideRight { from { opacity:0; transform:translateX(-12px);} to { opacity:1; transform:translateX(0);} }
    .animate-slide-right { animation: slideRight .35s cubic-bezier(.22,1,.36,1) both; }
    @keyframes pulse2 { 0%,100% { opacity:1; } 50% { opacity:.5; } }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════ --}}
{{-- HERO                                     --}}
{{-- ══════════════════════════════════════════ --}}
<div class="hero-bg-pegawai relative w-full overflow-hidden">

    {{-- Background Decoration --}}
    <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full border-[48px] border-white/[.05] pointer-events-none"></div>
    <div class="absolute top-6 right-1/3 w-44 h-44 rounded-full border-[28px] border-white/[.04] pointer-events-none"></div>
    <div class="absolute -bottom-12 left-1/4 w-56 h-56 rounded-full border-[36px] border-white/[.03] pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-8">

        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            {{-- Text --}}
            <div>
                <h1 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                    Data Pegawai
                </h1>

                <p class="mt-2 max-w-xl text-sm text-white/70">
                    Kelola dan pantau seluruh data pegawai pengemudi kendaraan operasional
                    PT. Telkom Akses Binjai.
                </p>
            </div>

            {{-- Button --}}
            <div class="flex-shrink-0">
                <a href="{{ route('pegawai.create') }}"
                    class="group inline-flex items-center gap-3 rounded-2xl border border-white/80 bg-white px-6 py-3 text-sm font-bold text-ta-dark shadow-xl transition-all duration-300 hover:-translate-y-1 hover:bg-ta-soft hover:shadow-2xl">

                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-xl bg-ta-red transition-colors group-hover:bg-ta-dark">

                        <svg class="h-4 w-4 text-white"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4v16m8-8H4"/>
                        </svg>

                    </span>

                    Tambah Pegawai
                </a>
            </div>

        </div>

    </div>

</div>

{{-- ══════════════════════════════════════════ --}}
{{-- KONTEN                                     --}}
{{-- ══════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">

    {{-- Flash message --}}
    @if(session('success'))
    <div id="flash"
        class="animate-slide-right flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-2xl px-5 py-4 shadow-sm">
        <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <span>{{ session('success') }}</span>
        <button onclick="dismissFlash()" class="ml-auto w-7 h-7 flex items-center justify-center rounded-lg hover:bg-emerald-100 text-emerald-500 transition-colors text-lg leading-none">&times;</button>
    </div>
    @endif

    {{-- Stat + Search row --}}
    <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-up">

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-4 rounded-2xl px-5 py-4 shadow-lg border border-black/10"
                 style="background: linear-gradient(135deg, #7A000D, #3a0000);">
                <div class="w-12 h-12 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p id="countDisplay" class="font-display font-extrabold text-3xl text-white leading-none tabular-nums">{{ count($pegawais) }}</p>
                    <p class="text-[10.5px] font-semibold uppercase tracking-[.14em] text-white/50 mt-1">Total Pegawai</p>
                </div>
            </div>

            <div class="hidden sm:flex flex-col gap-2">
                <div class="flex items-center gap-2 bg-white rounded-xl px-3.5 py-2 shadow-sm border border-ta-border">
                    <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                    <span class="text-xs font-semibold text-gray-600">Aktif</span>
                    <span class="text-xs font-bold text-gray-900 ml-1">{{ count($pegawais) }}</span>
                </div>
                <div class="flex items-center gap-2 bg-white rounded-xl px-3.5 py-2 shadow-sm border border-ta-border">
                    <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                    <span class="text-xs font-semibold text-gray-600">Divisi</span>
                    @php $divisiCount = $pegawais->pluck('divisi')->unique()->count(); @endphp
                    <span class="text-xs font-bold text-gray-900 ml-1">{{ $divisiCount }}</span>
                </div>
            </div>
        </div>

        <div class="flex-1 min-w-[220px] max-w-sm">
            <div class="search-ring flex items-center gap-2.5 bg-white border border-ta-border rounded-2xl px-4 h-12 shadow-sm transition-all duration-200">
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input id="searchInput" type="text" placeholder="Cari nama, jabatan, divisi…"
                    class="flex-1 bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none">
                <button id="clearSearch"
                        class="hidden w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 transition-colors text-base leading-none">
                    &times;
                </button>
            </div>
        </div>
    </div>

    {{-- Table card --}}
    <div class="rounded-3xl overflow-hidden shadow-2xl animate-fade-up border border-black/20" style="animation-delay:.08s; background: #3a0000;">

        <div class="shimmer-bar"></div>

        @if(count($pegawais) > 0)

        <div class="px-6 pt-5 pb-3 flex items-center justify-between">
            <div>
                <h2 class="font-display font-bold text-white text-base">Daftar Pegawai</h2>
                <p class="text-white/40 text-xs mt-0.5">Semua pengemudi kendaraan operasional</p>
            </div>
            <div class="flex items-center gap-2">
                <span id="searchBadge" class="hidden items-center gap-1.5 bg-white/10 text-white/80 text-xs font-semibold px-3 py-1.5 rounded-full border border-white/15">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span id="searchBadgeText"></span>
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="pegawaiTable">
                <thead>
                    <tr style="background:rgba(0,0,0,0.25); border-bottom: 1px solid rgba(255,255,255,0.07);">
                        <th class="text-left text-[10px] font-bold uppercase tracking-[.18em] text-white/35 px-6 py-3.5 w-12">#</th>
                        <th class="text-left text-[10px] font-bold uppercase tracking-[.18em] text-white/35 px-4 py-3.5">Pegawai</th>
                        <th class="text-left text-[10px] font-bold uppercase tracking-[.18em] text-white/35 px-4 py-3.5">Jabatan</th>
                        <th class="text-left text-[10px] font-bold uppercase tracking-[.18em] text-white/35 px-4 py-3.5">Divisi</th>
                        <th class="text-left text-[10px] font-bold uppercase tracking-[.18em] text-white/35 px-4 py-3.5">No. HP</th>
                        <th class="text-right text-[10px] font-bold uppercase tracking-[.18em] text-white/35 px-6 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pegawais as $i => $pegawai)
                    @php
                        $palette  = ['#E2001A','#2563eb','#0891b2','#059669','#7c3aed','#d97706','#0e7490','#be185d'];
                        $color    = $palette[$i % count($palette)];
                        $words    = array_filter(explode(' ', $pegawai->nama));
                        $initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice($words, 0, 2)));
                    @endphp
                    <tr class="trow row-hidden border-b border-white/[0.05] last:border-b-0"
                        data-name="{{ strtolower($pegawai->nama) }}"
                        data-jabatan="{{ strtolower($pegawai->jabatan) }}"
                        data-divisi="{{ strtolower($pegawai->divisi) }}">

                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-white/25 tabular-nums">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        </td>

                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="av-circle relative w-10 h-10 rounded-full flex items-center justify-center text-white text-[12px] font-bold shrink-0 shadow-md ring-2 ring-white/10"
                                    style="background: {{ $color }};">
                                    {{ $initials }}
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-400 border-2" style="border-color:#3a0000;"></span>
                                </div>
                                <div>
                                    <p class="name-text font-semibold text-white text-[13.5px] leading-snug transition-colors duration-150">{{ $pegawai->nama }}</p>
                                    <p class="text-[11px] text-white/35 font-mono mt-0.5">ID #{{ str_pad($pegawai->id, 3, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1.5 bg-white/8 text-white/75 border border-white/12 text-[11.5px] font-semibold px-3 py-1.5 rounded-full">
                                <svg class="w-3 h-3 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $pegawai->jabatan }}
                            </span>
                        </td>

                        <td class="px-4 py-4">
                            <span class="inline-block bg-ta-red text-white text-[11.5px] font-semibold px-3 py-1.5 rounded-full shadow-sm">
                                {{ $pegawai->divisi }}
                            </span>
                        </td>

                        <td class="px-4 py-4">
                            <a href="tel:{{ $pegawai->no_hp }}"
                            class="group inline-flex items-center gap-2 text-[12.5px] text-white/50 hover:text-white transition-colors duration-150">
                                <span class="w-6 h-6 rounded-lg bg-white/8 border border-white/10 flex items-center justify-center group-hover:bg-ta-red group-hover:border-ta-red transition-all duration-150">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </span>
                                <span class="font-mono text-[12px]">{{ $pegawai->no_hp }}</span>
                            </a>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('pegawai.edit', $pegawai->id) }}"
                                data-tip="Edit pegawai"
                                class="group w-9 h-9 rounded-xl bg-blue-500/20 border border-blue-400/30 text-blue-300 hover:bg-blue-500 hover:text-white hover:border-blue-500 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-blue-500/30 flex items-center justify-center transition-all duration-150">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('pegawai.destroy', $pegawai->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            data-tip="Hapus pegawai"
                                            onclick="confirmDelete(this,'{{ addslashes($pegawai->nama) }}')"
                                            class="group w-9 h-9 rounded-xl bg-red-500/15 border border-red-400/25 text-red-300 hover:bg-ta-red hover:text-white hover:border-ta-red hover:-translate-y-0.5 hover:shadow-lg hover:shadow-red-600/30 flex items-center justify-center transition-all duration-150">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 flex items-center justify-between gap-4" style="background:rgba(0,0,0,0.2); border-top: 1px solid rgba(255,255,255,0.06);">
            <p class="text-[11.5px] text-white/35">
                Menampilkan
                <span id="visibleCount" class="text-white/70 font-bold tabular-nums">{{ count($pegawais) }}</span>
                dari
                <span class="text-white/70 font-bold tabular-nums">{{ count($pegawais) }}</span>
                pegawai
            </p>
            <span id="noResult" class="hidden text-[11.5px] text-amber-400/70 font-medium italic">
                ⚠ Tidak ada hasil untuk pencarian ini
            </span>
        </div>

        @else

        {{-- Empty state --}}
        <div class="py-28 flex flex-col items-center gap-5 animate-fade-up px-6 text-center">
            <div class="relative">
                <div class="w-20 h-20 rounded-3xl bg-white/8 border border-white/15 flex items-center justify-center">
                    <svg class="w-9 h-9 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-ta-red flex items-center justify-center">
                    <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
            </div>
            <div>
                <p class="font-display font-bold text-white text-lg">Belum Ada Pegawai</p>
                <p class="text-white/45 text-sm mt-1.5 max-w-xs mx-auto leading-relaxed">Tambahkan pegawai pertama untuk mulai memantau penggunaan BBM kendaraan operasional.</p>
            </div>
            <a href="{{ route('pegawai.create') }}"
            class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-ta-dark font-bold text-sm px-6 py-3 rounded-2xl transition-all hover:shadow-lg hover:-translate-y-0.5 duration-150">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Pegawai Pertama
            </a>
        </div>

        @endif
    </div>

</div>

{{-- ══════════════════════════════════════════ --}}
{{-- DELETE MODAL                               --}}
{{-- ══════════════════════════════════════════ --}}
<div id="deleteModal" class="modal-wrap" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-box" id="modalBox">
        <div class="w-16 h-16 rounded-2xl bg-ta-soft border-2 border-red-100 flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-ta-red" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <h3 id="modalTitle" class="font-display font-bold text-gray-900 text-lg mb-2">Hapus Pegawai?</h3>
        <p class="text-gray-500 text-sm mb-2 leading-relaxed">
            Anda akan menghapus data pegawai:
        </p>
        <div class="bg-gray-50 rounded-xl px-4 py-3 mb-6 border border-gray-100">
            <p id="deleteName" class="text-gray-900 font-bold text-[15px]"></p>
            <p class="text-gray-500 text-xs mt-0.5">Data tidak dapat dipulihkan setelah dihapus.</p>
        </div>

        <div class="flex gap-3">
            <button onclick="closeModal()"
                    class="flex-1 py-3 rounded-2xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 hover:border-gray-300 active:scale-95 transition-all duration-150">
                Batal
            </button>
            <button id="confirmBtn"
                    class="flex-1 py-3 rounded-2xl bg-ta-red hover:bg-ta-dark active:bg-ta-darker text-white font-semibold text-sm active:scale-95 transition-all duration-150 shadow-sm hover:shadow-md">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ─── Live search ─── */
const searchInput  = document.getElementById('searchInput');
const clearBtn     = document.getElementById('clearSearch');
const rows         = document.querySelectorAll('#pegawaiTable tbody tr');
const visibleCount = document.getElementById('visibleCount');
const noResult     = document.getElementById('noResult');
const searchBadge  = document.getElementById('searchBadge');
const searchBadgeText = document.getElementById('searchBadgeText');

function runSearch(kw) {
    let shown = 0;
    rows.forEach(r => {
        const hit = (r.dataset.name + r.dataset.jabatan + r.dataset.divisi).includes(kw);
        r.style.display = hit ? '' : 'none';
        if (hit) shown++;
    });
    if (visibleCount) visibleCount.textContent = shown;
    if (noResult) noResult.classList.toggle('hidden', shown > 0);
    if (clearBtn) clearBtn.classList.toggle('hidden', !kw);
    if (searchBadge) {
        if (kw) {
            searchBadge.classList.remove('hidden');
            searchBadge.classList.add('flex');
            if (searchBadgeText) searchBadgeText.textContent = `"${kw}" — ${shown} hasil`;
        } else {
            searchBadge.classList.add('hidden');
            searchBadge.classList.remove('flex');
        }
    }
}

searchInput && searchInput.addEventListener('input', e => runSearch(e.target.value.toLowerCase().trim()));
clearBtn && clearBtn.addEventListener('click', () => { searchInput.value = ''; runSearch(''); searchInput.focus(); });

/* ─── Delete modal ─── */
let pendingForm = null;

function confirmDelete(btn, name) {
    pendingForm = btn.closest('form');
    document.getElementById('deleteName').textContent = name;
    document.getElementById('deleteModal').classList.add('open');
}

function closeModal() {
    const box = document.getElementById('modalBox');
    box.style.transform = 'scale(.9) translateY(16px)';
    box.style.opacity   = '0';
    setTimeout(() => {
        document.getElementById('deleteModal').classList.remove('open');
        box.style.transform = '';
        box.style.opacity   = '';
    }, 260);
    pendingForm = null;
}

document.getElementById('confirmBtn') && document.getElementById('confirmBtn').addEventListener('click', () => {
    if (!pendingForm) return;
    const btn = document.getElementById('confirmBtn');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="w-4 h-4 animate-spin inline mr-1.5" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>Menghapus…`;
    pendingForm.submit();
});

document.getElementById('deleteModal') && document.getElementById('deleteModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeModal();
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
});

/* ─── Flash dismiss ─── */
function dismissFlash() {
    const flash = document.getElementById('flash');
    if (!flash) return;
    flash.style.transition = 'opacity .35s ease, transform .35s ease, max-height .35s ease, margin .35s ease, padding .35s ease';
    flash.style.opacity    = '0';
    flash.style.transform  = 'translateX(16px)';
    flash.style.maxHeight  = '0';
    flash.style.margin     = '0';
    flash.style.padding    = '0';
    setTimeout(() => flash.remove(), 380);
}

const flash = document.getElementById('flash');
if (flash) setTimeout(dismissFlash, 4500);

/* ─── Row entrance stagger ─── */
const tableRows = document.querySelectorAll('#pegawaiTable tbody tr');
tableRows.forEach((row, i) => {
    row.style.transition = 'opacity .3s ease, transform .3s cubic-bezier(.22,1,.36,1)';
    setTimeout(() => {
        row.style.opacity   = '1';
        row.style.transform = 'translateY(0)';
        row.classList.remove('row-hidden');
    }, 80 + i * 50);
});

/* ─── Stat counter animation ─── */
const countEl = document.getElementById('countDisplay');
if (countEl) {
    const target = parseInt(countEl.textContent) || 0;
    if (target > 1) {
        let current = 0;
        const step = Math.ceil(target / 20);
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            countEl.textContent = current;
            if (current >= target) clearInterval(timer);
        }, 40);
    }
}
</script>
@endpush