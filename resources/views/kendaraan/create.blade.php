<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kendaraan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        merah: { DEFAULT: '#c0392b', tua: '#7f1d1d', muda: '#e74c3c' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#0f0f0f] min-h-screen font-sans antialiased flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-lg">

        {{-- Accent + Judul --}}
        <div class="w-10 h-[3px] bg-merah rounded mb-2"></div>
        <h1 class="text-white text-2xl font-semibold tracking-tight mb-1">
            Tambah <span class="text-merah">Kendaraan</span>
        </h1>
        <p class="text-zinc-500 text-xs mb-8">Isi data kendaraan baru di bawah ini</p>

        {{-- Error Bag --}}
        @if ($errors->any())
        <div class="bg-red-950 border border-merah-tua rounded-lg px-4 py-3 mb-6">
            <p class="text-red-400 text-xs font-medium mb-1">Terdapat kesalahan input:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                <li class="text-red-400 text-xs">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Form Card --}}
        <div class="bg-[#161616] border border-zinc-800/60 rounded-xl p-6 space-y-5">
            <form action="{{ route('kendaraan.store') }}" method="POST">
                @csrf

                {{-- Plat Nomor --}}
                <div>
                    <label for="plat_nomor" class="block text-xs font-medium text-zinc-400 uppercase tracking-widest mb-2">
                        Plat Nomor <span class="text-merah">*</span>
                    </label>
                    <input
                        type="text"
                        id="plat_nomor"
                        name="plat_nomor"
                        value="{{ old('plat_nomor') }}"
                        placeholder="Contoh: BK 1234 AB"
                        required
                        class="w-full bg-[#1e1e1e] border border-zinc-700 focus:border-merah focus:outline-none text-white placeholder-zinc-600 text-sm rounded-lg px-4 py-3 transition-colors duration-150 uppercase"
                    >
                    @error('plat_nomor')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Merk --}}
                <div>
                    <label for="merk" class="block text-xs font-medium text-zinc-400 uppercase tracking-widest mb-2">
                        Merk <span class="text-merah">*</span>
                    </label>
                    <input
                        type="text"
                        id="merk"
                        name="merk"
                        value="{{ old('merk') }}"
                        placeholder="Contoh: Toyota, Honda, Yamaha"
                        class="w-full bg-[#1e1e1e] border border-zinc-700 focus:border-merah focus:outline-none text-white placeholder-zinc-600 text-sm rounded-lg px-4 py-3 transition-colors duration-150"
                    >
                    @error('merk')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis --}}
                <div>
                    <label class="block text-xs font-medium text-zinc-400 uppercase tracking-widest mb-2">
                        Jenis Kendaraan <span class="text-merah">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="jenis" value="R2" class="peer sr-only"
                                {{ old('jenis') == 'R2' ? 'checked' : '' }}>
                            <div class="flex items-center justify-center gap-2 border border-zinc-700 peer-checked:border-merah peer-checked:bg-red-950/40 rounded-lg py-3 text-zinc-400 peer-checked:text-merah text-sm font-medium transition-all duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <circle cx="5.5" cy="17.5" r="2.5"/><circle cx="18.5" cy="17.5" r="2.5"/>
                                    <path stroke-linecap="round" d="M8 17.5h7M3 17.5H1v-4l2-6h11l3 4h2a1 1 0 011 1v5h-2"/>
                                </svg>
                                R2 — Roda 2
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="jenis" value="R4" class="peer sr-only"
                                {{ old('jenis') == 'R4' ? 'checked' : '' }}>
                            <div class="flex items-center justify-center gap-2 border border-zinc-700 peer-checked:border-merah peer-checked:bg-red-950/40 rounded-lg py-3 text-zinc-400 peer-checked:text-merah text-sm font-medium transition-all duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <circle cx="5" cy="17" r="2"/><circle cx="19" cy="17" r="2"/>
                                    <path stroke-linecap="round" d="M7 17h10M1 17H3V9l3-5h10l4 5h1a1 1 0 011 1v7h-2"/>
                                </svg>
                                R4 — Roda 4
                            </div>
                        </label>
                    </div>
                    @error('jenis')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tahun --}}
                <div>
                    <label for="tahun" class="block text-xs font-medium text-zinc-400 uppercase tracking-widest mb-2">
                        Tahun <span class="text-merah">*</span>
                    </label>
                    <input
                        type="number"
                        id="tahun"
                        name="tahun"
                        value="{{ old('tahun') }}"
                        placeholder="Contoh: 2022"
                        min="1900"
                        max="{{ date('Y') }}"
                        class="w-full bg-[#1e1e1e] border border-zinc-700 focus:border-merah focus:outline-none text-white placeholder-zinc-600 text-sm rounded-lg px-4 py-3 transition-colors duration-150"
                    >
                    @error('tahun')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-merah hover:bg-merah-tua text-white text-sm font-medium py-3 rounded-lg transition-colors duration-200">
                        Simpan Kendaraan
                    </button>
                    <a href="{{ route('kendaraan.index') }}"
                        class="flex-1 text-center border border-zinc-700 hover:border-zinc-500 text-zinc-400 hover:text-white text-sm font-medium py-3 rounded-lg transition-all duration-150">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>

</body>
</html>