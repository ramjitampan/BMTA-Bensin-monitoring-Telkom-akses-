<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistem Monitoring BBM Kendaraan Operasional | PT. Telkom Akses Binjai</title>
<meta name="description" content="Sistem Monitoring BBM Kendaraan Operasional PT. Telkom Akses Binjai — kelola, monitor, dan evaluasi penggunaan BBM kendaraan operasional secara efektif dan terukur.">

{{-- Font Awesome 6 --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
{{-- Google Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
{{-- Tailwind CSS CDN --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        display: ['Poppins', 'sans-serif'],
      },
      colors: {
        ta: {
          red:        '#E2001A',
          dark:       '#B10014',
          darker:     '#7A000D',
          soft:       '#FEE8EB',
          ink:        '#1A1A2E',
          muted:      '#6B7280',
          bg:         '#F8F8FA',
          border:     '#E5E7EB',
        }
      },
      boxShadow: {
        'ta':    '0 10px 30px rgba(26,26,46,0.07)',
        'ta-lg': '0 20px 48px rgba(177,0,20,0.18)',
        'ta-xl': '0 32px 64px rgba(177,0,20,0.22)',
      },
      borderRadius: {
        'ta': '18px',
      }
    }
  }
}
</script>
<style>
  body { font-family: 'Inter', sans-serif; color: #1A1A2E; background: #fff; overflow-x: hidden; }
  h1,h2,h3,h4,h5,.font-display { font-family: 'Poppins', sans-serif; }

  /* Eyebrow */
  .eyebrow { display: inline-flex; align-items: center; gap: 8px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: #E2001A; }
  .eyebrow::before { content: ''; width: 22px; height: 3px; background: #E2001A; border-radius: 4px; display: inline-block; }

  /* Navbar scroll shadow */
  .navbar-scrolled { box-shadow: 0 8px 32px rgba(26,26,46,0.10); }

  /* Hero gradient */
  .hero-bg { background: linear-gradient(135deg, #E2001A 0%, #B10014 55%, #7A000D 100%); }

  /* Dashed API line */
  .api-line { flex: 1; height: 2px; background: repeating-linear-gradient(90deg, #E2001A 0 8px, transparent 8px 14px); min-width: 24px; }

  /* Feature card top bar */
  .feature-card { position: relative; overflow: hidden; transition: transform .25s, box-shadow .25s, border-color .25s; }
  .feature-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #E2001A, #B10014); transform: scaleX(0); transform-origin: left; transition: transform .3s ease; }
  .feature-card:hover::before { transform: scaleX(1); }
  .feature-card:hover { transform: translateY(-8px); box-shadow: 0 20px 48px rgba(177,0,20,0.16); border-color: transparent !important; }
  .feature-icon { transition: background .25s, color .25s; }
  .feature-card:hover .feature-icon { background: linear-gradient(135deg, #E2001A, #B10014) !important; color: #fff !important; }

  /* Stat card hover */
  .stat-card { transition: transform .25s, box-shadow .25s; }
  .stat-card:hover { transform: translateY(-6px); box-shadow: 0 20px 48px rgba(177,0,20,0.18); }

  /* Flow step hover */
  .flow-step { transition: transform .25s, box-shadow .25s; }
  .flow-step:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(26,26,46,0.08); }

  /* Hero btn */
  .btn-hero-primary { transition: transform .2s, box-shadow .2s; }
  .btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(0,0,0,0.2); }
  .btn-hero-outline { transition: transform .2s, background .2s, border-color .2s; }
  .btn-hero-outline:hover { transform: translateY(-3px); background: rgba(255,255,255,0.12); border-color: #fff; }

  /* Nav link */
  .nav-link-item { font-weight: 600; font-size: 0.93rem; padding: 8px 14px; border-radius: 8px; transition: color .18s, background .18s; color: #1A1A2E; }
  .nav-link-item:hover, .nav-link-item.active { color: #E2001A; background: #FEE8EB; }

  /* Footer link */
  .footer-link { color: rgba(255,255,255,0.72); transition: color .18s, padding-left .18s; display: inline-block; text-decoration: none; }
  .footer-link:hover { color: #fff; padding-left: 4px; }

  /* About badge */
  .about-badge { background: rgba(255,255,255,0.13); border: 1px solid rgba(255,255,255,0.24); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px; font-weight: 600; font-size: 0.91rem; color: #fff; margin-bottom: 10px; }

  /* Wave */
  .hero-wave { position: absolute; left: 0; right: 0; bottom: -2px; line-height: 0; z-index: 1; }
  .hero-wave svg { width: 100%; height: 70px; display: block; }

  /* Mobile menu */
  #mobileMenu { display: none; }
  #mobileMenu.open { display: flex; }

  /* API diagram box */
  .api-box { background: #fff; border: 1px solid #E5E7EB; border-radius: 16px; padding: 20px 16px; text-align: center; box-shadow: 0 10px 30px rgba(26,26,46,0.07); width: 148px; flex-shrink: 0; }
  .api-box .icon-wrap { width: 52px; height: 52px; border-radius: 50%; background: #FEE8EB; color: #E2001A; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; margin: 0 auto 10px; }
  .api-box.center-box { border-color: #E2001A; }
  .api-box.center-box .icon-wrap { background: linear-gradient(135deg, #E2001A, #B10014); color: #fff; }
  .api-box span { font-size: 0.83rem; font-weight: 700; color: #1A1A2E; }

  @media (prefers-reduced-motion: reduce) { * { animation: none !important; transition: none !important; scroll-behavior: auto !important; } }
  a:focus-visible, button:focus-visible { outline: 3px solid #B10014; outline-offset: 2px; }
</style>
</head>
<body class="antialiased">

{{-- ============ NAVBAR ============ --}}
<nav id="mainNavbar" class="sticky top-0 z-50 bg-white border-b border-ta-border py-3 transition-shadow duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">

    {{-- Logo --}}
    <a href="{{ url('/') }}" class="flex items-center gap-3 no-underline group">
      {{-- Ganti div ini dengan: <img src="{{ asset('images/logo-telkom-akses.png') }}" class="w-11 h-11 rounded-xl object-cover"> --}}
      <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-ta-red to-ta-dark flex items-center justify-center text-white font-display font-extrabold text-[0.95rem] shadow-[0_6px_18px_rgba(226,0,26,0.38)] shrink-0 transition-transform duration-200 group-hover:scale-105">
        TA
      </div>
      <span class="leading-tight">
        <span class="block text-[0.67rem] font-semibold tracking-widest uppercase text-ta-muted">PT. Telkom Akses Binjai</span>
        <span class="block font-display font-bold text-[1.01rem] text-ta-ink group-hover:text-ta-red transition-colors">Monitoring BBM</span>
      </span>
    </a>

    {{-- Desktop Nav --}}
    <div class="hidden lg:flex items-center gap-1">
      <a href="#beranda" class="nav-link-item active">Beranda</a>
      <a href="{{ route('pegawai.index') }}" class="nav-link-item">Data Pegawai</a>
      <a href="{{ route('kendaraan.index') }}" class="nav-link-item">Data Kendaraan</a>
      <a href="{{ route('perjalanan.index') }}" class="nav-link-item">Data Perjalanan</a>
      <a href="#" class="ml-3 inline-flex items-center gap-2 bg-gradient-to-br from-ta-red to-ta-dark text-white font-semibold text-[0.93rem] px-5 py-2.5 rounded-full shadow-[0_6px_18px_rgba(226,0,26,0.32)] hover:brightness-105 hover:-translate-y-[1px] transition-all">
        <i class="fa-solid fa-gauge-high text-sm"></i> Dashboard Monitoring
      </a>
    </div>

    {{-- Mobile toggle --}}
    <button id="mobileMenuBtn" class="lg:hidden p-2 rounded-lg text-ta-ink hover:text-ta-red hover:bg-ta-soft focus:outline-none transition-colors" aria-label="Buka menu">
      <i id="menuIcon" class="fa-solid fa-bars text-xl"></i>
    </button>
  </div>

  {{-- Mobile menu --}}
  <div id="mobileMenu" class="lg:hidden flex-col gap-1 px-4 pb-4 pt-3 border-t border-ta-border mt-3 bg-white">
    <a href="#beranda" class="nav-link-item active block">Beranda</a>
    <a href="{{ route('pegawai.index') }}" class="nav-link-item block">Data Pegawai</a>
    <a href="{{ route('kendaraan.index') }}" class="nav-link-item block">Data Kendaraan</a>
    <a href="{{ route('perjalanan.index') }}" class="nav-link-item block">Data Perjalanan</a>
    <a href="#" class="mt-2 flex items-center justify-center gap-2 bg-gradient-to-br from-ta-red to-ta-dark text-white font-semibold text-sm px-5 py-2.5 rounded-full shadow-[0_6px_18px_rgba(226,0,26,0.28)]">
      <i class="fa-solid fa-gauge-high"></i> Dashboard Monitoring
    </a>
  </div>
</nav>

{{-- ============ HERO ============ --}}
<header class="hero-bg relative text-white pt-20 pb-32 sm:pt-24 sm:pb-36 overflow-hidden" id="beranda">
  {{-- Decorative circles --}}
  <div class="absolute w-[420px] h-[420px] rounded-full bg-white/[0.07] -top-40 -right-28 pointer-events-none"></div>
  <div class="absolute w-[280px] h-[280px] rounded-full bg-white/[0.05] -bottom-28 -left-16 pointer-events-none"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-center">

      {{-- Left copy --}}
      <div class="lg:col-span-7">
        <span class="inline-flex items-center gap-2 bg-white/[0.13] border border-white/[0.24] text-white text-xs font-semibold px-4 py-1.5 rounded-full mb-5">
          <i class="fa-solid fa-circle-check"></i> Sistem Internal PT. Telkom Akses Binjai
        </span>
        <h1 class="font-display font-extrabold text-[clamp(2rem,1.4rem+2.4vw,3.2rem)] leading-[1.18] mb-4">
          Sistem Monitoring BBM Kendaraan Operasional
        </h1>
        <p class="text-[1.06rem] text-white/90 max-w-[560px] mb-8 leading-relaxed">
          Membantu PT. Telkom Akses Binjai dalam mengelola, memonitor, dan mengevaluasi penggunaan BBM kendaraan operasional secara efektif dan terukur.
        </p>
        <div class="flex flex-wrap gap-3">
          <a href="{{ route('perjalanan.create') }}" class="btn-hero-primary bg-white text-ta-dark font-bold rounded-full px-7 py-3.5 border-2 border-white inline-flex items-center gap-2">
            <i class="fa-solid fa-play text-sm"></i> Mulai Monitoring
          </a>
          <a href="{{ route('perjalanan.index') }}" class="btn-hero-outline text-white font-bold rounded-full px-7 py-3.5 border-2 border-white/60 inline-flex items-center gap-2">
            <i class="fa-solid fa-chart-line text-sm"></i> Lihat Dashboard
          </a>
        </div>
      </div>

      {{-- Right visual card --}}
      <div class="lg:col-span-5">
        <div class="bg-white/[0.10] border border-white/[0.24] rounded-[20px] p-7 backdrop-blur-md shadow-[0_8px_32px_rgba(0,0,0,0.18)]">
          <div class="w-16 h-16 rounded-full bg-white/[0.18] flex items-center justify-center text-3xl mb-4">
            <i class="fa-solid fa-gas-pump"></i>
          </div>
          <h5 class="font-display font-bold text-xl mb-2">Pantau Konsumsi BBM Real-time</h5>
          <p class="text-white/82 text-[0.91rem] leading-relaxed mb-6">
            Semua catatan perjalanan, bon BBM, dan kendaraan operasional terhubung dalam satu sistem terpadu.
          </p>
          <div class="grid grid-cols-2 gap-3">
            <div class="bg-white/[0.10] rounded-2xl p-4">
              <div class="font-display font-bold text-2xl">32</div>
              <div class="text-xs text-white/78 mt-1">Kendaraan Aktif</div>
            </div>
            <div class="bg-white/[0.10] rounded-2xl p-4">
              <div class="font-display font-bold text-2xl">99%</div>
              <div class="text-xs text-white/78 mt-1">Akurasi Pencatatan</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="hero-wave">
    <svg viewBox="0 0 1440 100" preserveAspectRatio="none">
      <path fill="#ffffff" d="M0,40 C240,90 480,0 720,30 C960,60 1200,90 1440,40 L1440,100 L0,100 Z"></path>
    </svg>
  </div>
</header>

{{-- ============ STATISTIK ============ --}}
@php

if (!function_exists('formatRupiahSingkat')) {
    function formatRupiahSingkat($angka)
    {
        if ($angka >= 1000000000) {
            return 'Rp ' . number_format($angka / 1000000000, 1, ',', '.') . ' M';
        }

        if ($angka >= 1000000) {
            return 'Rp ' . number_format($angka / 1000000, 1, ',', '.') . ' Jt';
        }

        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

$statistik = [
    [
        'icon' => 'fa-users',
        'label' => 'Total Pegawai',
        'value' => $totalPegawai,
    ],
    [
        'icon' => 'fa-truck',
        'label' => 'Total Kendaraan',
        'value' => $totalKendaraan,
    ],
    [
        'icon' => 'fa-route',
        'label' => 'Total Perjalanan',
        'value' => $totalPerjalanan,
    ],
    [
        'icon' => 'fa-gas-pump',
        'label' => 'Total Pengeluaran BBM',
        'value' => $totalBBM,
        'is_rupiah' => true,
    ],
];
@endphp

<section class="-mt-14 relative z-20 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">

            @foreach ($statistik as $item)
            <div class="stat-card bg-white rounded-ta shadow-ta border border-ta-border p-6">

                <div class="w-[52px] h-[52px] rounded-[14px] flex items-center justify-center bg-gradient-to-br from-ta-red to-ta-dark text-white text-xl mb-4 shadow-[0_8px_18px_rgba(226,0,26,0.28)]">
                    <i class="fa-solid {{ $item['icon'] }}"></i>
                </div>

                @if(isset($item['is_rupiah']))
                    <div class="font-display font-extrabold text-[1.85rem] text-ta-ink leading-tight">
                        {{ formatRupiahSingkat($item['value']) }}
                    </div>
                @else
                    <div class="font-display font-extrabold text-[1.85rem] text-ta-ink leading-tight">
                        <span class="counter" data-target="{{ $item['value'] }}">0</span>
                    </div>
                @endif

                <div class="text-ta-muted text-[0.88rem] font-medium mt-1">
                    {{ $item['label'] }}
                </div>

            </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ============ FITUR SISTEM ============ --}}
@php
  $fitur = [
    ['icon' => 'fa-users',              'title' => 'Data Pegawai',    'desc' => 'Kelola data pengemudi dan pegawai yang bertanggung jawab atas kendaraan operasional.'],
    ['icon' => 'fa-truck',              'title' => 'Data Kendaraan',  'desc' => 'Catat identitas, jenis, dan status seluruh kendaraan operasional perusahaan.'],
    ['icon' => 'fa-route',              'title' => 'Data Perjalanan', 'desc' => 'Rekam setiap perjalanan dinas lengkap dengan rute, jarak, dan tujuan.'],
    ['icon' => 'fa-gas-pump',           'title' => 'Monitoring BBM',  'desc' => 'Pantau konsumsi BBM setiap kendaraan secara berkala dan akurat.'],
    ['icon' => 'fa-receipt',            'title' => 'Upload Bon',      'desc' => 'Unggah bukti pembelian BBM sebagai dasar verifikasi dan pelaporan.'],
    ['icon' => 'fa-chart-pie',          'title' => 'Laporan & Rekap', 'desc' => 'Hasilkan laporan dan rekap penggunaan BBM untuk evaluasi manajemen.'],
  ];
@endphp
<section class="py-20 md:py-28" id="fitur">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-14">
      <span class="eyebrow justify-center mb-3 block">Fitur Sistem</span>
      <h2 class="font-display font-bold text-[clamp(1.6rem,1.2rem+1.6vw,2.4rem)] mb-3 text-ta-ink">Semua Kebutuhan Monitoring dalam Satu Sistem</h2>
      <p class="text-ta-muted text-[1.01rem]">Dirancang khusus untuk mendukung operasional PT. Telkom Akses Binjai secara menyeluruh, dari pencatatan data hingga pelaporan.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach ($fitur as $f)
      <div class="feature-card bg-white rounded-ta border border-ta-border p-8">
        <div class="feature-icon w-[56px] h-[56px] rounded-2xl bg-ta-soft text-ta-red flex items-center justify-center text-[1.4rem] mb-5">
          <i class="fa-solid {{ $f['icon'] }}"></i>
        </div>
        <h3 class="font-display font-bold text-[1.05rem] mb-2 text-ta-ink">{{ $f['title'] }}</h3>
        <p class="text-ta-muted text-[0.9rem] leading-relaxed">{{ $f['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ============ ALUR SISTEM ============ --}}
<section class="py-20 md:py-28 bg-ta-bg">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-14">
      <span class="eyebrow justify-center mb-3 block">Alur Sistem</span>
      <h2 class="font-display font-bold text-[clamp(1.6rem,1.2rem+1.6vw,2.4rem)] mb-3 text-ta-ink">Bagaimana Sistem Ini Bekerja</h2>
      <p class="text-ta-muted text-[1.01rem]">Proses sederhana dari input data hingga tersaji dalam dashboard yang siap dianalisis.</p>
    </div>

    <div class="flex flex-col lg:flex-row items-center justify-center gap-4 lg:gap-3">

      <div class="flow-step w-full max-w-[200px] bg-white rounded-ta border border-ta-border p-6 text-center">
        <div class="w-9 h-9 rounded-full bg-ta-red text-white font-display font-bold text-sm flex items-center justify-center mx-auto mb-4">1</div>
        <div class="text-ta-dark text-[1.55rem] mb-3"><i class="fa-solid fa-file-import"></i></div>
        <div class="font-display font-bold text-[0.96rem] mb-1 text-ta-ink">Input Data</div>
        <p class="text-ta-muted text-[0.83rem] leading-relaxed">Pegawai, kendaraan & perjalanan dicatat ke sistem.</p>
      </div>

      <div class="text-ta-red text-xl rotate-90 lg:rotate-0 my-1 lg:my-0"><i class="fa-solid fa-arrow-right"></i></div>

      <div class="flow-step w-full max-w-[200px] bg-white rounded-ta border border-ta-border p-6 text-center">
        <div class="w-9 h-9 rounded-full bg-ta-red text-white font-display font-bold text-sm flex items-center justify-center mx-auto mb-4">2</div>
        <div class="text-ta-dark text-[1.55rem] mb-3"><i class="fa-solid fa-gears"></i></div>
        <div class="font-display font-bold text-[0.96rem] mb-1 text-ta-ink">Analisis Otomatis</div>
        <p class="text-ta-muted text-[0.83rem] leading-relaxed">Sistem menghitung konsumsi & rekap BBM otomatis.</p>
      </div>

      <div class="text-ta-red text-xl rotate-90 lg:rotate-0 my-1 lg:my-0"><i class="fa-solid fa-arrow-right"></i></div>

      <div class="flow-step w-full max-w-[200px] bg-white rounded-ta border border-ta-border p-6 text-center">
        <div class="w-9 h-9 rounded-full bg-ta-red text-white font-display font-bold text-sm flex items-center justify-center mx-auto mb-4">3</div>
        <div class="text-ta-dark text-[1.55rem] mb-3"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
        <div class="font-display font-bold text-[0.96rem] mb-1 text-ta-ink">Monitoring</div>
        <p class="text-ta-muted text-[0.83rem] leading-relaxed">Penggunaan BBM dipantau secara berkala.</p>
      </div>

      <div class="text-ta-red text-xl rotate-90 lg:rotate-0 my-1 lg:my-0"><i class="fa-solid fa-arrow-right"></i></div>

      <div class="flow-step w-full max-w-[200px] bg-white rounded-ta border border-ta-border p-6 text-center">
        <div class="w-9 h-9 rounded-full bg-ta-red text-white font-display font-bold text-sm flex items-center justify-center mx-auto mb-4">4</div>
        <div class="text-ta-dark text-[1.55rem] mb-3"><i class="fa-solid fa-gauge-high"></i></div>
        <div class="font-display font-bold text-[0.96rem] mb-1 text-ta-ink">Dashboard</div>
        <p class="text-ta-muted text-[0.83rem] leading-relaxed">Hasil tersaji dalam dashboard yang mudah dibaca.</p>
      </div>

    </div>
  </div>
</section>

{{-- ============ INTEGRASI API ============ --}}
<section class="py-20 md:py-28 bg-gradient-to-b from-white to-ta-bg" id="integrasi">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

      {{-- Copy --}}
      <div>
        <span class="eyebrow mb-4 block">Integrasi API</span>
        <h2 class="font-display font-bold text-[clamp(1.6rem,1.2rem+1.6vw,2.4rem)] mb-5 text-ta-ink">Satu Data, Siap untuk Web & Mobile</h2>
        <p class="text-ta-muted text-[1.01rem] mb-7 leading-relaxed">
          Website dan aplikasi mobile dapat menggunakan data yang sama melalui API Laravel, sehingga sistem ini siap dikembangkan menjadi aplikasi Android di masa depan tanpa perlu membangun ulang dari awal.
        </p>
        <ul class="space-y-4">
          <li class="flex items-start gap-3">
            <i class="fa-solid fa-circle-check text-ta-red mt-0.5 text-base shrink-0"></i>
            <span class="text-ta-ink text-[0.94rem]">Data pegawai, kendaraan, dan perjalanan tersimpan terpusat di satu basis data.</span>
          </li>
          <li class="flex items-start gap-3">
            <i class="fa-solid fa-circle-check text-ta-red mt-0.5 text-base shrink-0"></i>
            <span class="text-ta-ink text-[0.94rem]">API Laravel menghubungkan website dengan aplikasi mobile secara real-time.</span>
          </li>
          <li class="flex items-start gap-3">
            <i class="fa-solid fa-circle-check text-ta-red mt-0.5 text-base shrink-0"></i>
            <span class="text-ta-ink text-[0.94rem]">Arsitektur siap dikembangkan menjadi aplikasi Android tanpa mengubah sistem inti.</span>
          </li>
        </ul>
      </div>

      {{-- API Diagram --}}
      <div class="relative bg-white border border-ta-border rounded-[24px] p-8 shadow-ta-lg overflow-hidden">
        {{-- dot grid bg --}}
        <div class="absolute inset-0 rounded-[24px] opacity-40" style="background-image:radial-gradient(#D1D5DB 1px,transparent 1px);background-size:16px 16px;"></div>
        <div class="relative flex items-center justify-between gap-2 flex-wrap sm:flex-nowrap">
          <div class="api-box">
            <div class="icon-wrap"><i class="fa-solid fa-globe"></i></div>
            <span>Website</span>
          </div>
          <div class="api-line hidden sm:block"></div>
          <div class="api-box center-box relative">
            <div class="absolute -top-3 -right-3 bg-ta-red text-white text-[0.6rem] font-bold px-2 py-0.5 rounded">CORE</div>
            <div class="icon-wrap"><i class="fa-brands fa-laravel"></i></div>
            <span>Laravel API</span>
          </div>
          <div class="api-line hidden sm:block"></div>
          <div class="api-box">
            <div class="icon-wrap"><i class="fa-brands fa-android"></i></div>
            <span>Mobile App</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- ============ TENTANG PROYEK ============ --}}
<section class="py-20 md:py-28" id="tentang">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

      {{-- Copy --}}
      <div class="order-2 lg:order-1">
        <span class="eyebrow mb-4 block">Tentang Proyek</span>
        <h2 class="font-display font-bold text-[clamp(1.6rem,1.2rem+1.6vw,2.4rem)] mb-5 text-ta-ink">Hasil Karya Mahasiswa Magang</h2>
        <p class="text-ta-muted text-[1.01rem] leading-relaxed">
          Sistem ini dikembangkan sebagai proyek magang mahasiswa Informatika Universitas Negeri Padang di PT. Telkom Akses Binjai, untuk membantu proses monitoring penggunaan BBM kendaraan operasional agar lebih tertib, transparan, dan mudah dievaluasi oleh manajemen.
        </p>
      </div>

      {{-- Visual card --}}
      <div class="order-1 lg:order-2">
        <div class="bg-gradient-to-br from-ta-red to-ta-dark rounded-ta p-8 text-white relative overflow-hidden shadow-ta-xl">
          <div class="absolute w-[220px] h-[220px] rounded-full bg-white/[0.07] -bottom-16 -right-10 pointer-events-none"></div>
          <h5 class="font-display font-bold text-lg mb-5 relative z-10 flex items-center gap-2">
            <i class="fa-solid fa-graduation-cap"></i> Proyek Magang 2026
          </h5>
          <div class="relative z-10">
            <div class="about-badge"><i class="fa-solid fa-building"></i> PT. Telkom Akses Binjai</div>
            <div class="about-badge"><i class="fa-solid fa-school"></i> Universitas Negeri Padang</div>
            <div class="about-badge"><i class="fa-solid fa-laptop-code"></i> Teknik Informatika</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
{{-- ============ FOOTER ============ --}}
<footer style="background: linear-gradient(170deg, #9d0000 0%, #6b0000 40%, #3a0000 100%)" class="text-white mt-24">

    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-14">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- ── Branding ── --}}
            <div>
                {{-- Logo dalam card putih --}}
                <div class="inline-flex items-center justify-center bg-white rounded-2xl px-6 py-3 mb-5" style="box-shadow: 0 2px 12px rgba(0,0,0,0.25);">
                    <img
                        src="{{ asset('asset/foto/Image2.png') }}"
                        alt="Logo Telkom Akses"
                        class="h-16 w-auto object-contain">
                </div>

                <div class="mb-4">
                    <h3 class="font-semibold text-xl leading-tight">Monitoring BBM</h3>
                    <p class="text-white/50 text-xs mt-1">PT. Telkom Akses Binjai</p>
                </div>

                <p class="text-white/65 text-sm leading-relaxed mb-5">
                    Sistem monitoring BBM kendaraan operasional — pengawasan konsumsi bahan bakar,
                    efisiensi armada, dan rekap perjalanan dinas secara real-time.
                </p>

                <div class="flex gap-2">
                    <a href="#" aria-label="Instagram"
                       class="w-9 h-9 rounded-full flex items-center justify-center text-sm
                              bg-white/10 border border-white/15 hover:bg-white/20 transition-colors">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#" aria-label="Email"
                       class="w-9 h-9 rounded-full flex items-center justify-center text-sm
                              bg-white/10 border border-white/15 hover:bg-white/20 transition-colors">
                        <i class="fa-solid fa-envelope"></i>
                    </a>
                    <a href="#" aria-label="Telepon"
                       class="w-9 h-9 rounded-full flex items-center justify-center text-sm
                              bg-white/10 border border-white/15 hover:bg-white/20 transition-colors">
                        <i class="fa-solid fa-phone"></i>
                    </a>
                </div>
            </div>

            {{-- ── Navigasi ── --}}
            <div>
                <p class="text-[10px] font-medium tracking-widest uppercase text-white/40 mb-4">Navigasi</p>

                <ul class="space-y-2.5">
                    <li>
                        <a href="/" class="flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors">
                            <i class="fa-solid fa-house text-[13px] opacity-60"></i> Beranda
                        </a>
                    </li>
                    <li>
                        <a href="/pegawai" class="flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors">
                            <i class="fa-solid fa-users text-[13px] opacity-60"></i> Data Pegawai
                        </a>
                    </li>
                    <li>
                        <a href="/kendaraan" class="flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors">
                            <i class="fa-solid fa-car text-[13px] opacity-60"></i> Data Kendaraan
                        </a>
                    </li>
                    <li>
                        <a href="/perjalanan" class="flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors">
                            <i class="fa-solid fa-route text-[13px] opacity-60"></i> Data Perjalanan
                        </a>
                    </li>
                </ul>
            </div>

            {{-- ── Perusahaan ── --}}
            <div>
                <p class="text-[10px] font-medium tracking-widest uppercase text-white/40 mb-4">Perusahaan</p>

                <ul class="space-y-3">
                    <li class="flex items-start gap-2.5 text-sm text-white/70">
                        <i class="fa-solid fa-building mt-0.5 opacity-60 flex-shrink-0"></i>
                        <span>PT. Telkom Akses Binjai</span>
                    </li>
                    <li class="flex items-start gap-2.5 text-sm text-white/70">
                        <i class="fa-solid fa-briefcase mt-0.5 opacity-60 flex-shrink-0"></i>
                        <span>Sistem Internal Operasional</span>
                    </li>
                    <li class="flex items-start gap-2.5 text-sm text-white/70">
                        <i class="fa-solid fa-location-dot mt-0.5 opacity-60 flex-shrink-0"></i>
                        <span>Regional Sumatera Utara</span>
                    </li>
                    <li class="flex items-start gap-2.5 text-sm text-white/70">
                        <i class="fa-solid fa-network-wired mt-0.5 opacity-60 flex-shrink-0"></i>
                        <span>Anak Usaha PT. Telkom Indonesia</span>
                    </li>
                </ul>
            </div>

            {{-- ── Peta ── --}}
            <div>
                <p class="text-[10px] font-medium tracking-widest uppercase text-white/40 mb-4">Lokasi Kantor</p>

                <iframe
                    src="https://maps.google.com/maps?q=Binjai%20Sumatera%20Utara&t=&z=13&ie=UTF8&iwloc=&output=embed"
                    class="w-full rounded-xl border border-white/10"
                    style="height: 160px;"
                    loading="lazy"
                    title="Peta lokasi kantor">
                </iframe>

                <p class="mt-2.5 text-xs text-white/50 flex items-center gap-1.5">
                    <i class="fa-solid fa-map-pin text-[11px]"></i>
                    Binjai, Sumatera Utara
                </p>
            </div>

        </div>

        {{-- ── Bottom bar ── --}}
        <div class="border-t border-white/10 mt-10 pt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-2">

            <p class="text-xs text-white/45">
                © {{ date('Y') }} PT. Telkom Akses Binjai. Seluruh hak cipta dilindungi.
            </p>

            <p class="text-xs text-white/45 md:text-right">
                Dikembangkan oleh
                <span class="text-white/85 font-medium">Ramzy Junfaris Hamonangan</span>
                — Mahasiswa Magang Universitas Negeri Padang
            </p>

        </div>

    </div>

</footer>

<script>
// ─── Navbar shadow on scroll ───────────────────────────────
const navbar = document.getElementById('mainNavbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('navbar-scrolled', window.scrollY > 10);
});

// ─── Mobile menu toggle ────────────────────────────────────
const btn      = document.getElementById('mobileMenuBtn');
const menu     = document.getElementById('mobileMenu');
const menuIcon = document.getElementById('menuIcon');

btn.addEventListener('click', () => {
  const isOpen = menu.classList.toggle('open');
  menuIcon.className = isOpen ? 'fa-solid fa-xmark text-xl' : 'fa-solid fa-bars text-xl';
  btn.setAttribute('aria-expanded', isOpen);
});

// Close on mobile nav link click
menu.querySelectorAll('a').forEach(link => {
  link.addEventListener('click', () => {
    menu.classList.remove('open');
    menuIcon.className = 'fa-solid fa-bars text-xl';
    btn.setAttribute('aria-expanded', 'false');
  });
});

// ─── Counter animation (ease-out cubic) ───────────────────
const counters = document.querySelectorAll('.counter');
const DURATION = 1400;

function animateCounter(el) {
  const target = +el.getAttribute('data-target');
  const startTime = performance.now();
  function tick(now) {
    const progress = Math.min((now - startTime) / DURATION, 1);
    const eased    = 1 - Math.pow(1 - progress, 3);
    el.textContent = Math.floor(eased * target).toLocaleString('id-ID');
    if (progress < 1) requestAnimationFrame(tick);
    else el.textContent = target.toLocaleString('id-ID');
  }
  requestAnimationFrame(tick);
}

const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      animateCounter(entry.target);
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.4 });

counters.forEach(c => observer.observe(c));
</script>
</body>
</html>