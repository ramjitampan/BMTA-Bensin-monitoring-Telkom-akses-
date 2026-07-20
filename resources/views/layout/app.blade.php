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
          ink:    '#000000',
          muted:  '#47494c',
          bg:     '#F8F8FA',
          border: '#E5E7EB',
        }
      },
      boxShadow: {
        'ta':    '0 10px 30px rgba(255, 255, 255, 0.07)',
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

@vite('resources/Tema/layout.css')
@stack('styles')
</head>
<body class="antialiased bg-[#F8F8FA]">

{{-- ============================================================ --}}
{{-- NAVBAR                                                        --}}
{{-- ============================================================ --}}
<nav id="mainNavbar">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between py-3">

    {{-- Logo --}}
    <a href="{{ url('/') }}" class="flex items-center gap-5 no-underline group">
      <img src="{{ asset('asset/foto/Image2.png') }}" alt="PT. Telkom Akses Binjai"
           class="h-12 w-auto object-contain shrink-0 transition-transform duration-200 group-hover:scale-105">
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

      @auth
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
      <a href="{{ route('perjalanan.index') }}"
         class="ml-3 inline-flex items-center gap-2 bg-gradient-to-br from-ta-red to-ta-dark text-white font-semibold text-[0.93rem] px-5 py-2.5 rounded-full shadow-[0_6px_18px_rgba(226,0,26,0.32)] hover:brightness-105 hover:-translate-y-[1px] transition-all">
        <i class="fa-solid fa-gauge-high text-sm"></i> Dashboard
      </a>

      <form method="POST" action="{{ route('logout') }}" class="ml-2">
        @csrf
        <button type="submit"
                class="text-sm text-ta-muted hover:text-ta-red px-3 py-2 transition-colors">
          <i class="fa-solid fa-right-from-bracket mr-1"></i> Keluar
        </button>
      </form>
      @else
      <a href="{{ route('login') }}"
         class="ml-3 inline-flex items-center gap-2 bg-gradient-to-br from-ta-red to-ta-dark text-white font-semibold text-[0.93rem] px-5 py-2.5 rounded-full hover:brightness-105 transition-all">
        <i class="fa-solid fa-right-to-bracket text-sm"></i> Masuk
      </a>
      @endauth
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

    @auth
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
    <a href="{{ route('perjalanan.index') }}"
       class="mt-2 flex items-center justify-center gap-2 bg-gradient-to-br from-ta-red to-ta-dark text-white font-semibold text-sm px-5 py-2.5 rounded-full shadow-[0_6px_18px_rgba(226,0,26,0.28)]">
      <i class="fa-solid fa-gauge-high"></i> Dashboard Monitoring
    </a>
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
      @csrf
      <button type="submit"
              class="w-full flex items-center justify-center gap-2 text-sm text-ta-muted hover:text-ta-red py-2.5 transition-colors">
        <i class="fa-solid fa-right-from-bracket"></i> Keluar
      </button>
    </form>
    @else
    <a href="{{ route('login') }}"
       class="mt-2 flex items-center justify-center gap-2 bg-gradient-to-br from-ta-red to-ta-dark text-white font-semibold text-sm px-5 py-2.5 rounded-full">
      <i class="fa-solid fa-right-to-bracket"></i> Masuk
    </a>
    @endauth

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
        <img src="{{ asset('asset/foto/image.png') }}" alt="PT. Telkom Akses Binjai"
     class="h-14 w-auto object-contain mb-9">
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

    @vite('resources/js/app.js')

    @stack('scripts')
</body>
</html>
