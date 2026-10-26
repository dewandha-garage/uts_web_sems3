@extends('layouts.app')

@section('title', 'Daftar Produk')
@section('page_title', 'Master Data Produk')
@section('page_subtitle', 'Kelola inventaris barang, kode SKU, penetapan harga, dan pemantauan stok.')

@section('content')
<div class="space-y-4">

    <!-- Search, Filter & Action Toolbar -->
    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
        <form action="{{ route('products.index') }}" method="GET" class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-2">
            <!-- Search Text -->
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama produk / SKU..."
                    class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Filter Category -->
            <div>
                <select name="category_id" onchange="this.form.submit()" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Stock Status -->
            <div class="flex space-x-1.5">
                <select name="stock_status" onchange="this.form.submit()" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status Stok</option>
                    <option value="low" {{ $stockStatus == 'low' ? 'selected' : '' }}>Stok Menipis (&le; Min)</option>
                    <option value="out" {{ $stockStatus == 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
                    <option value="safe" {{ $stockStatus == 'safe' ? 'selected' : '' }}>Stok Aman</option>
                </select>
                @if($search || $categoryId || $stockStatus)
                    <a href="{{ route('products.index') }}" class="px-2.5 py-1.5 text-xs text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg flex items-center shrink-0">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-2xs transition shrink-0">
            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Produk
        </a>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                        <th class="px-4 py-3">SKU</th>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-right">Harga Beli</th>
                        <th class="px-4 py-3 text-right">Harga Jual</th>
                        <th class="px-4 py-3 text-center">Stok / Min</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50 transition {{ $product->isLowStock() ? 'bg-amber-50/20' : ($product->isOutOfStock() ? 'bg-rose-50/20' : '') }}">
                        <td class="px-4 py-3 font-mono font-medium text-slate-600">
                            {{ $product->sku }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-900">
                            <a href="{{ route('products.show', $product) }}" class="hover:text-blue-600 hover:underline">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-slate-500">
                            {{ $product->category->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-right text-slate-500">
                            Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-slate-900">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-bold {{ $product->isOutOfStock() ? 'text-rose-600' : ($product->isLowStock() ? 'text-amber-700' : 'text-slate-800') }}">
                                {{ $product->stock }}
                            </span>
                            <span class="text-slate-400">/ {{ $product->minimum_stock }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($product->isOutOfStock())
                                <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-rose-100 text-rose-800">
                                    Habis
                                </span>
                            @elseif($product->isLowStock())
                                <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-800">
                                    Menipis
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-800">
                                    Aman
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('products.show', $product) }}" class="px-2 py-1 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded transition">
                                Detail
                            </a>
                            <a href="{{ route('products.edit', $product) }}" class="px-2 py-1 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded transition">
                                Edit
                            </a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus/arsipkan produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 text-rose-600 hover:bg-rose-50 rounded transition">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-slate-400">
                            Tidak ada produk yang cocok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
            {{ $products->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
