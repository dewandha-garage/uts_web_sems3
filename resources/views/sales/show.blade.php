@extends('layouts.app')

@section('title', 'Faktur Penjualan ' . $sale->sale_number)
@section('page_title', 'Struk & Faktur Penjualan')
@section('page_subtitle', 'Bukti transaksi penjualan resmi dan rincian pemotongan stok.')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="no-print flex items-center justify-between">
        <a href="{{ route('sales.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 inline-flex items-center">
            &larr; Kembali ke Daftar Penjualan
        </a>

        <div class="flex items-center space-x-2">
            <button type="button" onclick="window.print()" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Struk / Faktur
            </button>

            @if($sale->status === 'completed' && auth()->user()->isOwner())
                <form action="{{ route('sales.cancel', $sale) }}" method="POST" onsubmit="return confirm('PERINGATAN OWNER: Membatalkan faktur penjualan ini akan secara otomatis MENGEMBALIKAN seluruh stok produk yang telah berkurang. Lanjutkan?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition-colors inline-flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        Batalkan Faktur & Pulihkan Stok
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Printable Invoice Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-8 sm:p-10 print-card">
        
        <!-- Header Info -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 pb-8 border-b border-slate-200">
            <div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">
                        IP
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">Inventa Pro</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Distribusi Produk & Grosir Resmi</p>
                <div class="mt-4 text-xs text-slate-600">
                    <span class="font-semibold text-slate-800">Petugas Kasir:</span> {{ $sale->user->name ?? '-' }}<br>
                    <span class="font-semibold text-slate-800">Waktu Penjualan:</span> {{ $sale->sold_at->format('d F Y, H:i') }} WIB
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block mb-1">FAKTUR PENJUALAN (INVOICE)</span>
                <span class="font-mono text-xl font-extrabold text-indigo-700 block">{{ $sale->sale_number }}</span>
                <div class="mt-2">
                    @if($sale->status === 'completed')
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            LUNAS / SELESAI
                        </span>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                            TRANSAKSI DIBATALKAN (VOID)
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Customer & Notes Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-slate-100 text-xs">
            <div>
                <span class="font-bold uppercase tracking-wider text-slate-400 block mb-1">Nama Pembeli:</span>
                <span class="text-sm font-bold text-slate-900 block">{{ $sale->customer_name }}</span>
            </div>

            <div>
                <span class="font-bold uppercase tracking-wider text-slate-400 block mb-1">Catatan Transaksi:</span>
                <p class="text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                    {{ $sale->notes ?: 'Transaksi selesai secara normal.' }}
                </p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="py-6">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-3 w-1/12">#</th>
                        <th class="py-3 w-5/12">Nama Barang</th>
                        <th class="py-3 w-2/12 text-center">Jumlah Kuantitas</th>
                        <th class="py-3 w-2/12 text-right">Harga Satuan</th>
                        <th class="py-3 w-2/12 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($sale->items as $index => $item)
                    <tr>
                        <td class="py-3.5 text-slate-400 font-medium">{{ $index + 1 }}</td>
                        <td class="py-3.5">
                            <span class="font-bold text-slate-900 block">{{ $item->product->name ?? 'Produk Terhapus' }}</span>
                            <span class="font-mono text-[11px] text-slate-400">{{ $item->product->sku ?? '-' }}</span>
                        </td>
                        <td class="py-3.5 text-center font-bold text-slate-900">
                            {{ $item->quantity }} Pcs
                        </td>
                        <td class="py-3.5 text-right font-medium">
                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 text-right font-bold text-slate-900">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Summary -->
        <div class="pt-4 border-t border-slate-200 flex justify-end">
            <div class="w-full sm:w-72 space-y-2 text-xs">
                <div class="flex justify-between py-1 text-slate-600">
                    <span>Total Kuantitas:</span>
                    <span class="font-bold text-slate-800">{{ $sale->items->sum('quantity') }} Pcs</span>
                </div>
                <div class="flex justify-between py-2 border-t border-slate-200 text-base font-extrabold text-slate-900">
                    <span>Total Pembayaran:</span>
                    <span class="text-emerald-700">Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Receipt Notes for Print -->
        <div class="mt-12 pt-8 border-t border-slate-200 text-center text-xs text-slate-500 print-only">
            <p class="font-semibold text-slate-700">Terima kasih atas kunjungan dan kepercayaan Anda!</p>
            <p class="mt-1 text-[11px] text-slate-400">Barang yang sudah dibeli dapat ditukar dalam 1x24 jam dengan membawa struk asli.</p>
        </div>

    </div>
</div>
@endsection
