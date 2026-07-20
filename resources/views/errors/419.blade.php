@extends('layout.app')

@section('title', '419 — Sesi Berakhir')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="text-center max-w-md">
        <div class="text-[8rem] sm:text-[10rem] font-display font-extrabold text-ta-red leading-none opacity-20 select-none">
            419
        </div>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-ta-ink mt-[-1rem] mb-3">
            Sesi Berakhir
        </h1>
        <p class="text-ta-muted text-sm leading-relaxed mb-8">
            Halaman ini sudah kadaluarsa. Silakan muat ulang halaman dan coba lagi. Biasanya terjadi karena halaman terlalu lama terbuka.
        </p>
        <a href="{{ url('/') }}"
           class="inline-flex items-center gap-2 bg-ta-red hover:bg-ta-dark text-white font-semibold text-sm px-6 py-3 rounded-2xl transition-all duration-150"
           onclick="event.preventDefault(); window.location.reload();">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Muat Ulang Halaman
        </a>
    </div>
</div>
@endsection
