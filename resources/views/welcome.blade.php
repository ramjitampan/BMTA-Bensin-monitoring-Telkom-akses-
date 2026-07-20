@extends('layout.app')

@section('title', 'Sistem Monitoring BBM Kendaraan Operasional')
@section('meta_description', 'Sistem Monitoring BBM Kendaraan Operasional PT. Telkom Akses Binjai — kelola, monitor, dan evaluasi penggunaan BBM kendaraan operasional secara efektif dan terukur.')

@push('styles')
@vite('resources/Tema/beranda/beranda.css')
@endpush

@section('content')

{{-- ============ HERO ============ --}}
<header class="hero-bg relative text-white pt-6 pb-32 sm:pt-8 sm:pb-36 overflow-hidden" id="beranda">
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

@endsection

@push('scripts')
<script>
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
@endpush
