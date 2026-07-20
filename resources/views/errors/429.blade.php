@extends('layout.app')

@section('title', '429 — Terlalu Banyak Permintaan')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="text-center max-w-md">
        <div class="text-[8rem] sm:text-[10rem] font-display font-extrabold text-ta-red leading-none opacity-20 select-none">
            429
        </div>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-ta-ink mt-[-1rem] mb-3">
            Terlalu Banyak Permintaan
        </h1>
        <p class="text-ta-muted text-sm leading-relaxed mb-8">
            Anda mengirim terlalu banyak permintaan dalam waktu singkat. Harap tunggu beberapa saat sebelum mencoba lagi.
        </p>
        <a href="{{ url('/') }}"
           class="inline-flex items-center gap-2 bg-ta-red hover:bg-ta-dark text-white font-semibold text-sm px-6 py-3 rounded-2xl transition-all duration-150">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
