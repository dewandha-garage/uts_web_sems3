@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan operasional stok barang dan transaksi penjualan.')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards Grid (Simple, Clean, Enterprise Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Penjualan Bulan Ini -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs">
            <span class="text-xs font-medium text-slate-500 block mb-1">Penjualan Bulan Ini</span>
            <div class="text-2xl font-bold text-slate-900 tracking-tight">
                Rp {{ number_format($salesThisMonth, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-xs text-slate-500">
                Hari ini: <strong class="text-emerald-600 font-semibold">Rp {{ number_format($salesToday, 0, ',', '.') }}</strong> ({{ $totalSalesCount }} trx)
            </div>
        </div>

        <!-- Pembelian Bulan Ini -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs">
            <span class="text-xs font-medium text-slate-500 block mb-1">Pembelian Stok (Bulan Ini)</span>
            <div class="text-2xl font-bold text-slate-900 tracking-tight">
                Rp {{ number_format($purchasesThisMonth, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-xs text-slate-500">
                Total: {{ $totalPurchasesCount }} transaksi pengadaan
            </div>
        </div>

        <!-- Total Produk -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs">
            <span class="text-xs font-medium text-slate-500 block mb-1">Total Item Produk</span>
            <div class="text-2xl font-bold text-slate-900 tracking-tight">
                {{ $totalProducts }} <span class="text-xs font-normal text-slate-500">SKU</span>
            </div>
            <div class="mt-2 text-xs">
                <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline">Kelola inventaris &rarr;</a>
            </div>
        </div>

        <!-- Alert Stok Rendah -->
        <div class="bg-white rounded-xl p-5 border {{ $lowStockCount > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200' }} shadow-2xs">
            <span class="text-xs font-medium {{ $lowStockCount > 0 ? 'text-amber-800' : 'text-slate-500' }} block mb-1">Stok Perlu Perhatian</span>
            <div class="text-2xl font-bold {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-slate-900' }} tracking-tight">
                {{ $lowStockCount }} <span class="text-xs font-normal text-slate-500">Item</span>
            </div>
            <div class="mt-2 text-xs">
                @if($lowStockCount > 0)
                    <span class="text-amber-700 font-medium">Stok &le; batas minimum</span>
                @else
                    <span class="text-slate-500">Semua stok aman</span>
                @endif
            </div>
        </div>

    </div>

    <!-- Low Stock Alert Warning Box & Table -->
    @if($lowStockCount > 0)
    <div class="bg-white rounded-xl border border-amber-200 overflow-hidden shadow-2xs">
        <div class="px-5 py-3.5 bg-amber-50/70 border-b border-amber-200 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="font-bold text-amber-900 text-xs sm:text-sm">Peringatan: Produk Mendekati / Melewati Batas Minimum Stok</h3>
            </div>
            <a href="{{ route('purchases.create') }}" class="text-xs font-medium px-3 py-1 rounded-md bg-amber-600 text-white hover:bg-amber-700 transition">
                + Tambah Pembelian
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                        <th class="px-5 py-2.5">SKU</th>
                        <th class="px-5 py-2.5">Nama Produk</th>
                        <th class="px-5 py-2.5">Kategori</th>
                        <th class="px-5 py-2.5 text-center">Stok</th>
                        <th class="px-5 py-2.5 text-center">Batas Min</th>
                        <th class="px-5 py-2.5 text-right">Harga Jual</th>
                        <th class="px-5 py-2.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($lowStockProducts as $lowP)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-2.5 font-mono text-slate-600">{{ $lowP->sku }}</td>
                        <td class="px-5 py-2.5 font-semibold text-slate-900">{{ $lowP->name }}</td>
                        <td class="px-5 py-2.5 text-slate-500">{{ $lowP->category->name ?? '-' }}</td>
                        <td class="px-5 py-2.5 text-center font-bold {{ $lowP->stock <= 0 ? 'text-rose-600' : 'text-amber-700' }}">
                            {{ $lowP->stock }}
                        </td>
                        <td class="px-5 py-2.5 text-center text-slate-500">{{ $lowP->minimum_stock }}</td>
                        <td class="px-5 py-2.5 text-right">Rp {{ number_format($lowP->selling_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-2.5 text-center">
                            @if($lowP->stock <= 0)
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-800">
                                    Habis (0)
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800">
                                    Menipis
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- 2 Column Grid: Recent Sales & Recent Purchases -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <!-- Recent Sales -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Penjualan Terkini (Stock-Out)</h3>
                    <p class="text-xs text-slate-500">Transaksi kasir & pesanan pelanggan</p>
                </div>
                <a href="{{ route('sales.index') }}" class="text-xs text-blue-600 hover:underline">
                    Semua &rarr;
                </a>
            </div>
            <div class="divide-y divide-slate-100 text-xs">
                @forelse($recentSales as $sale)
                    <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50 transition">
                        <div>
                            <a href="{{ route('sales.show', $sale) }}" class="font-mono font-semibold text-blue-600 hover:underline">
                                {{ $sale->sale_number }}
                            </a>
                            <p class="text-slate-500 mt-0.5">
                                {{ $sale->customer_name }} &bull; {{ $sale->sold_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-900 block">Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
                            <span class="text-[11px] text-slate-400">Kasir: {{ $sale->user->name ?? '-' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center text-slate-400">Belum ada transaksi penjualan.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Purchases -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Pembelian Terkini (Stock-In)</h3>
                    <p class="text-xs text-slate-500">Transaksi pengadaan stok dari supplier</p>
                </div>
                <a href="{{ route('purchases.index') }}" class="text-xs text-blue-600 hover:underline">
                    Semua &rarr;
                </a>
            </div>
            <div class="divide-y divide-slate-100 text-xs">
                @forelse($recentPurchases as $purchase)
                    <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50 transition">
                        <div>
                            <a href="{{ route('purchases.show', $purchase) }}" class="font-mono font-semibold text-blue-600 hover:underline">
                                {{ $purchase->purchase_number }}
                            </a>
                            <p class="text-slate-500 mt-0.5">
                                {{ $purchase->supplier->name ?? '-' }} &bull; {{ $purchase->purchased_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-900 block">Rp {{ number_format($purchase->total, 0, ',', '.') }}</span>
                            <span class="text-[11px] text-slate-400">PIC: {{ $purchase->user->name ?? '-' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center text-slate-400">Belum ada transaksi pembelian.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
