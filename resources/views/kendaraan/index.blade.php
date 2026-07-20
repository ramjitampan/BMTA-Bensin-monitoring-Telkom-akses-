@extends('layout.app')

@section('title', 'Data Kendaraan')
@section('meta_description', 'Kelola dan pantau seluruh data kendaraan operasional PT. Telkom Akses Binjai.')

@push('styles')
@vite('resources/Tema/kendaraan/kendaraan.css')
@endpush

@section('content')

{{-- ══════════════════════════════════════════
     HERO — matching Data Pegawai design system
     ══════════════════════════════════════════ --}}
<section class="hero-bg-kendaraan relative w-full overflow-hidden">

    {{-- Decorative circles --}}
    <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full border-[48px] border-white/[0.04] pointer-events-none hidden md:block"></div>
    <div class="absolute top-6 right-1/3 w-44 h-44 rounded-full border-[28px] border-white/[0.03] pointer-events-none hidden lg:block"></div>
    <div class="absolute -bottom-12 left-1/4 w-56 h-56 rounded-full border-[36px] border-white/[0.02] pointer-events-none hidden lg:block"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-white">Data Kendaraan</h1>
                <p class="mt-2 max-w-xl text-sm text-white/70">Kelola dan pantau seluruh kendaraan operasional perusahaan secara terpusat.</p>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('kendaraan.create') }}"
                   class="group inline-flex items-center gap-3 rounded-2xl border border-white/80 bg-white px-6 py-3 text-sm font-bold text-ta-dark shadow-xl transition-all duration-200 hover:-translate-y-1 hover:bg-ta-soft hover:shadow-2xl">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-ta-red transition-colors group-hover:bg-ta-dark">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    </span>
                    Tambah Kendaraan
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     BODY
     ══════════════════════════════════════════ --}}
<div class="kend-body">
<div class="kend-container">

    {{-- Flash --}}
    @if(session('success'))
    <div class="kend-flash" id="kFlash">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
        <button class="kend-flash-close" onclick="document.getElementById('kFlash').remove()">×</button>
    </div>
    @endif

    @php
        $totalR4 = $kendaraans->filter(fn($k) =>
            str_contains(strtolower($k->jenis),'mobil') || strtolower($k->jenis)==='r4'
        )->count();
    @endphp

    {{-- Top row: Stats + Search --}}
    <div class="kend-top-row">

        {{-- Stats --}}
        <div class="kend-stats">
            <div class="kend-stat">
                <span class="kend-stat-num" id="kCountDisplay">{{ count($kendaraans) }}</span>
                <span class="kend-stat-label">Kendaraan<br>Operasional</span>
            </div>
            <div class="kend-stat">
                <span class="kend-stat-num c-blue">{{ $totalR4 }}</span>
                <span class="kend-stat-label">Kendaraan<br>R4</span>
            </div>
        </div>

        {{-- Search --}}
        <div class="kend-search-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input
                id="kSearchInput"
                type="text"
                placeholder="Cari kendaraan… contoh: BK 9868 TAZ, Toyota Avanza, R4"
                autocomplete="off"
            >
            <button class="kend-search-clear" id="kClearSearch" aria-label="Hapus pencarian">×</button>
        </div>
    </div>

    @if(count($kendaraans) > 0)

    {{-- List header --}}
    <div class="kend-list-header kend-list-desktop">
        <span class="kend-list-label">Daftar Kendaraan</span>
        <span class="kend-list-count">
            Menampilkan <b id="kVisibleCount">{{ count($kendaraans) }}</b>
            dari {{ count($kendaraans) }} kendaraan
        </span>
    </div>

    {{-- ═══ DESKTOP LIST ═══ --}}
    <div class="kend-list kend-list-desktop" id="kVehList">

        @foreach($kendaraans as $i => $kendaraan)
        @php
            $j = strtolower($kendaraan->jenis);
            if      (str_contains($j,'mobil') || $j === 'r4')   { $pc = 'badge-r4';     $jLabel = 'Mobil R4'; $ico = 'fa-car'; }
            elseif  (str_contains($j,'pickup'))                  { $pc = 'badge-pickup'; $jLabel = 'Pickup';   $ico = 'fa-truck-pickup'; }
            elseif  (str_contains($j,'truck'))                   { $pc = 'badge-truck';  $jLabel = 'Truck';    $ico = 'fa-truck'; }
            else                                                 { $pc = 'badge-lain';   $jLabel = ucfirst($kendaraan->jenis); $ico = 'fa-car-side'; }
        @endphp
        <div class="kend-row"
             data-plat="{{ strtolower($kendaraan->plat_nomor) }}"
             data-merk="{{ strtolower($kendaraan->merk) }}"
             data-jenis="{{ strtolower($kendaraan->jenis) }}">

            <div class="kend-row-icon">
                <i class="fa-solid {{ $ico }}"></i>
            </div>

            <div class="kend-row-main">
                <div class="kend-row-plat">{{ $kendaraan->plat_nomor }}</div>
                <div class="kend-row-meta">
                    <span class="kend-row-merk">{{ $kendaraan->merk }}</span>
                    <span class="kend-row-sep">·</span>
                    <span class="kend-row-year">{{ $kendaraan->tahun }}</span>
                </div>
            </div>

            <div class="kend-badges">
                <span class="kend-badge {{ $pc }}">
                    <span class="kend-badge-dot"></span>{{ $jLabel }}
                </span>
                <span class="kend-badge badge-aktif">
                    <span class="kend-badge-dot"></span>Aktif
                </span>
            </div>

            <span class="kend-row-id">#{{ str_pad($kendaraan->id, 3, '0', STR_PAD_LEFT) }}</span>

            <div class="kend-row-actions">
                <a href="{{ route('kendaraan.edit', $kendaraan->id) }}"
                   class="kend-act-btn edit"
                   title="Edit kendaraan">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <form action="{{ route('kendaraan.destroy', $kendaraan->id) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="button"
                            class="kend-act-btn del"
                            title="Hapus kendaraan"
                            onclick="kConfirmDelete(this,'{{ addslashes($kendaraan->plat_nomor) }}')">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach

        <div class="kend-no-result" id="kNoResult">
            <strong>Tidak ada hasil</strong>
            <p>Tidak ditemukan kendaraan yang cocok dengan pencarian.</p>
        </div>
    </div>

    {{-- ═══ MOBILE CARDS ═══ --}}
    <div class="kend-cards" id="kCards">
        @foreach($kendaraans as $i => $kendaraan)
        @php
            $j = strtolower($kendaraan->jenis);
            if      (str_contains($j,'mobil') || $j === 'r4')   { $pc = 'badge-r4';     $jLabel = 'Mobil R4'; $ico = 'fa-car'; }
            elseif  (str_contains($j,'pickup'))                  { $pc = 'badge-pickup'; $jLabel = 'Pickup';   $ico = 'fa-truck-pickup'; }
            elseif  (str_contains($j,'truck'))                   { $pc = 'badge-truck';  $jLabel = 'Truck';    $ico = 'fa-truck'; }
            else                                                 { $pc = 'badge-lain';   $jLabel = ucfirst($kendaraan->jenis); $ico = 'fa-car-side'; }
        @endphp
        <div class="kend-card"
             data-plat="{{ strtolower($kendaraan->plat_nomor) }}"
             data-merk="{{ strtolower($kendaraan->merk) }}"
             data-jenis="{{ strtolower($kendaraan->jenis) }}">
            <div class="kend-card-body">
                <div class="kend-row-icon">
                    <i class="fa-solid {{ $ico }}"></i>
                </div>
                <div class="kend-card-info">
                    <div class="kend-card-plat">{{ $kendaraan->plat_nomor }}</div>
                    <div class="kend-card-sub">{{ $kendaraan->merk }} · {{ $kendaraan->tahun }}</div>
                    <div class="kend-card-badges">
                        <span class="kend-badge {{ $pc }}"><span class="kend-badge-dot"></span>{{ $jLabel }}</span>
                        <span class="kend-badge badge-aktif"><span class="kend-badge-dot"></span>Aktif</span>
                    </div>
                </div>
            </div>
            <div class="kend-card-foot">
                <a href="{{ route('kendaraan.edit', $kendaraan->id) }}" class="kend-card-btn edit">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                </a>
                <form action="{{ route('kendaraan.destroy', $kendaraan->id) }}" method="POST" style="flex:1;display:flex">
                    @csrf @method('DELETE')
                    <button type="button"
                            class="kend-card-btn del"
                            style="flex:1;border:none"
                            onclick="kConfirmDelete(this,'{{ addslashes($kendaraan->plat_nomor) }}')">
                        <i class="fa-solid fa-trash-can"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="px-4 pb-4">
        {{ $kendaraans->links() }}
    </div>

    @else

    {{-- Empty state --}}
    <div class="kend-empty">
        <div class="kend-empty-icon">
            <i class="fa-solid fa-truck"></i>
        </div>
        <h3>Belum Ada Kendaraan</h3>
        <p>Tambahkan kendaraan pertama untuk mulai memantau penggunaan BBM operasional.</p>
        <a href="{{ route('kendaraan.create') }}" class="kend-btn-primary">
            <i class="fa-solid fa-plus"></i>
            Tambah Kendaraan Pertama
        </a>
    </div>

    @endif

</div>
</div>

{{-- ══════════════════════════════════════════
     DELETE MODAL
     ══════════════════════════════════════════ --}}
<div id="kDeleteModal" class="kend-modal-wrap" role="dialog" aria-modal="true" aria-labelledby="kModalTitle">
    <div class="kend-modal-box">
        <div class="kend-modal-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 id="kModalTitle">Hapus Kendaraan?</h3>
        <p>Anda akan menghapus kendaraan dengan plat nomor:</p>
        <div class="kend-modal-plat">
            <strong id="kDeleteName"></strong>
            <small>Data tidak dapat dipulihkan setelah dihapus.</small>
        </div>
        <div class="kend-modal-actions">
            <button class="kend-modal-btn cancel" onclick="kCloseModal()">Batal</button>
            <button class="kend-modal-btn confirm" id="kConfirmBtn">Ya, Hapus</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    /* ── Search ── */
    const input   = document.getElementById('kSearchInput');
    const clear   = document.getElementById('kClearSearch');
    const counter = document.getElementById('kVisibleCount');
    const noRes   = document.getElementById('kNoResult');
    const rows    = Array.from(document.querySelectorAll('#kVehList .kend-row'));
    const cards   = Array.from(document.querySelectorAll('#kCards .kend-card'));

    function doSearch(q) {
        q = q.trim().toLowerCase();
        clear.style.display = q ? 'flex' : 'none';
        let n = 0;
        function matchEl(el) {
            const ok = !q
                || (el.dataset.plat  || '').includes(q)
                || (el.dataset.merk  || '').includes(q)
                || (el.dataset.jenis || '').includes(q);
            el.style.display = ok ? '' : 'none';
            if (ok) n++;
        }
        rows.forEach(matchEl);
        cards.forEach(matchEl);
        if (counter) counter.textContent = n;
        if (noRes)   noRes.style.display = (q && n === 0) ? 'block' : 'none';
    }

    if (input) {
        input.addEventListener('input', () => doSearch(input.value));
        if (clear) clear.addEventListener('click', () => { input.value = ''; doSearch(''); input.focus(); });
    }

    /* ── Entrance animation ── */
    const all = [...rows, ...cards];
    all.forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(12px)';
        el.style.transition = `opacity 0.32s ease ${i * 45}ms, transform 0.32s ease ${i * 45}ms`;
        requestAnimationFrame(() => requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }));
    });
})();

/* ── Delete modal ── */
let _kPendingForm = null;

function kConfirmDelete(btn, plat) {
    _kPendingForm = btn.closest('form');
    document.getElementById('kDeleteName').textContent = plat;
    document.getElementById('kDeleteModal').classList.add('open');
}
function kCloseModal() {
    document.getElementById('kDeleteModal').classList.remove('open');
    _kPendingForm = null;
}

document.getElementById('kConfirmBtn').addEventListener('click', function () {
    if (_kPendingForm) _kPendingForm.submit();
});
document.getElementById('kDeleteModal').addEventListener('click', function (e) {
    if (e.target === this) kCloseModal();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') kCloseModal();
});
</script>
@endpush