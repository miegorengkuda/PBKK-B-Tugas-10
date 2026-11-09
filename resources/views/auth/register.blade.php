@extends('layouts.app')

@section('title', 'Daftar Akun — Portal Akademik')

@section('content')
<div class="max-w-md mx-auto py-8">
    <header class="border-b border-slate-200 dark:border-slate-700 pb-6 mb-8 text-center">
        <h1 class="text-3xl font-black tracking-tight text-gray-900 dark:text-gray-100 mb-2">Buat Akun Baru</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Daftarkan akun untuk mengelola proyek akademik Anda.</p>
    </header>

    <form method="POST" action="{{ route('register.submit') }}" class="border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 space-y-5">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <label for="name" class="block text-xs font-mono uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                Nama Lengkap
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-its dark:focus:border-blue-400 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-mono uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                Email Address
            </label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-its dark:focus:border-blue-400 @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-xs font-mono uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                Password (min. 8 karakter)
            </label>
            <input type="password" name="password" id="password" required
                class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-its dark:focus:border-blue-400 @error('password') border-red-500 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label for="password_confirmation" class="block text-xs font-mono uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                Konfirmasi Password
            </label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-its dark:focus:border-blue-400">
        </div>

        {{-- Submit button --}}
        <button type="submit"
            class="w-full py-2.5 px-4 text-sm font-semibold text-white bg-its hover:bg-blue-900 dark:bg-blue-600 dark:hover:bg-blue-700 transition">
            Daftar Akun
        </button>

        {{-- Login link --}}
        <div class="text-center pt-3 border-t border-slate-100 dark:border-slate-700 text-xs text-gray-500 dark:text-gray-400">
            Sudah memiliki akun?
            <a href="{{ route('login', request()->only('theme')) }}" class="font-semibold text-its dark:text-blue-400 hover:underline">
                Masuk di sini
            </a>
        </div>
    </form>
</div>
@endsection
