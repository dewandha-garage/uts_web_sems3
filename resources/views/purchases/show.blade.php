@extends('layouts.app')

@section('title', 'Faktur Pembelian ' . $purchase->purchase_number)
@section('page_title', 'Faktur Pengadaan Stok')
@section('page_subtitle', 'Detail transaksi barang masuk dan verifikasi stok gudang.')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="no-print flex items-center justify-between">
        <a href="{{ route('purchases.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 inline-flex items-center">
            &larr; Kembali ke Daftar Pembelian
        </a>

        <div class="flex items-center space-x-2">
            <button type="button" onclick="window.print()" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Faktur (Print)
            </button>

            @if($purchase->status === 'completed' && auth()->user()->isOwner())
                <form action="{{ route('purchases.cancel', $purchase) }}" method="POST" onsubmit="return confirm('PERINGATAN OWNER: Membatalkan faktur pembelian ini akan secara konsisten MENGURANGI stok barang yang telah bertambah sebelumnya. Lanjutkan?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition-colors inline-flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        Batalkan Transaksi & Pulihkan Stok
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
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
                <p class="text-xs text-slate-500 mt-1">Sistem Distribusi & Manajemen Pergudangan</p>
                <div class="mt-4 text-xs text-slate-600">
                    <span class="font-semibold text-slate-800">Petugas Penerima (PIC):</span> {{ $purchase->user->name ?? '-' }}<br>
                    <span class="font-semibold text-slate-800">Waktu Transaksi:</span> {{ $purchase->purchased_at->format('d F Y, H:i') }} WIB
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block mb-1">FAKTUR PENGADAAN (STOCK-IN)</span>
                <span class="font-mono text-xl font-extrabold text-violet-700 block">{{ $purchase->purchase_number }}</span>
                <div class="mt-2">
                    @if($purchase->status === 'completed')
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            TRANSAKSI SELESAI
                        </span>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                            TRANSAKSI DIBATALKAN (VOID)
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Supplier & Notes Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-slate-100 text-xs">
            <div>
                <span class="font-bold uppercase tracking-wider text-slate-400 block mb-1">Diterima Dari Pemasok:</span>
                <span class="text-sm font-bold text-slate-900 block">{{ $purchase->supplier->name ?? 'Supplier' }}</span>
                <span class="text-slate-600 block mt-0.5">{{ $purchase->supplier->phone ?: 'No Telp: -' }}</span>
                <span class="text-slate-600 block">{{ $purchase->supplier->address ?: 'Alamat: -' }}</span>
            </div>

            <div>
                <span class="font-bold uppercase tracking-wider text-slate-400 block mb-1">Catatan Dokumen / PO:</span>
                <p class="text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                    {{ $purchase->notes ?: 'Tidak ada catatan tambahan.' }}
                </p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="py-6">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-3 w-1/12">#</th>
                        <th class="py-3 w-5/12">Item Produk</th>
                        <th class="py-3 w-2/12 text-center">Kuantitas Masuk</th>
                        <th class="py-3 w-2/12 text-right">Harga Modal Satuan</th>
                        <th class="py-3 w-2/12 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($purchase->items as $index => $item)
                    <tr>
                        <td class="py-3.5 text-slate-400 font-medium">{{ $index + 1 }}</td>
                        <td class="py-3.5">
                            <span class="font-bold text-slate-900 block">{{ $item->product->name ?? 'Produk Terhapus' }}</span>
                            <span class="font-mono text-[11px] text-slate-400">{{ $item->product->sku ?? '-' }}</span>
                        </td>
                        <td class="py-3.5 text-center font-bold text-slate-900">
                            {{ $item->quantity }} Unit
                        </td>
                        <td class="py-3.5 text-right font-medium">
                            Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
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
                    <span>Jumlah Total Item:</span>
                    <span class="font-bold text-slate-800">{{ $purchase->items->sum('quantity') }} Pcs</span>
                </div>
                <div class="flex justify-between py-2 border-t border-slate-200 text-base font-extrabold text-slate-900">
                    <span>Total Pembelian:</span>
                    <span class="text-violet-700">Rp {{ number_format($purchase->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Signatures for Print -->
        <div class="mt-12 pt-8 border-t border-slate-200 grid grid-cols-2 text-center text-xs text-slate-600 print-only">
            <div>
                <p class="mb-14">Pengirim (Supplier)</p>
                <p class="font-bold">( ............................................ )</p>
            </div>
            <div>
                <p class="mb-14">Penerima Gudang (PIC)</p>
                <p class="font-bold">{{ $purchase->user->name ?? '( ............................................ )' }}</p>
            </div>
        </div>

    </div>
</div>
@endsection
