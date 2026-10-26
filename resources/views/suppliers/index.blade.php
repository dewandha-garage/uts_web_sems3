@extends('layouts.app')

@section('title', 'Daftar Pemasok')
@section('page_title', 'Master Data Pemasok (Suppliers)')
@section('page_subtitle', 'Pihak distributor mitra penyedia stok barang inventaris.')

@section('content')
<div class="space-y-5">

    <!-- Action & Search Toolbar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('suppliers.index') }}" method="GET" class="relative flex-1 max-w-md">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, atau telepon supplier..."
                class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </form>

        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-colors shrink-0">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Supplier Baru
        </a>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-3.5">#</th>
                        <th class="px-6 py-3.5">Nama Perusahaan / Toko</th>
                        <th class="px-6 py-3.5">Kontak (Email / Telepon)</th>
                        <th class="px-6 py-3.5">Alamat Gudang / Kantor</th>
                        <th class="px-6 py-3.5 text-center">Transaksi Pembelian</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($suppliers as $index => $sup)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4 text-xs font-medium text-slate-400">
                            {{ $suppliers->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900">
                            {{ $sup->name }}
                        </td>
                        <td class="px-6 py-4 text-xs space-y-0.5">
                            <div class="font-medium text-slate-700">{{ $sup->phone ?: '-' }}</div>
                            <div class="text-slate-400">{{ $sup->email ?: '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500 max-w-xs truncate">
                            {{ $sup->address ?: '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-100">
                                {{ $sup->purchases_count }} Transaksi
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('suppliers.edit', $sup) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                Ubah
                            </a>
                            <form action="{{ route('suppliers.destroy', $sup) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus supplier ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-sm">
                            Tidak ada data pemasok yang sesuai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
