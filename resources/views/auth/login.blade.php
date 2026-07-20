@extends('layout.app')

@section('title', 'Masuk')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-display font-bold text-ta-ink">Masuk</h1>
            <p class="text-sm text-ta-muted mt-1">Sistem Monitoring BBM — PT Telkom Akses Binjai</p>
        </div>

        <div class="bg-white border border-ta-border rounded-2xl shadow-sm p-8">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-5">
                    <label for="email" class="block text-xs font-bold uppercase tracking-widest text-ta-muted mb-2">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="field {{ $errors->has('email') ? 'field-error' : '' }}"
                           placeholder="admin@telkomakses.co.id">
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-xs font-bold uppercase tracking-widest text-ta-muted mb-2">Kata Sandi</label>
                    <input id="password" type="password" name="password" required
                           class="field {{ $errors->has('password') ? 'field-error' : '' }}"
                           placeholder="••••••••">
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 text-sm text-ta-muted cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" class="rounded border-ta-border text-ta-red focus:ring-ta-red">
                        Ingat saya
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-br from-ta-red to-ta-dark text-white font-semibold text-sm py-3 rounded-xl hover:brightness-105 transition-all">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk
                </button>

                @if(session('status'))
                    <p class="text-green-600 text-sm text-center mt-4">{{ session('status') }}</p>
                @endif
            </form>
        </div>

    </div>
</div>
@endsection
