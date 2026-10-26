@extends('layouts.auth')

@section('title', 'Daftar Akun Pelanggan')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <!-- Header -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-blue-600 text-white font-bold text-xl shadow-sm mb-3">
            IP
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Inventa Pro</h1>
        <p class="text-sm text-slate-500 mt-1">Pendaftaran Akun Pelanggan</p>
    </div>

    <!-- Register Card -->
    <div class="bg-white py-8 px-6 sm:px-8 border border-slate-200 rounded-xl shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100">Buat Akun Baru</h2>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('name') border-rose-400 bg-rose-50/20 @enderror"
                    placeholder="Nama Lengkap / Nama Toko">
                @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('email') border-rose-400 bg-rose-50/20 @enderror"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-xs font-medium text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="08123456789">
            </div>

            <div>
                <label for="password" class="block text-xs font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('password') border-rose-400 bg-rose-50/20 @enderror"
                    placeholder="Minimal 6 karakter">
                @error('password')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-medium text-slate-700 mb-1">Ulangi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Konfirmasi password">
            </div>

            <button type="submit" class="w-full py-2.5 px-4 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                Daftar & Masuk
            </button>
        </form>

        <div class="mt-4 pt-3 border-t border-slate-100 text-center text-xs text-slate-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-700 hover:underline">Masuk</a>
        </div>
    </div>
</div>
@endsection
