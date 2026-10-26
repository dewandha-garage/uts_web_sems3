@extends('layouts.portal')

@section('title', 'Katalog Produk')

@section('content')
<div class="space-y-5">

    <!-- Page Header (Simple & Clean) -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs">
        <h1 class="text-xl font-bold text-slate-900">Katalog Barang & Produk</h1>
        <p class="text-xs text-slate-500 mt-1">Pilih barang dan jumlah pesanan untuk melakukan pembelian langsung.</p>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Categories Filter -->
        <div class="flex items-center space-x-1.5 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
            <a href="{{ route('portal.catalog') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition {{ empty($categoryId) ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Semua Kategori
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('portal.catalog', ['category_id' => $cat->id, 'search' => $search]) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition {{ $categoryId == $cat->id ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        <!-- Search Form -->
        <form action="{{ route('portal.catalog') }}" method="GET" class="w-full sm:w-64">
            @if($categoryId)
                <input type="hidden" name="category_id" value="{{ $categoryId }}">
            @endif
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang / SKU..."
                class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </form>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($products as $p)
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs hover:border-slate-300 transition flex flex-col justify-between">
            
            <div class="p-4">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="text-[10px] font-mono text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                        {{ $p->sku }}
                    </span>
                    @if($p->stock > 0)
                        <span class="text-[10px] font-medium text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded">
                            Stok: {{ $p->stock }}
                        </span>
                    @else
                        <span class="text-[10px] font-medium text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded">
                            Habis
                        </span>
                    @endif
                </div>

                <h3 class="font-bold text-slate-900 text-sm leading-snug">
                    {{ $p->name }}
                </h3>

                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $p->category->name ?? 'Umum' }}
                </p>

                @if($p->description)
                    <p class="text-xs text-slate-500 mt-2 line-clamp-2">
                        {{ $p->description }}
                    </p>
                @endif
            </div>

            <div class="p-4 pt-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-900 block">
                        Rp {{ number_format($p->selling_price, 0, ',', '.') }}
                    </span>
                </div>

                @if($p->stock > 0)
                    <button type="button" onclick="openOrderModal('{{ $p->id }}', '{{ addslashes($p->name) }}', '{{ $p->selling_price }}', {{ $p->stock }})"
                        class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                        Beli
                    </button>
                @else
                    <button type="button" disabled class="px-3 py-1.5 text-xs text-slate-400 bg-slate-200 rounded-lg cursor-not-allowed">
                        Habis
                    </button>
                @endif
            </div>

        </div>
        @empty
        <div class="col-span-full bg-white rounded-xl p-8 text-center text-slate-400 border border-slate-200">
            <p class="text-xs">Tidak ada produk yang ditemukan.</p>
        </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-2xs">
        {{ $products->links() }}
    </div>
    @endif

</div>

<!-- Simple Clean Order Modal -->
<div id="order-modal" class="hidden fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-sm w-full p-5 sm:p-6 shadow-lg border border-slate-200 relative">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Form Pemesanan</h3>
            <button type="button" onclick="closeOrderModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('portal.order') }}" method="POST" class="space-y-3.5">
            @csrf
            <input type="hidden" name="product_id" id="modal-product-id">

            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-xs">
                <span class="font-semibold text-slate-800 block" id="modal-product-name"></span>
                <div class="flex justify-between items-center mt-1 text-slate-500">
                    <span>Harga: <strong class="text-slate-900" id="modal-product-price"></strong></span>
                    <span class="text-emerald-700" id="modal-product-stock"></span>
                </div>
            </div>

            <div>
                <label for="modal-quantity" class="block text-xs font-medium text-slate-700 mb-1">
                    Jumlah Pesanan
                </label>
                <input type="number" name="quantity" id="modal-quantity" value="1" min="1" required oninput="calculateModalTotal()"
                    class="w-full px-3 py-1.5 text-xs text-center font-bold rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div>
                <label for="modal-notes" class="block text-xs font-medium text-slate-700 mb-1">
                    Catatan (Opsional)
                </label>
                <textarea name="notes" id="modal-notes" rows="2"
                    class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    placeholder="Catatan pengiriman..."></textarea>
            </div>

            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex justify-between items-center text-xs">
                <span class="text-slate-600">Total Biaya:</span>
                <span class="font-bold text-slate-900" id="modal-total-display">Rp 0</span>
            </div>

            <div class="pt-2 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeOrderModal()" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 transition">
                    Konfirmasi Pesanan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let activePrice = 0;
    let maxStock = 1;

    function openOrderModal(productId, productName, price, stock) {
        document.getElementById('modal-product-id').value = productId;
        document.getElementById('modal-product-name').innerText = productName;
        document.getElementById('modal-product-price').innerText = 'Rp ' + Number(price).toLocaleString('id-ID');
        document.getElementById('modal-product-stock').innerText = 'Tersedia: ' + stock;
        
        activePrice = parseFloat(price);
        maxStock = parseInt(stock);

        const qtyInput = document.getElementById('modal-quantity');
        qtyInput.value = 1;
        qtyInput.max = maxStock;

        calculateModalTotal();
        document.getElementById('order-modal').classList.remove('hidden');
    }

    function closeOrderModal() {
        document.getElementById('order-modal').classList.add('hidden');
    }

    function calculateModalTotal() {
        const qty = parseInt(document.getElementById('modal-quantity').value) || 0;
        const total = qty * activePrice;
        document.getElementById('modal-total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }
</script>
@endsection
