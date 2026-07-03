@extends('layout.app')

@section('title', 'AI Analyst')
@section('meta_description', 'Analisis otomatis kendaraan operasional dan efisiensi BBM berbasis AI.')

@push('styles')
<style>
    @keyframes fadeUp { from { opacity:0; transform:translateY(16px);} to { opacity:1; transform:translateY(0);} }
    .animate-fade-up { animation: fadeUp .45s cubic-bezier(.22,1,.36,1) both; }

    .ai-hero {
        background: linear-gradient(135deg, #8f0012 0%, #d30b24 55%, #ff5d6f 100%);
        position: relative;
        overflow: hidden;
    }
    .ai-hero::after {
        content: '';
        position: absolute; inset: auto -80px -80px auto;
        width: 240px; height: 240px;
        background: rgba(255,255,255,0.10);
        border-radius: 50%;
    }
    .ai-hero::before {
        content: '';
        position: absolute; top: -60px; left: -60px;
        width: 180px; height: 180px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }

    .status-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 18px; border-radius: 999px;
        font-weight: 700; font-size: 0.9rem;
    }
    .status-pill.online  { color: #166534; background: #dcfce7; }
    .status-pill.offline { color: #991b1b; background: #fee2e2; }

    .metric-card {
        border-radius: 18px;
        border: 1px solid #f1d4d8;
        background: linear-gradient(180deg, #ffffff 0%, #fff9fa 100%);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .metric-card:hover { transform: translateY(-4px); box-shadow: 0 14px 32px rgba(177,0,20,.12); }

    /* ── Styling hasil analisis AI (dari parser markdown-lite) ── */
    .ai-result h4 {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1.02rem;
        color: #1A1A2E;
        margin: 0 0 14px;
        padding-bottom: 10px;
        border-bottom: 2px solid #FEE8EB;
    }
    .ai-result p { color: #374151; line-height: 1.8; margin-bottom: 14px; }
    .ai-result ol.ai-numbered { list-style: none; margin: 0 0 18px; padding: 0; counter-reset: ai-counter; }
    .ai-result ol.ai-numbered > li {
        counter-increment: ai-counter;
        position: relative;
        padding: 12px 16px 12px 46px;
        margin-bottom: 10px;
        background: #fff9fa;
        border: 1px solid #f6dde1;
        border-radius: 14px;
        color: #374151;
        line-height: 1.75;
    }
    .ai-result ol.ai-numbered > li::before {
        content: counter(ai-counter);
        position: absolute; left: 12px; top: 12px;
        width: 24px; height: 24px;
        background: linear-gradient(135deg, #E2001A, #B10014);
        color: #fff; font-weight: 700; font-size: 0.78rem;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
    }
    .ai-result ul.ai-bullets { margin: 0 0 18px; padding: 0; list-style: none; }
    .ai-result ul.ai-bullets > li {
        position: relative;
        padding-left: 22px;
        margin-bottom: 8px;
        color: #4b5563;
        line-height: 1.75;
        font-size: 0.94rem;
    }
    .ai-result ul.ai-bullets > li::before {
        content: '';
        position: absolute; left: 0; top: 9px;
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #E2001A;
    }
    .ai-result strong { color: #B10014; font-weight: 700; }
    .ai-result .ai-note {
        margin-top: 18px;
        padding: 12px 16px;
        background: #FEE8EB;
        border-radius: 12px;
        font-size: 0.85rem;
        color: #7A000D;
        font-style: italic;
    }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════ --}}
{{-- HERO                                        --}}
{{-- ══════════════════════════════════════════ --}}
<div class="ai-hero">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 relative z-10">
        <span class="eyebrow text-white before:bg-white mb-3">AI Analyst</span>
        <h1 class="font-display font-extrabold text-white text-3xl sm:text-4xl leading-tight tracking-tight mt-2">
            AI Analyst
        </h1>
        <p class="text-white/70 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
            Dashboard analisis kendaraan operasional untuk membaca tren efisiensi, penggunaan BBM,
            dan ringkasan rekomendasi otomatis dari Local AI yang saya Build.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">

    {{-- Status + Ringkasan --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 animate-fade-up">

        {{-- Status AI --}}
        <div class="lg:col-span-4 bg-white rounded-ta shadow-ta border border-ta-border overflow-hidden">
            <div class="px-6 py-4" style="background: linear-gradient(135deg, #E2001A, #B10014);">
                <h2 class="font-display font-semibold text-white text-[15px]">Status AI</h2>
            </div>
            <div class="p-6">
                <p class="text-ta-muted text-sm mb-4 leading-relaxed">
                    Status koneksi layanan Ollama lokal untuk analisis kendaraan.
                </p>
                <span class="status-pill {{ $isOllamaConnected ? 'online' : 'offline' }}">
                    @if($isOllamaConnected)
                        <i class="fa-solid fa-circle-check"></i> Ollama Connected
                    @else
                        <i class="fa-solid fa-circle-xmark"></i> Ollama Offline
                    @endif
                </span>
            </div>
        </div>

        {{-- Ringkasan metrik --}}
        <div class="lg:col-span-8 bg-white rounded-ta shadow-ta border border-ta-border overflow-hidden">
            <div class="px-6 py-4" style="background: linear-gradient(135deg, #E2001A, #B10014);">
                <h2 class="font-display font-semibold text-white text-[15px]">Informasi Ringkas</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="metric-card p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[.12em] text-ta-muted mb-2">Jumlah Kendaraan</p>
                        <p class="font-display font-extrabold text-2xl text-ta-ink">{{ number_format($totalKendaraan ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-1.5">unit terdaftar</p>
                    </div>
                    <div class="metric-card p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[.12em] text-ta-muted mb-2">Jumlah Pegawai</p>
                        <p class="font-display font-extrabold text-2xl text-ta-ink">{{ number_format($totalPegawai ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-1.5">pegawai aktif</p>
                    </div>
                    <div class="metric-card p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[.12em] text-ta-muted mb-2">Jumlah Perjalanan</p>
                        <p class="font-display font-extrabold text-2xl text-ta-ink">{{ number_format($totalPerjalanan ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-1.5">data perjalanan</p>
                    </div>
                    <div class="metric-card p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[.12em] text-ta-muted mb-2">Rata-rata Efisiensi</p>
                        <p class="font-display font-extrabold text-2xl text-ta-ink">{{ number_format((float) ($rataEfisiensi ?? 0), 2, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-1.5">km/liter</p>
                    </div>
                    <div class="metric-card p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[.12em] text-ta-muted mb-2">Total BBM</p>
                        <p class="font-display font-extrabold text-2xl text-ta-ink">{{ number_format((float) ($totalPenggunaanBbm ?? 0), 2, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-1.5">liter</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hasil Analisis AI --}}
    <div class="bg-white rounded-ta shadow-ta border border-ta-border overflow-hidden animate-fade-up" style="animation-delay:.08s">
        <div class="px-6 py-4 flex items-center gap-3" style="background: linear-gradient(135deg, #E2001A, #B10014);">
            <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-wand-magic-sparkles text-white text-sm"></i>
            </div>
            <h2 class="font-display font-semibold text-white text-[15px]">Hasil Analisis AI</h2>
        </div>
        <div class="p-6 sm:p-7">
            <p class="text-ta-muted text-sm mb-5">
                Ringkasan analisis otomatis berdasarkan data kendaraan dan perjalanan terbaru.
            </p>

            @php
                /**
                 * Parser markdown-lite: mengubah teks hasil AI (dengan **bold**,
                 * penomoran "1. **Label:** ..." dan bullet "* ...") jadi HTML rapi,
                 * tetap aman (di-escape dulu sebelum diubah jadi tag).
                 */
                $renderAiResult = function (?string $raw): string {
                    $raw = trim((string) $raw);
                    if ($raw === '') {
                        return '<p class="text-gray-400 italic">Tidak ada analisis.</p>';
                    }

                    $blocks = preg_split('/\n\s*\n/', $raw);
                    $html = '';

                    foreach ($blocks as $block) {
                        $block = trim($block);
                        if ($block === '') continue;

                        $lines = preg_split('/\n/', $block);
                        $firstLine = trim($lines[0]);

                        // Blok heading tunggal: **Judul...**
                        if (count($lines) === 1 && preg_match('/^\*\*(.+)\*\*$/', $firstLine, $m)) {
                            $html .= '<h4>' . e(trim($m[1])) . '</h4>';
                            continue;
                        }

                        // Blok catatan: diawali "**Catatan"
                        if (preg_match('/^\*\*Catatan/i', $firstLine)) {
                            $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', e($block));
                            $html .= '<p class="ai-note">' . nl2br($text) . '</p>';
                            continue;
                        }

                        // Blok list bernomor: "1. ...", "2. ..." dst
                        if (preg_match('/^\d+\.\s/', $firstLine)) {
                            $html .= '<ol class="ai-numbered">';
                            foreach ($lines as $line) {
                                $line = trim($line);
                                if ($line === '') continue;
                                $line = preg_replace('/^\d+\.\s*/', '', $line);
                                $line = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', e($line));
                                $html .= '<li>' . $line . '</li>';
                            }
                            $html .= '</ol>';
                            continue;
                        }

                        // Blok bullet: "* ..."
                        if (preg_match('/^\*\s/', $firstLine)) {
                            $html .= '<ul class="ai-bullets">';
                            foreach ($lines as $line) {
                                $line = trim($line);
                                if ($line === '') continue;
                                $line = preg_replace('/^\*\s*/', '', $line);
                                $line = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', e($line));
                                $html .= '<li>' . $line . '</li>';
                            }
                            $html .= '</ul>';
                            continue;
                        }

                        // Paragraf biasa
                        $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', e($block));
                        $html .= '<p>' . nl2br($text) . '</p>';
                    }

                    return $html;
                };
            @endphp

            <div class="ai-result">
                {!! $renderAiResult($hasil ?? null) !!}
            </div>
        </div>
    </div>

</div>

@endsection