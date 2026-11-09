@extends('layouts.app')

@section('title', 'Login — Portal Akademik')

@section('content')
<div class="max-w-md mx-auto py-8">
    <header class="border-b border-slate-200 dark:border-slate-700 pb-6 mb-8 text-center">
        <h1 class="text-3xl font-black tracking-tight text-gray-900 dark:text-gray-100 mb-2">Masuk ke Portal</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Gunakan akun Anda untuk mengelola proyek akademik.</p>
    </header>

    {{-- Demo Account Info --}}
    <div class="mb-6 p-4 border border-blue-200 dark:border-blue-800 bg-blue-50/50 dark:bg-blue-950/20 text-xs text-blue-800 dark:text-blue-300">
        <p class="font-bold mb-1">Akun Demo Pengujian:</p>
        <p>Email: <code class="font-mono font-semibold">demo@example.com</code></p>
        <p>Password: <code class="font-mono font-semibold">password123</code></p>
    </div>

    <form method="POST" action="{{ route('login.submit') }}" class="border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-mono uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                Email Address
            </label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-its dark:focus:border-blue-400 @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-xs font-mono uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                Password
            </label>
            <input type="password" name="password" id="password" required
                class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-its dark:focus:border-blue-400 @error('password') border-red-500 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember me --}}
        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center gap-2 text-gray-600 dark:text-gray-400 cursor-pointer">
                <input type="checkbox" name="remember" class="accent-its">
                <span>Ingat saya di perangkat ini</span>
            </label>
        </div>

        {{-- Submit button --}}
        <button type="submit"
            class="w-full py-2.5 px-4 text-sm font-semibold text-white bg-its hover:bg-blue-900 dark:bg-blue-600 dark:hover:bg-blue-700 transition">
            Masuk
        </button>

        {{-- Register link --}}
        <div class="text-center pt-3 border-t border-slate-100 dark:border-slate-700 text-xs text-gray-500 dark:text-gray-400">
            Belum memiliki akun?
            <a href="{{ route('register', request()->only('theme')) }}" class="font-semibold text-its dark:text-blue-400 hover:underline">
                Daftar sekarang
            </a>
        </div>
    </form>
</div>
@endsection
