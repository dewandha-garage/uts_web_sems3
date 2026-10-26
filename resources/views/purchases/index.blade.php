@extends('layouts.app')

@section('title', 'Daftar Pembelian Stok (Stock-In)')
@section('page_title', 'Pengadaan Stok Barang (Purchases)')
@section('page_subtitle', 'Pencatatan faktur barang masuk dari supplier ke gudang inventaris.')

@section('content')
<div class="space-y-5">

    <!-- Search & Date Filter Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
        <form action="{{ route('purchases.index') }}" method="GET" class="flex-1 grid grid-cols-1 sm:grid-cols-4 gap-2.5">
            <!-- Search Text -->
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. PO / Supplier..."
                    class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Filter Status -->
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                </select>
            </div>

            <!-- Start Date -->
            <div>
                <input type="date" name="start_date" value="{{ $startDate }}" onchange="this.form.submit()"
                    class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" title="Mulai Tanggal">
            </div>

            <!-- End Date & Reset -->
            <div class="flex space-x-2">
                <input type="date" name="end_date" value="{{ $endDate }}" onchange="this.form.submit()"
                    class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" title="Sampai Tanggal">
                @if($search || $status || $startDate || $endDate)
                    <a href="{{ route('purchases.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl flex items-center shrink-0">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <a href="{{ route('purchases.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-colors shrink-0">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Catat Pembelian Baru
        </a>
    </div>

    <!-- Purchases Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-3.5">No. Pembelian</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Pemasok (Supplier)</th>
                        <th class="px-6 py-3.5">Item Barang</th>
                        <th class="px-6 py-3.5 text-right">Total Transaksi</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($purchases as $p)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-xs text-violet-600">
                            <a href="{{ route('purchases.show', $p) }}" class="hover:underline">
                                {{ $p->purchase_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-600 whitespace-nowrap">
                            {{ $p->purchased_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-900">
                            {{ $p->supplier->name ?? 'Supplier' }}
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $p->items->count() }} macam item
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-slate-900">
                            Rp {{ number_format($p->total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($p->status === 'completed')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    Dibatalkan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('purchases.show', $p) }}" class="inline-flex items-center px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                Rincian Faktur
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-400 text-sm">
                            Tidak ada transaksi pembelian stok yang cocok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $purchases->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
