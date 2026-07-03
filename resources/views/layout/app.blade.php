<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Sistem Monitoring BBM') | PT. Telkom Akses Binjai</title>
<meta name="description" content="@yield('meta_description', 'Sistem Monitoring BBM Kendaraan Operasional PT. Telkom Akses Binjai.')">

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
          red:    '#E2001A',
          dark:   '#B10014',
          darker: '#7A000D',
          soft:   '#FEE8EB',
          ink:    '#1A1A2E',
          muted:  '#6B7280',
          bg:     '#F8F8FA',
          border: '#E5E7EB',
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
  /* ── Global Reset & Base ── */
  *, *::before, *::after { box-sizing: border-box; }
  body {
    font-family: 'Inter', sans-serif;
    color: #1A1A2E;
    background: #F8F8FA;
    overflow-x: hidden;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }
  main { flex: 1; }
  h1,h2,h3,h4,h5,.font-display { font-family: 'Poppins', sans-serif; }

  /* ── Eyebrow label ── */
  .eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: #E2001A;
  }
  .eyebrow::before {
    content: '';
    width: 22px;
    height: 3px;
    background: #E2001A;
    border-radius: 4px;
    display: inline-block;
  }

  /* ── Navbar ── */
  #mainNavbar {
    position: sticky;
    top: 0;
    z-index: 50;
    background: rgba(255,255,255,0.97);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid #E5E7EB;
    transition: box-shadow 0.3s ease;
  }
  .navbar-scrolled { box-shadow: 0 8px 32px rgba(26,26,46,0.10); }

  /* ── Nav links ── */
  .nav-link-item {
    font-weight: 600;
    font-size: 0.93rem;
    padding: 8px 14px;
    border-radius: 8px;
    transition: color 0.18s, background 0.18s;
    color: #1A1A2E;
    text-decoration: none;
    display: inline-block;
  }
  .nav-link-item:hover,
  .nav-link-item.active {
    color: #E2001A;
    background: #FEE8EB;
  }

  /* ── Mobile menu ── */
  #mobileMenu { display: none; }
  #mobileMenu.open { display: flex; }

  /* ── Feature card ── */
  .feature-card {
    position: relative;
    overflow: hidden;
    transition: transform .25s, box-shadow .25s, border-color .25s;
  }
  .feature-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #E2001A, #B10014);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform .3s ease;
  }
  .feature-card:hover::before { transform: scaleX(1); }
  .feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 48px rgba(177,0,20,0.16);
    border-color: transparent !important;
  }
  .feature-icon { transition: background .25s, color .25s; }
  .feature-card:hover .feature-icon {
    background: linear-gradient(135deg, #E2001A, #B10014) !important;
    color: #fff !important;
  }

  /* ── Stat card ── */
  .stat-card { transition: transform .25s, box-shadow .25s; }
  .stat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 48px rgba(177,0,20,0.18);
  }

  /* ── Flow step ── */
  .flow-step { transition: transform .25s, box-shadow .25s; }
  .flow-step:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(26,26,46,0.08);
  }

  /* ── Footer link ── */
  .footer-link {
    color: rgba(255,255,255,0.72);
    transition: color .18s, padding-left .18s;
    display: inline-block;
    text-decoration: none;
  }
  .footer-link:hover { color: #fff; padding-left: 4px; }

  /* ── About badge ── */
  .about-badge {
    background: rgba(255,255,255,0.13);
    border: 1px solid rgba(255,255,255,0.24);
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    font-size: 0.91rem;
    color: #fff;
    margin-bottom: 10px;
  }

  /* ── API diagram ── */
  .api-line {
    flex: 1;
    height: 2px;
    background: repeating-linear-gradient(90deg, #E2001A 0 8px, transparent 8px 14px);
    min-width: 24px;
  }
  .api-box {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 16px;
    padding: 20px 16px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(26,26,46,0.07);
    width: 148px;
    flex-shrink: 0;
  }
  .api-box .icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #FEE8EB;
    color: #E2001A;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    margin: 0 auto 10px;
  }
  .api-box.center-box { border-color: #E2001A; }
  .api-box.center-box .icon-wrap {
    background: linear-gradient(135deg, #E2001A, #B10014);
    color: #fff;
  }
  .api-box span { font-size: 0.83rem; font-weight: 700; color: #1A1A2E; }

  /* ── Focus & motion ── */
  a:focus-visible, button:focus-visible {
    outline: 3px solid #B10014;
    outline-offset: 2px;
  }
  @media (prefers-reduced-motion: reduce) {
    * { animation: none !important; transition: none !important; scroll-behavior: auto !important; }
  }

  /* ── Page content padding (so content isn't hugged against edges on mobile) ── */
  .page-content {
    max-width: 1280px;
    margin: 0 auto;
    padding: 2rem 1rem;
  }
  @media (min-width: 640px) { .page-content { padding: 2.5rem 1.5rem; } }
  @media (min-width: 1024px) { .page-content { padding: 3rem 2rem; } }
</style>

@stack('styles')
</head>
<body class="antialiased">

{{-- ============================================================ --}}
{{-- NAVBAR                                                        --}}
{{-- ============================================================ --}}
<nav id="mainNavbar">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between py-3">

    {{-- Logo --}}
    <a href="{{ url('/') }}" class="flex items-center gap-3 no-underline group">
      {{-- Ganti div ini dengan <img src="{{ asset('images/logo-telkom-akses.png') }}" …> jika ada logo --}}
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
      <a href="{{ url('/') }}"
         class="nav-link-item {{ request()->is('/') ? 'active' : '' }}">
        Beranda
      </a>
      <a href="{{ route('pegawai.index') }}"
         class="nav-link-item {{ request()->routeIs('pegawai.*') ? 'active' : '' }}">
        Data Pegawai
      </a>
      <a href="{{ route('kendaraan.index') }}"
         class="nav-link-item {{ request()->routeIs('kendaraan.*') ? 'active' : '' }}">
        Data Kendaraan
      </a>
      <a href="{{ route('perjalanan.index') }}"
         class="nav-link-item {{ request()->routeIs('perjalanan.*') ? 'active' : '' }}">
        Data Perjalanan
      </a>
      <a href="{{ route('ai.dashboard') }}"
         class="nav-link-item {{ request()->routeIs('ai.dashboard') ? 'active' : '' }}">
        AI Analyst
      </a>
      <a href="{{ route('perjalanan.index') }}"
         class="ml-3 inline-flex items-center gap-2 bg-gradient-to-br from-ta-red to-ta-dark text-white font-semibold text-[0.93rem] px-5 py-2.5 rounded-full shadow-[0_6px_18px_rgba(226,0,26,0.32)] hover:brightness-105 hover:-translate-y-[1px] transition-all">
        <i class="fa-solid fa-gauge-high text-sm"></i> Dashboard
      </a>
    </div>

    {{-- Mobile hamburger button --}}
    <button id="mobileMenuBtn"
            class="lg:hidden p-2 rounded-lg text-ta-ink hover:text-ta-red hover:bg-ta-soft focus:outline-none transition-colors"
            aria-label="Buka menu"
            aria-expanded="false"
            aria-controls="mobileMenu">
      <i id="menuIcon" class="fa-solid fa-bars text-xl"></i>
    </button>

  </div>

  {{-- Mobile menu (dropdown) --}}
  <div id="mobileMenu"
       class="lg:hidden flex-col gap-1 px-4 pb-4 pt-3 border-t border-ta-border bg-white"
       role="navigation"
       aria-label="Menu mobile">

    <a href="{{ url('/') }}"
       class="nav-link-item block {{ request()->is('/') ? 'active' : '' }}">
      <i class="fa-solid fa-house mr-1.5 text-sm"></i> Beranda
    </a>
    <a href="{{ route('pegawai.index') }}"
       class="nav-link-item block {{ request()->routeIs('pegawai.*') ? 'active' : '' }}">
      <i class="fa-solid fa-users mr-1.5 text-sm"></i> Data Pegawai
    </a>
    <a href="{{ route('kendaraan.index') }}"
       class="nav-link-item block {{ request()->routeIs('kendaraan.*') ? 'active' : '' }}">
      <i class="fa-solid fa-truck mr-1.5 text-sm"></i> Data Kendaraan
    </a>
    <a href="{{ route('perjalanan.index') }}"
       class="nav-link-item block {{ request()->routeIs('perjalanan.*') ? 'active' : '' }}">
      <i class="fa-solid fa-route mr-1.5 text-sm"></i> Data Perjalanan
    </a>
    <a href="{{ route('ai.dashboard') }}"
       class="nav-link-item block {{ request()->routeIs('ai.dashboard') ? 'active' : '' }}">
      <i class="fa-solid fa-robot mr-1.5 text-sm"></i> AI Analyst
    </a>

    <a href="{{ route('perjalanan.index') }}"
       class="mt-2 flex items-center justify-center gap-2 bg-gradient-to-br from-ta-red to-ta-dark text-white font-semibold text-sm px-5 py-2.5 rounded-full shadow-[0_6px_18px_rgba(226,0,26,0.28)]">
      <i class="fa-solid fa-gauge-high"></i> Dashboard Monitoring
    </a>

  </div>
</nav>

{{-- ============================================================ --}}
{{-- MAIN CONTENT                                                   --}}
{{-- ============================================================ --}}
<main>
  @yield('content')
</main>

{{-- ============================================================ --}}
{{-- FOOTER                                                        --}}
{{-- ============================================================ --}}
<footer style="background: linear-gradient(170deg, #9d0000 0%, #6b0000 40%, #3a0000 100%);" class="text-white mt-auto">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

      {{-- ── Branding ── --}}
      <div>
        <div class="inline-flex items-center justify-center bg-white rounded-2xl px-5 py-3 mb-5 shadow-[0_2px_12px_rgba(0,0,0,0.25)]">
          {{-- Ganti dengan <img src="{{ asset('asset/foto/Image2.png') }}" alt="Logo Telkom Akses" class="h-14 w-auto object-contain"> --}}
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-ta-red to-ta-dark flex items-center justify-center text-white font-display font-extrabold text-sm">
            TA
          </div>
        </div>

        <div class="mb-4">
          <h3 class="font-display font-semibold text-xl leading-tight">Monitoring BBM</h3>
          <p class="text-white/50 text-xs mt-1">PT. Telkom Akses Binjai</p>
        </div>

        <p class="text-white/65 text-sm leading-relaxed mb-5">
          Sistem monitoring BBM kendaraan operasional — pengawasan konsumsi bahan bakar,
          efisiensi armada, dan rekap perjalanan dinas secara real-time.
        </p>

        <div class="flex gap-2">
          <a href="#" aria-label="Instagram"
             class="w-9 h-9 rounded-full flex items-center justify-center text-sm bg-white/10 border border-white/15 hover:bg-white/20 transition-colors">
            <i class="fa-brands fa-instagram"></i>
          </a>
          <a href="#" aria-label="Email"
             class="w-9 h-9 rounded-full flex items-center justify-center text-sm bg-white/10 border border-white/15 hover:bg-white/20 transition-colors">
            <i class="fa-solid fa-envelope"></i>
          </a>
          <a href="#" aria-label="Telepon"
             class="w-9 h-9 rounded-full flex items-center justify-center text-sm bg-white/10 border border-white/15 hover:bg-white/20 transition-colors">
            <i class="fa-solid fa-phone"></i>
          </a>
        </div>
      </div>

      {{-- ── Navigasi ── --}}
      <div>
        <p class="text-[10px] font-medium tracking-widest uppercase text-white/40 mb-4">Navigasi</p>
        <ul class="space-y-2.5">
          <li>
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors">
              <i class="fa-solid fa-house text-[13px] opacity-60"></i> Beranda
            </a>
          </li>
          <li>
            <a href="{{ route('pegawai.index') }}" class="flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors">
              <i class="fa-solid fa-users text-[13px] opacity-60"></i> Data Pegawai
            </a>
          </li>
          <li>
            <a href="{{ route('kendaraan.index') }}" class="flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors">
              <i class="fa-solid fa-car text-[13px] opacity-60"></i> Data Kendaraan
            </a>
          </li>
          <li>
            <a href="{{ route('perjalanan.index') }}" class="flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors">
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
          style="height:160px;"
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
    <div class="border-t border-white/10 mt-10 pt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
      <p class="text-xs text-white/45">
        © {{ date('Y') }} PT. Telkom Akses Binjai. Seluruh hak cipta dilindungi.
      </p>
      <p class="text-xs text-white/45 sm:text-right">
        Dikembangkan oleh
        <span class="text-white/85 font-medium">Ramzy Junfaris Hamonangan</span>
        — Mahasiswa Magang Universitas Negeri Padang
      </p>
    </div>

  </div>
</footer>

{{-- ============================================================ --}}
{{-- SCRIPTS                                                       --}}
{{-- ============================================================ --}}
<script>
// ── Navbar shadow on scroll ──────────────────────────────────────
const navbar = document.getElementById('mainNavbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('navbar-scrolled', window.scrollY > 10);
}, { passive: true });

// ── Mobile menu toggle ───────────────────────────────────────────
const mobileBtn  = document.getElementById('mobileMenuBtn');
const mobileMenu = document.getElementById('mobileMenu');
const menuIcon   = document.getElementById('menuIcon');

mobileBtn.addEventListener('click', () => {
  const isOpen = mobileMenu.classList.toggle('open');
  menuIcon.className   = isOpen ? 'fa-solid fa-xmark text-xl' : 'fa-solid fa-bars text-xl';
  mobileBtn.setAttribute('aria-expanded', String(isOpen));
});

// Close mobile menu on link click
mobileMenu.querySelectorAll('a').forEach(link => {
  link.addEventListener('click', () => {
    mobileMenu.classList.remove('open');
    menuIcon.className = 'fa-solid fa-bars text-xl';
    mobileBtn.setAttribute('aria-expanded', 'false');
  });
});

// Close mobile menu on outside click
document.addEventListener('click', (e) => {
  if (!navbar.contains(e.target) && mobileMenu.classList.contains('open')) {
    mobileMenu.classList.remove('open');
    menuIcon.className = 'fa-solid fa-bars text-xl';
    mobileBtn.setAttribute('aria-expanded', 'false');
  }
});

// ── Counter animation (ease-out cubic) — used in home page ──────
function animateCounter(el) {
  const target   = +el.getAttribute('data-target');
  const DURATION = 1400;
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

const counterObserver = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      animateCounter(entry.target);
      counterObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.4 });

document.querySelectorAll('.counter').forEach(c => counterObserver.observe(c));
</script>

@stack('scripts')
</body>
</html>
