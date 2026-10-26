@extends('layouts.app')

@section('title', 'Detail Produk ' . $product->name)
@section('page_title', 'Detail Produk Inventaris')
@section('page_subtitle', 'Kartu stok barang dan riwayat transaksi keluar masuk.')

@section('content')
<div class="space-y-6">

    <!-- Top Info Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-lg border border-indigo-100">
                        {{ $product->sku }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700">
                        {{ $product->category->name ?? 'Tanpa Kategori' }}
                    </span>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $product->name }}</h2>
                @if($product->description)
                    <p class="text-sm text-slate-500 mt-1 max-w-2xl">{{ $product->description }}</p>
                @endif
            </div>

            <div class="flex items-center space-x-2 shrink-0">
                <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Ubah Data
                </a>
                <a href="{{ route('products.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    &larr; Kembali
                </a>
            </div>
        </div>

        <!-- 4 Metric Pillars -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block mb-1">Stok Tersedia</span>
                <div class="text-2xl font-extrabold {{ $product->isOutOfStock() ? 'text-rose-600' : ($product->isLowStock() ? 'text-amber-600' : 'text-slate-900') }}">
                    {{ $product->stock }} <span class="text-xs font-normal text-slate-500">Unit</span>
                </div>
                <span class="text-[11px] text-slate-400">Min. Alert: {{ $product->minimum_stock }} Unit</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block mb-1">Harga Beli / Modal</span>
                <div class="text-2xl font-extrabold text-slate-900">
                    Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-slate-400">Biaya pengadaan terakhir</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block mb-1">Harga Jual Satuan</span>
                <div class="text-2xl font-extrabold text-indigo-600">
                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-slate-400">Harga resmi kasir & portal</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block mb-1">Estimasi Margin Laba</span>
                @php
                    $margin = $product->selling_price - $product->purchase_price;
                    $marginPercent = $product->purchase_price > 0 ? ($margin / $product->purchase_price) * 100 : 0;
                @endphp
                <div class="text-2xl font-extrabold text-emerald-600">
                    Rp {{ number_format($margin, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-emerald-700 font-semibold">+{{ number_format($marginPercent, 1) }}% dari modal</span>
            </div>
        </div>
    </div>

    <!-- History Tables (Purchases & Sales) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Stock-In (Purchases) History -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Riwayat Pembelian Masuk (Stock-In)</h3>
                <p class="text-[11px] text-slate-500">Daftar transaksi pengadaan stok untuk barang ini</p>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($product->purchaseItems as $pItem)
                    <div class="px-6 py-3.5 flex items-center justify-between text-xs hover:bg-slate-50 transition-colors">
                        <div>
                            <span class="font-mono font-bold text-violet-600">{{ $pItem->purchase->purchase_number ?? '-' }}</span>
                            <p class="text-slate-500 mt-0.5">
                                Supplier: {{ $pItem->purchase->supplier->name ?? '-' }} &bull; {{ $pItem->purchase->purchased_at->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-emerald-700 text-sm block">+{{ $pItem->quantity }} Unit</span>
                            <span class="text-[11px] text-slate-400">@ Rp {{ number_format($pItem->unit_cost, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada riwayat pembelian masuk.</div>
                @endforelse
            </div>
        </div>

        <!-- Stock-Out (Sales) History -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Riwayat Penjualan Keluar (Stock-Out)</h3>
                <p class="text-[11px] text-slate-500">Daftar transaksi penjualan kasir & pesanan pelanggan</p>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($product->saleItems as $sItem)
                    <div class="px-6 py-3.5 flex items-center justify-between text-xs hover:bg-slate-50 transition-colors">
                        <div>
                            <span class="font-mono font-bold text-indigo-600">{{ $sItem->sale->sale_number ?? '-' }}</span>
                            <p class="text-slate-500 mt-0.5">
                                {{ $sItem->sale->customer_name ?? '-' }} &bull; {{ $sItem->sale->sold_at->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-rose-600 text-sm block">-{{ $sItem->quantity }} Unit</span>
                            <span class="text-[11px] text-slate-400">Subtotal: Rp {{ number_format($sItem->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada riwayat penjualan keluar.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
