<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kendaraan — Bensin Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        merah: { DEFAULT: '#c0392b', tua: '#922b21' }
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ── sticky footer ── */
        html { height: 100%; }
        body {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            background: #0a0a0a;
            margin: 0;
        }
        #wrap { flex: 1 0 auto; }

        /* nav pill */
        .nav-pill {
            color: #374151; font-weight: 500; font-size: 13px;
            padding: 7px 14px; border-radius: 999px;
            text-decoration: none; white-space: nowrap;
            transition: background .15s, color .15s;
        }
        .nav-pill:hover  { background: #f1f5f9; color: #111; }
        .nav-pill.active { background: #fee2e2; color: #c0392b; font-weight: 600; }

        /* scrollable nav wrapper */
        .nav-scroll {
            overflow-x: auto; -webkit-overflow-scrolling: touch;
            scrollbar-width: none; padding-bottom: 2px;
        }
        .nav-scroll::-webkit-scrollbar { display: none; }

        /* plat */
        .plat {
            display: inline-block;
            background: #fff; color: #111;
            border-left: 3px solid #c0392b;
            padding: 5px 10px; border-radius: 5px;
            font-weight: 800; font-size: 11px;
            letter-spacing: .12em; white-space: nowrap;
        }

        /* table hover */
        tbody tr { transition: background .1s; }
        tbody tr:hover { background: #161616; }

        .accent-bar {
            height: 2px;
            background: linear-gradient(90deg,#c0392b 0%,#e74c3c 40%,transparent 100%);
        }
    </style>
</head>
<body class="font-sans antialiased text-white">
<div id="wrap">

    {{-- ═══════════════ NAVBAR ═══════════════ --}}
    <div class="px-4 sm:px-8 pt-4 sm:pt-5">
        <div class="max-w-6xl mx-auto">
            <div class="nav-scroll">
                <nav class="bg-white inline-flex items-center gap-0.5 rounded-full px-1.5 py-1.5 shadow-sm">
                    <a href="{{ url('/') }}"                  class="nav-pill">Beranda</a>
                    <a href="{{ route('pegawai.index') }}"    class="nav-pill">Data Pegawai</a>
                    <a href="{{ route('kendaraan.index') }}"  class="nav-pill active">Data Kendaraan</a>
                    <a href="{{ route('perjalanan.index') }}" class="nav-pill">Data Perjalanan</a>
                    <a href="{{ url('/') }}"
                       class="ml-1.5 inline-flex items-center gap-1.5 bg-merah hover:bg-merah-tua
                              text-white font-semibold rounded-full px-4 py-2 transition-colors whitespace-nowrap"
                       style="font-size:13px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z
                                     M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z
                                     M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z
                                     M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Dashboard Monitoring
                    </a>
                </nav>
            </div>
        </div>
    </div>

    {{-- ═══════════════ MAIN ═══════════════ --}}
    <main class="max-w-6xl mx-auto px-4 sm:px-8 py-7 sm:py-10">

        {{-- Header row --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-5 sm:gap-4 mb-6 sm:mb-8">

            {{-- Title --}}
            <div>
                <p class="text-zinc-600 text-[10px] uppercase tracking-[.25em] font-semibold mb-1.5">
                    Armada Kendaraan
                </p>
                <h1 class="text-white text-2xl font-bold tracking-tight">
                    Data <span class="text-merah">Kendaraan</span>
                </h1>
                <div class="w-7 h-0.5 bg-merah mt-2.5 rounded-full"></div>
            </div>

            {{-- Stat + CTA — side by side always --}}
            <div class="flex items-center gap-3 sm:mt-1">
                <div class="border border-zinc-800 rounded-xl px-5 py-2.5 text-center bg-[#111] min-w-[64px]">
                    <p class="text-zinc-600 text-[9px] uppercase tracking-widest font-semibold">Total</p>
                    <p class="text-white font-bold text-xl leading-none mt-0.5">{{ $kendaraans->count() }}</p>
                </div>
                <a href="{{ route('kendaraan.create') }}"
                   class="inline-flex items-center gap-2 bg-merah hover:bg-merah-tua
                          text-white text-sm font-semibold px-5 py-3 rounded-xl
                          transition-colors whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="hidden sm:inline">Tambah Kendaraan</span>
                    <span class="sm:hidden">Tambah</span>
                </a>
            </div>
        </div>

        {{-- ── DESKTOP TABLE (md+) ── --}}
        <div class="hidden md:block bg-[#111] border border-zinc-800/80 rounded-2xl overflow-hidden">
            <div class="accent-bar"></div>
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-800/80" style="background:#141414;">
                        <th class="text-left text-zinc-600 font-semibold text-[10px] uppercase tracking-[.18em] px-6 py-3.5">Plat Nomor</th>
                        <th class="text-left text-zinc-600 font-semibold text-[10px] uppercase tracking-[.18em] px-6 py-3.5">Merk</th>
                        <th class="text-left text-zinc-600 font-semibold text-[10px] uppercase tracking-[.18em] px-6 py-3.5">Jenis</th>
                        <th class="text-left text-zinc-600 font-semibold text-[10px] uppercase tracking-[.18em] px-6 py-3.5">Tahun</th>
                        <th class="text-right text-zinc-600 font-semibold text-[10px] uppercase tracking-[.18em] px-6 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50">
                    @forelse ($kendaraans as $kendaraan)
                    @php
                        $j = strtolower($kendaraan->jenis);
                        if (str_contains($j,'motor')||$j==='r2') {
                            $dot='#f87171'; $pill='background:rgba(127,29,29,.4);color:#fca5a5;border:1px solid rgba(185,28,28,.35);';
                        } elseif (str_contains($j,'mobil')||$j==='r4') {
                            $dot='#60a5fa'; $pill='background:rgba(30,58,138,.4);color:#93c5fd;border:1px solid rgba(29,78,216,.35);';
                        } else {
                            $dot='#a1a1aa'; $pill='background:rgba(39,39,42,.8);color:#d4d4d8;border:1px solid rgba(63,63,70,.5);';
                        }
                    @endphp
                    <tr>
                        <td class="px-6 py-4"><span class="plat">{{ $kendaraan->plat_nomor }}</span></td>
                        <td class="px-6 py-4"><span class="text-white font-semibold text-sm">{{ $kendaraan->merk }}</span></td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-3 py-1 rounded-full" style="{{ $pill }}">
                                <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:{{ $dot }};"></span>
                                {{ $kendaraan->jenis }}
                            </span>
                        </td>
                        <td class="px-6 py-4"><span class="text-zinc-400 text-sm font-mono">{{ $kendaraan->tahun }}</span></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('kendaraan.edit', $kendaraan->id) }}"
                                   class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-300 hover:text-white
                                          px-3.5 py-2 rounded-lg border border-zinc-700 hover:border-zinc-500
                                          bg-zinc-800/60 hover:bg-zinc-700 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('kendaraan.destroy', $kendaraan->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus kendaraan ini?')"
                                            class="inline-flex items-center gap-1.5 text-xs font-medium text-red-400 hover:text-white
                                                   px-3.5 py-2 rounded-lg border border-red-900/50 hover:border-merah
                                                   bg-red-950/30 hover:bg-merah transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <p class="text-zinc-400 text-sm font-medium">Belum ada data kendaraan</p>
                            <p class="text-zinc-700 text-xs mt-1">Tambah kendaraan pertama untuk memulai</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── MOBILE CARDS (< md) ── --}}
        <div class="md:hidden space-y-3">
            @forelse ($kendaraans as $kendaraan)
            @php
                $j = strtolower($kendaraan->jenis);
                if (str_contains($j,'motor')||$j==='r2') {
                    $dot='#f87171'; $pill='background:rgba(127,29,29,.4);color:#fca5a5;border:1px solid rgba(185,28,28,.35);';
                } elseif (str_contains($j,'mobil')||$j==='r4') {
                    $dot='#60a5fa'; $pill='background:rgba(30,58,138,.4);color:#93c5fd;border:1px solid rgba(29,78,216,.35);';
                } else {
                    $dot='#a1a1aa'; $pill='background:rgba(39,39,42,.8);color:#d4d4d8;border:1px solid rgba(63,63,70,.5);';
                }
            @endphp
            <div style="background:#111;border:1px solid rgba(63,63,70,.6);border-radius:12px;padding:1rem;">
                {{-- Info row --}}
                <div class="flex items-center gap-3 mb-3">
                    <span class="plat">{{ $kendaraan->plat_nomor }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-semibold text-sm leading-tight truncate">{{ $kendaraan->merk }}</p>
                        <p class="text-zinc-500 text-xs font-mono mt-0.5">{{ $kendaraan->tahun }}</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-full shrink-0"
                          style="{{ $pill }}">
                        <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $dot }};"></span>
                        {{ $kendaraan->jenis }}
                    </span>
                </div>
                {{-- Action row --}}
                <div class="flex gap-2 pt-3 border-t border-zinc-800">
                    <a href="{{ route('kendaraan.edit', $kendaraan->id) }}"
                       class="flex-1 inline-flex items-center justify-center gap-1.5
                              text-xs font-medium text-zinc-300 hover:text-white
                              py-2.5 rounded-lg border border-zinc-700 bg-zinc-800/60
                              hover:bg-zinc-700 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('kendaraan.destroy', $kendaraan->id) }}" method="POST" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus kendaraan ini?')"
                                class="w-full inline-flex items-center justify-center gap-1.5
                                       text-xs font-medium text-red-400 hover:text-white
                                       py-2.5 rounded-lg border border-red-900/60
                                       bg-red-950/40 hover:bg-merah hover:border-merah transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-16">
                <p class="text-zinc-400 text-sm font-medium">Belum ada data kendaraan</p>
                <p class="text-zinc-700 text-xs mt-1">Tambah kendaraan pertama untuk memulai</p>
            </div>
            @endforelse
        </div>

        <p class="mt-3 text-zinc-700 text-xs px-1">
            Menampilkan <span class="text-zinc-500 font-semibold">{{ $kendaraans->count() }}</span> kendaraan
        </p>

    </main>
</div>{{-- #wrap --}}

{{-- ═══════════════ FOOTER — always sticks to bottom ═══════════════ --}}
<footer style="border-top:1px solid rgba(63,63,70,.35);flex-shrink:0;">
    <div class="max-w-6xl mx-auto px-4 sm:px-8 py-6 sm:py-7">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Brand --}}
            <div class="flex items-center gap-3 sm:gap-4">
                <img src="{{ asset('asset/foto/image.png') }}"
                     alt="Telkom Akses"
                     class="h-10 sm:h-12 w-auto object-contain"
                     style="filter:brightness(1.1);">
                <div class="hidden sm:block w-px h-8 bg-zinc-800"></div>
                <div>
                    <p class="text-zinc-300 text-xs font-semibold">Bensin Monitoring</p>
                    <p class="text-zinc-600 text-[10px] mt-0.5">PT Telkom Akses — Sistem Pemantauan BBM</p>
                </div>
            </div>
            {{-- Copyright --}}
            <p class="text-zinc-700 text-[10px]">&copy; {{ date('Y') }} PT Telkom Akses. All rights reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>