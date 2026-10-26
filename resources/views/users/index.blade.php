@extends('layouts.app')

@section('title', 'Kelola Pengguna & Hak Akses')
@section('page_title', 'Manajemen Pengguna (User & Role)')
@section('page_subtitle', 'Kontrol hak akses 3 tingkat: Owner (Pemilik), Staff / Admin (Operasional), dan User (Customer).')

@section('content')
<div class="space-y-5">

    <!-- Action & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <form action="{{ route('users.index') }}" method="GET" class="flex-1 flex space-x-2 max-w-lg">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, atau telepon..."
                    class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <select name="role" onchange="this.form.submit()" class="px-3 py-2 text-sm rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                <option value="">Semua Peran</option>
                <option value="owner" {{ $role === 'owner' ? 'selected' : '' }}>Owner</option>
                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Staff / Admin</option>
                <option value="user" {{ $role === 'user' ? 'selected' : '' }}>Customer (User)</option>
            </select>
        </form>

        <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-colors shrink-0">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Tambah Pengguna Baru
        </a>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-3.5">Nama Pengguna</th>
                        <th class="px-6 py-3.5">Email</th>
                        <th class="px-6 py-3.5">Telepon</th>
                        <th class="px-6 py-3.5 text-center">Tingkat Hak Akses (Role)</th>
                        <th class="px-6 py-3.5 text-slate-400">Terdaftar Pada</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($users as $u)
                    <tr class="hover:bg-slate-50/60 transition-colors {{ $u->id === auth()->id() ? 'bg-indigo-50/30' : '' }}">
                        <td class="px-6 py-4 font-bold text-slate-900">
                            {{ $u->name }}
                            @if($u->id === auth()->id())
                                <span class="ml-1.5 px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-100 text-indigo-700">Akun Anda</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600 font-mono text-xs">
                            {{ $u->email }}
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $u->phone ?: '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($u->isOwner())
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                    OWNER
                                </span>
                            @elseif($u->isAdmin())
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    STAFF / ADMIN
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                    CUSTOMER (USER)
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-400 whitespace-nowrap">
                            {{ $u->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('users.edit', $u) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                Ubah
                            </a>
                            @if($u->id !== auth()->id())
                                <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus akun pengguna {{ $u->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-sm">
                            Tidak ada data pengguna yang cocok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
