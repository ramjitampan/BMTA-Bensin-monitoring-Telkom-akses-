<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai | Monitoring BBM - PT. Telkom Akses Binjai</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* =========================================================
           DESIGN TOKENS — Telkom Akses Corporate Dashboard
        ========================================================= */
        :root {
            --red:          #CC1B12;
            --red-dark:     #9B1008;
            --red-deeper:   #6B0A05;
            --red-soft:     #FEF0EF;
            --red-mid:      #FADDDB;
            --ink:          #1C1C21;
            --ink-mid:      #52525E;
            --ink-muted:    #8B8D97;
            --ink-faint:    #B0B1BB;
            --surface:      #FFFFFF;
            --bg:           #F2F3F7;
            --bg-subtle:    #F7F7FA;
            --border:       #E3E4EA;
            --border-soft:  #EEEEF4;
            --blue:         #2563EB;
            --blue-soft:    #EFF4FF;
            --blue-border:  #C7D7FB;
            --radius-card:  14px;
            --radius-sm:    8px;
            --radius-pill:  20px;
            --navbar-h:     60px;
            --shadow-sm:    0 2px 8px rgba(28,28,33,0.05);
            --shadow-md:    0 2px 12px rgba(28,28,33,0.07);
        }

        /* =========================================================
           BASE
        ========================================================= */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--ink);
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, .font-display {
            font-family: 'Poppins', sans-serif;
        }

        /* =========================================================
           NAVBAR
           - Putih bersih, sticky, satu garis border bawah tipis
           - Tinggi tetap 60px
        ========================================================= */
        .navbar-ta {
            background: var(--surface);
            height: var(--navbar-h);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1040;
            box-shadow: 0 1px 4px rgba(28,28,33,0.06);
        }

        .ta-logo-badge {
            width: 36px; height: 36px;
            border-radius: var(--radius-sm);
            background: var(--red);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Poppins', sans-serif;
            font-weight: 800; font-size: 13px;
            flex-shrink: 0;
        }

        .navbar-brand-text .brand-sub {
            display: block; font-size: 10px; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase;
            color: var(--ink-muted); line-height: 1.3;
        }
        .navbar-brand-text .brand-main {
            display: block; font-family: 'Poppins', sans-serif;
            font-weight: 700; font-size: 13.5px; color: var(--ink); line-height: 1.3;
        }

        .navbar-ta .nav-link {
            font-weight: 600; font-size: 13px;
            color: var(--ink-mid) !important;
            padding: 6px 12px !important;
            border-radius: var(--radius-sm);
            transition: color .15s, background .15s;
        }
        .navbar-ta .nav-link:hover {
            color: var(--ink) !important;
            background: var(--bg-subtle);
        }
        .navbar-ta .nav-link.active {
            color: var(--red) !important;
            background: var(--red-soft);
        }
        .navbar-ta .nav-cta {
            background: var(--red) !important;
            color: #fff !important;
            border-radius: var(--radius-pill) !important;
            padding: 6px 14px !important;
            font-weight: 700 !important;
            font-size: 12.5px !important;
        }
        .navbar-ta .nav-cta:hover {
            background: var(--red-dark) !important;
            color: #fff !important;
        }
        .navbar-toggler {
            border-color: var(--border) !important;
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%2352525E' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }

        /* =========================================================
           HERO — max 170px, merah solid, tanpa wave
        ========================================================= */
        .page-hero {
            background: var(--red);
            padding: 18px 0 22px;
            position: relative;
            overflow: hidden;
        }
        .page-hero::before {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            border: 36px solid rgba(255,255,255,.07);
            top: -80px; right: -50px;
            pointer-events: none;
        }
        .page-hero .container { position: relative; z-index: 2; }

        .breadcrumb-ta {
            font-size: 11px;
            color: rgba(255,255,255,.55);
            margin-bottom: 8px;
            display: flex; align-items: center; gap: 5px;
        }
        .breadcrumb-ta a {
            color: rgba(255,255,255,.72);
            text-decoration: none;
            transition: color .15s;
        }
        .breadcrumb-ta a:hover { color: #fff; }
        .breadcrumb-ta i { font-size: 8px; }

        .page-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: clamp(1.3rem, 1.1rem + .6vw, 1.7rem);
            color: #fff;
            line-height: 1.15;
            margin-bottom: 4px;
        }
        .page-subtitle {
            font-size: 12.5px;
            color: rgba(255,255,255,.72);
            margin-bottom: 0;
        }

        .btn-ta-hero {
            background: #fff;
            color: var(--red-dark);
            font-weight: 700;
            font-size: 13px;
            border: none;
            border-radius: var(--radius-sm);
            padding: 9px 18px;
            height: 40px;
            display: inline-flex; align-items: center; gap: 7px;
            white-space: nowrap;
            box-shadow: 0 4px 14px rgba(0,0,0,.15);
            transition: transform .15s, box-shadow .15s;
            text-decoration: none;
        }
        .btn-ta-hero:hover {
            color: var(--red-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0,0,0,.2);
        }
        .btn-ta-hero i { font-size: 11px; }

        /* =========================================================
           TOOLBAR ROW — stat card kiri, search kanan
        ========================================================= */
        .toolbar-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 16px 0 12px;
        }

        .stat-card-compact {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-left: 3px solid var(--red);
            border-radius: 10px;
            padding: 10px 16px;
            min-height: 68px;
            flex: 0 1 auto;
            box-shadow: var(--shadow-sm);
        }
        .stat-card-compact .icon-wrap {
            width: 38px; height: 38px;
            border-radius: 9px;
            background: var(--red-soft);
            color: var(--red);
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0;
        }
        .stat-card-compact .num {
            font-family: 'Poppins', sans-serif;
            font-weight: 800; font-size: 20px;
            color: var(--ink); line-height: 1.1;
        }
        .stat-card-compact .lbl {
            font-size: 11px; color: var(--ink-muted);
            font-weight: 600; text-transform: uppercase;
            letter-spacing: .04em; margin-top: 2px;
        }

        .search-box-compact {
            flex: 0 1 360px;
            max-width: 360px;
            width: 100%;
        }
        .search-inner {
            display: flex; align-items: center;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 9px; height: 40px; overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: border-color .15s;
        }
        .search-inner:focus-within { border-color: var(--red); }
        .search-inner .si-icon {
            padding: 0 10px;
            color: var(--ink-muted);
            font-size: 12.5px;
            flex-shrink: 0;
        }
        .search-inner input {
            flex: 1; border: none; background: none; outline: none;
            font-family: 'Inter', sans-serif;
            font-size: 13px; color: var(--ink);
            padding-right: 12px;
        }
        .search-inner input::placeholder { color: var(--ink-faint); }

        /* =========================================================
           FLASH
        ========================================================= */
        .alert-ta-success {
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            border-left: 3px solid #059669;
            color: #065F46;
            border-radius: 10px;
            font-size: 13px;
            padding: 10px 14px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* =========================================================
           TABLE CARD
           - Border radius 16px, shadow, header sticky
           - Zebra stripe ringan, hover merah lembut
        ========================================================= */
        .table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-card);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            margin-bottom: 80px;
        }

        .table-ta { margin-bottom: 0; }

        .table-ta thead tr {
            background: var(--bg-subtle);
            border-bottom: 1.5px solid var(--border);
        }
        .table-ta thead th {
            position: sticky;
            top: var(--navbar-h);
            z-index: 5;
            background: var(--bg-subtle);
            color: var(--ink-muted);
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            border-bottom: 1.5px solid var(--border);
            padding: 11px 14px;
            white-space: nowrap;
        }
        .table-ta thead th.col-no { color: var(--ink-faint); }

        .table-ta tbody td {
            padding: 10px 14px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-soft);
            font-size: 13px;
        }
        .table-ta tbody tr:last-child td { border-bottom: none; }
        .table-ta tbody tr:nth-child(even) td { background: #FAFAFA; }
        .table-ta tbody tr { transition: background-color .12s; }
        .table-ta tbody tr:hover td { background-color: var(--red-soft) !important; }

        /* Kolom nomor */
        .row-num { color: var(--ink-faint); font-size: 12px; font-weight: 600; }

        /* Avatar + nama */
        .pegawai-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--red); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px; flex-shrink: 0;
            border: 2px solid var(--red-mid);
        }
        .pegawai-name {
            font-weight: 700; font-size: 13px;
            color: var(--ink); display: block; line-height: 1.3;
        }
        .pegawai-meta {
            font-size: 11px; color: var(--ink-faint); display: block;
        }

        /* Badge jabatan & divisi */
        .badge-jabatan {
            background: var(--bg);
            color: var(--ink-mid);
            border: 1px solid var(--border);
            font-weight: 600; font-size: 11px;
            padding: 3px 9px; border-radius: var(--radius-pill);
        }
        .badge-divisi {
            background: var(--red-soft);
            color: var(--red-dark);
            border: 1px solid var(--red-mid);
            font-weight: 600; font-size: 11px;
            padding: 3px 9px; border-radius: var(--radius-pill);
        }

        /* No. HP */
        .phone-link {
            color: var(--ink-muted); text-decoration: none; font-size: 12.5px;
            transition: color .15s;
        }
        .phone-link i { font-size: 10px; margin-right: 4px; }
        .phone-link:hover { color: var(--red); }

        /* Tombol aksi icon */
        .btn-icon-action {
            width: 30px; height: 30px; border-radius: 7px;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid transparent; font-size: 11.5px;
            cursor: pointer; text-decoration: none;
            transition: all .15s ease;
        }
        .btn-icon-action.edit {
            background: var(--blue-soft);
            color: var(--blue);
            border-color: var(--blue-border);
        }
        .btn-icon-action.edit:hover {
            background: var(--blue); color: #fff;
            transform: translateY(-1px);
        }
        .btn-icon-action.delete {
            background: var(--red-soft);
            color: var(--red);
            border-color: var(--red-mid);
        }
        .btn-icon-action.delete:hover {
            background: var(--red); color: #fff;
            transform: translateY(-1px);
        }
        /* Reset default button style untuk form delete */
        button.btn-icon-action {
            -webkit-appearance: none;
            appearance: none;
        }

        /* =========================================================
           EMPTY STATE
        ========================================================= */
        .empty-state { text-align: center; padding: 4rem 2rem; }
        .empty-state .icon-circle {
            width: 68px; height: 68px; border-radius: 50%;
            background: var(--red-soft); color: var(--red);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.7rem; margin: 0 auto 1.1rem;
        }
        .empty-state h5 { font-weight: 700; margin-bottom: .4rem; }
        .empty-state p { color: var(--ink-muted); font-size: 13px; margin-bottom: 1.3rem; }

        /* =========================================================
           FOOTER
        ========================================================= */
        .footer-ta {
            background: var(--ink);
            border-top: 2px solid var(--red);
            color: rgba(255,255,255,.4);
            padding: 14px 0;
            font-size: 12px;
        }
        .footer-ta .fw { color: rgba(255,255,255,.65); font-weight: 600; }
        .footer-ta .dot {
            display: inline-block;
            width: 4px; height: 4px; border-radius: 50%;
            background: var(--red);
            margin: 0 8px; vertical-align: middle;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */
        @media (max-width: 767.98px) {
            .search-box-compact { max-width: 100%; flex-basis: 100%; }
            .toolbar-row { gap: 10px; }
            .table-ta thead th { position: static; }
            .page-title { font-size: 1.3rem; }
            .btn-ta-hero { font-size: 12px; padding: 8px 14px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        a:focus-visible, button:focus-visible {
            outline: 3px solid var(--red-dark);
            outline-offset: 2px;
        }
    </style>
</head>
<body>

{{-- ============================== NAVBAR ============================== --}}
<nav class="navbar navbar-expand-lg navbar-ta">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
            <div class="ta-logo-badge">TA</div>
            <span class="navbar-brand-text">
                <span class="brand-sub">PT. Telkom Akses Binjai</span>
                <span class="brand-main">Monitoring BBM</span>
            </span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navMain"
                aria-controls="navMain" aria-expanded="false"
                aria-label="Buka menu navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="{{ route('pegawai.index') }}">Data Pegawai</a>
                </li>
                <li class="nav-item">
                    {{-- Ganti href dengan route('kendaraan.index') --}}
                    <a class="nav-link" href="#">Data Kendaraan</a>
                </li>
                <li class="nav-item">
                    {{-- Ganti href dengan route('perjalanan.index') --}}
                    <a class="nav-link" href="#">Data Perjalanan</a>
                </li>
                <li class="nav-item ms-lg-2">
                    {{-- Ganti href dengan route('dashboard') --}}
                    <a class="nav-link nav-cta" href="#">
                        <i class="fa-solid fa-gauge-high me-1"></i>Dashboard
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- ============================== HERO ============================== --}}
<header class="page-hero">
    <div class="container">
        <nav class="breadcrumb-ta" aria-label="breadcrumb">
            <a href="{{ url('/') }}">Beranda</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Data Pegawai</span>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="page-title">Data Pegawai</h1>
                <p class="page-subtitle">Kelola data pegawai pengemudi kendaraan operasional.</p>
            </div>
            <a href="{{ route('pegawai.create') }}" class="btn-ta-hero">
                <i class="fa-solid fa-plus"></i>Tambah Pegawai
            </a>
        </div>
    </div>
</header>

{{-- ============================== MAIN CONTENT ============================== --}}
<div class="container">

    {{-- TOOLBAR: stat card + search --}}
    <div class="toolbar-row">
        <div class="stat-card-compact">
            <div class="icon-wrap">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="num">{{ count($pegawais) }}</div>
                <div class="lbl">Total Pegawai</div>
            </div>
        </div>

        <div class="search-box-compact">
            <div class="search-inner">
                <span class="si-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" id="searchInput"
                       placeholder="Cari nama, jabatan, atau divisi...">
            </div>
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @if (session('success'))
        <div class="alert-ta-success" role="alert">
            <i class="fa-solid fa-circle-check"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- TABLE CARD --}}
    <div class="table-card">
        @if (count($pegawais) > 0)
            <div class="table-responsive">
                <table class="table table-ta" id="pegawaiTable">
                    <thead>
                        <tr>
                            <th class="col-no" style="width:44px">#</th>
                            <th>Nama Pegawai</th>
                            <th>Jabatan</th>
                            <th>Divisi</th>
                            <th>No. HP</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pegawais as $i => $pegawai)
                        <tr>
                            <td class="row-num">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="pegawai-avatar">
                                        {{ strtoupper(substr($pegawai->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="pegawai-name">{{ $pegawai->nama }}</span>
                                        <span class="pegawai-meta">ID #{{ $pegawai->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge-jabatan">{{ $pegawai->jabatan }}</span></td>
                            <td><span class="badge-divisi">{{ $pegawai->divisi }}</span></td>
                            <td>
                                <a href="tel:{{ $pegawai->no_hp }}" class="phone-link">
                                    <i class="fa-solid fa-phone"></i>{{ $pegawai->no_hp }}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('pegawai.edit', $pegawai->id) }}"
                                       class="btn-icon-action edit"
                                       title="Edit data {{ $pegawai->nama }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('pegawai.destroy', $pegawai->id) }}"
                                          method="POST"
                                          style="display:inline"
                                          onsubmit="return confirm('Yakin ingin menghapus data {{ addslashes($pegawai->nama) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-icon-action delete"
                                                title="Hapus data {{ $pegawai->nama }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="icon-circle">
                    <i class="fa-solid fa-users-slash"></i>
                </div>
                <h5>Belum Ada Data Pegawai</h5>
                <p>Mulai tambahkan data pegawai untuk memantau<br>penggunaan BBM kendaraan operasional.</p>
                <a href="{{ route('pegawai.create') }}" class="btn-ta-hero">
                    <i class="fa-solid fa-plus"></i>Tambah Pegawai Pertama
                </a>
            </div>
        @endif
    </div>

</div>

{{-- ============================== FOOTER ============================== --}}
<footer class="footer-ta">
    <div class="container d-flex flex-column flex-md-row
                justify-content-between align-items-center
                gap-2 text-center text-md-start">
        <span>
            <span class="fw">PT. Telkom Akses Binjai</span>
            &nbsp;&copy; {{ date('Y') }} Seluruh hak cipta dilindungi.
        </span>
        <span>
            Sistem Monitoring BBM
            <span class="dot"></span>
            Universitas Negeri Padang, 2026
        </span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    /* --- Live search di tabel (nama, jabatan, divisi) --- */
    const searchInput = document.getElementById('searchInput');
    const tableRows   = document.querySelectorAll('#pegawaiTable tbody tr');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const kw = this.value.toLowerCase().trim();
            tableRows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(kw) ? '' : 'none';
            });
        });
    }

    /* --- Auto-dismiss flash success setelah 4 detik --- */
    const flashAlert = document.querySelector('.alert-ta-success');
    if (flashAlert) {
        setTimeout(() => {
            flashAlert.style.transition = 'opacity .4s ease';
            flashAlert.style.opacity   = '0';
            setTimeout(() => flashAlert.remove(), 400);
        }, 4000);
    }
</script>
</body>
</html>