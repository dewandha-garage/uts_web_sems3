@extends('layouts.portal')

@section('title', 'Nota Pesanan ' . $sale->sale_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Action Toolbar -->
    <div class="no-print flex items-center justify-between">
        <a href="{{ route('portal.orders') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 inline-flex items-center">
            &larr; Kembali ke Riwayat Pesanan
        </a>

        <button type="button" onclick="window.print()" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors inline-flex items-center shadow-xs">
            <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Nota Pembelian
        </button>
    </div>

    <!-- Printable Customer Receipt Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-8 sm:p-10 print-card">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 pb-8 border-b border-slate-200">
            <div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">
                        IP
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">Inventa Pro</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Distribusi Grosir & E-Katalog Resmi</p>
                <div class="mt-4 text-xs text-slate-600">
                    <span class="font-semibold text-slate-800">Nama Pemesan:</span> {{ $sale->customer_name }}<br>
                    <span class="font-semibold text-slate-800">Waktu Order:</span> {{ $sale->sold_at->format('d F Y, H:i') }} WIB
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block mb-1">BUKTI PEMESANAN KONSUMEN</span>
                <span class="font-mono text-xl font-extrabold text-indigo-700 block">{{ $sale->sale_number }}</span>
                <div class="mt-2">
                    @if($sale->status === 'completed')
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            PESANAN SELESAI
                        </span>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                            DIBATALKAN
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($sale->notes)
        <div class="py-4 border-b border-slate-100 text-xs">
            <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Catatan Pemesan:</span>
            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">{{ $sale->notes }}</p>
        </div>
        @endif

        <!-- Items Table -->
        <div class="py-6">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-3 w-1/12">#</th>
                        <th class="py-3 w-5/12">Barang Yang Dipesan</th>
                        <th class="py-3 w-2/12 text-center">Kuantitas</th>
                        <th class="py-3 w-2/12 text-right">Harga Satuan</th>
                        <th class="py-3 w-2/12 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($sale->items as $index => $item)
                    <tr>
                        <td class="py-3.5 text-slate-400 font-medium">{{ $index + 1 }}</td>
                        <td class="py-3.5">
                            <span class="font-bold text-slate-900 block">{{ $item->product->name ?? 'Produk' }}</span>
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
                    <span>Jumlah Item:</span>
                    <span class="font-bold text-slate-800">{{ $sale->items->sum('quantity') }} Pcs</span>
                </div>
                <div class="flex justify-between py-2 border-t border-slate-200 text-base font-extrabold text-slate-900">
                    <span>Total Pembayaran:</span>
                    <span class="text-indigo-700">Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Print Footer -->
        <div class="mt-10 pt-6 border-t border-slate-200 text-center text-xs text-slate-400 print-only">
            Terima kasih telah berbelanja di Inventa Pro!
        </div>

    </div>

</div>
@endsection
