@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <!-- Header & Brand Logo -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-blue-600 text-white font-bold text-xl shadow-sm mb-3">
            IP
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Inventa Pro</h1>
        <p class="text-sm text-slate-500 mt-1">Sistem Informasi Inventaris & Penjualan</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white py-8 px-6 sm:px-8 border border-slate-200 rounded-xl shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100">Masuk ke Akun</h2>

        @if(session('success'))
            <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('email') border-rose-400 bg-rose-50/20 @enderror"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('password') border-rose-400 @enderror"
                    placeholder="••••••••">
                @error('password')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center text-slate-600 select-none cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2">
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                Masuk
            </button>
        </form>

        <!-- Demo Accounts Box (Clean & Simple) -->
        <div class="mt-6 pt-5 border-t border-slate-200">
            <p class="text-xs font-medium text-slate-500 mb-2">Akun Demo Pengujian:</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="setDemo('owner@example.com', 'password')" class="px-2 py-1.5 text-xs font-medium text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-md text-center transition">
                    Owner
                </button>
                <button type="button" onclick="setDemo('admin@example.com', 'password')" class="px-2 py-1.5 text-xs font-medium text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-md text-center transition">
                    Staff / Admin
                </button>
                <button type="button" onclick="setDemo('user@example.com', 'password')" class="px-2 py-1.5 text-xs font-medium text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-md text-center transition">
                    Customer
                </button>
            </div>
            <p class="text-[11px] text-slate-400 text-center mt-2">Password default: <code class="text-slate-600">password</code></p>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-100 text-center text-xs text-slate-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-700 hover:underline">Daftar pelanggan</a>
        </div>
    </div>

    <!-- Academic Footer -->
    <p class="text-center text-xs text-slate-400 mt-6">
        UTS Web Programming 2 &bull; Integrated Laravel Project
    </p>
</div>

<script>
    function setDemo(email, pass) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = pass;
    }
</script>
@endsection
