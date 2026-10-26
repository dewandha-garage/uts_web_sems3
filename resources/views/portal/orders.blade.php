@extends('layouts.portal')

@section('title', 'Riwayat Pesanan Saya')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Riwayat Faktur & Pemesanan Saya</h2>
        <p class="text-xs text-slate-500 mt-1">Daftar transaksi pesanan yang telah Anda lakukan melalui portal pelanggan</p>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-3.5">No. Order / Faktur</th>
                        <th class="px-6 py-3.5">Waktu Pemesanan</th>
                        <th class="px-6 py-3.5">Rincian Barang</th>
                        <th class="px-6 py-3.5 text-right">Total Tagihan</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-xs text-indigo-600">
                            {{ $order->sale_number }}
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                            {{ $order->sold_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-xs">
                            @foreach($order->items as $item)
                                <div class="font-medium text-slate-800">
                                    &bull; {{ $item->product->name ?? 'Produk' }} ({{ $item->quantity }} Pcs)
                                </div>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-slate-900">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($order->status === 'completed')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Pesanan Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    Dibatalkan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('portal.orderDetail', $order) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                Rincian & Cetak Struk
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                            Anda belum pernah melakukan pemesanan.
                            <div class="mt-2">
                                <a href="{{ route('portal.catalog') }}" class="text-xs text-indigo-600 font-bold hover:underline">Mulai Belanja &rarr;</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
