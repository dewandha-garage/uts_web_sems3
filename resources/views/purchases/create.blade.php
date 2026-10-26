@extends('layouts.app')

@section('title', 'Catat Pembelian Stok')
@section('page_title', 'Pengadaan Stok Masuk (Stock-In)')
@section('page_subtitle', 'Catat pembelian barang dari pemasok untuk menambah stok otomatis.')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form" class="space-y-5">
        @csrf

        <!-- Supplier & Tanggal -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 pb-2 border-b border-slate-100">
                Data Transaksi & Supplier
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="supplier_id" class="block text-xs font-medium text-slate-700 mb-1">
                        Supplier / Pemasok <span class="text-rose-500">*</span>
                    </label>
                    <select name="supplier_id" id="supplier_id" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('supplier_id') border-rose-400 @enderror">
                        <option value="">Pilih Supplier...</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="purchased_at" class="block text-xs font-medium text-slate-700 mb-1">
                        Tanggal & Waktu Pembelian <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" name="purchased_at" id="purchased_at" value="{{ old('purchased_at', now()->format('Y-m-d\TH:i')) }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="mt-3">
                <label for="notes" class="block text-xs font-medium text-slate-700 mb-1">
                    Catatan / Nomor Faktur Supplier (Opsional)
                </label>
                <input type="text" name="notes" id="notes" value="{{ old('notes') }}"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Contoh: No. Surat Jalan SJ/2026/01">
            </div>
        </div>

        <!-- Detail Items -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-5">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                    Rincian Barang Pembelian
                </h3>
                <button type="button" onclick="addItemRow()" class="px-2.5 py-1 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md transition">
                    + Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="items-table">
                    <thead>
                        <tr class="text-slate-500 font-semibold border-b border-slate-200 pb-2">
                            <th class="pb-2 w-5/12">Item Produk</th>
                            <th class="pb-2 w-2/12 text-center">Jumlah</th>
                            <th class="pb-2 w-2/12 text-right">Harga Modal (Rp)</th>
                            <th class="pb-2 w-2/12 text-right">Subtotal</th>
                            <th class="pb-2 w-1/12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="items-body" class="divide-y divide-slate-100">
                        <!-- Dynamic Rows -->
                    </tbody>
                </table>
            </div>

            <!-- Grand Total -->
            <div class="mt-4 pt-3 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-2">
                <span class="text-xs text-slate-500">
                    * Total dan penambahan stok dihitung otomatis oleh server.
                </span>
                <div class="text-right">
                    <span class="text-xs text-slate-500">Total Pembelian:</span>
                    <span class="text-lg font-bold text-slate-900 ml-2" id="grand-total-display">Rp 0</span>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end space-x-2">
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 transition">
                Simpan Pembelian
            </button>
        </div>
    </form>
</div>

<script>
    const productsData = @json($products);
    let rowIndex = 0;

    function addItemRow(selectedProductId = '', qty = 1, cost = '') {
        const tbody = document.getElementById('items-body');
        const tr = document.createElement('tr');
        tr.id = `row-${rowIndex}`;

        let optionsHtml = '<option value="">Pilih Produk...</option>';
        productsData.forEach(p => {
            const isSelected = p.id == selectedProductId ? 'selected' : '';
            optionsHtml += `<option value="${p.id}" data-cost="${p.purchase_price}" ${isSelected}>[${p.sku}] ${p.name} (Stok: ${p.stock})</option>`;
        });

        tr.innerHTML = `
            <td class="py-2.5 pr-2">
                <select name="items[${rowIndex}][product_id]" required onchange="handleProductChange(${rowIndex}, this)" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    ${optionsHtml}
                </select>
            </td>
            <td class="py-2.5 px-2">
                <input type="number" name="items[${rowIndex}][quantity]" value="${qty}" min="1" required oninput="calculateSubtotal(${rowIndex})" class="w-full px-2 py-1.5 text-center text-xs font-medium rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </td>
            <td class="py-2.5 px-2">
                <input type="number" name="items[${rowIndex}][unit_cost]" id="cost-${rowIndex}" value="${cost}" min="0" step="100" required oninput="calculateSubtotal(${rowIndex})" class="w-full px-2 py-1.5 text-right text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="0">
            </td>
            <td class="py-2.5 px-2 text-right font-semibold text-slate-800 text-xs" id="subtotal-${rowIndex}">
                Rp 0
            </td>
            <td class="py-2.5 pl-2 text-center">
                <button type="button" onclick="removeRow(${rowIndex})" class="text-rose-600 hover:text-rose-800 text-xs font-medium">
                    Hapus
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        if (cost !== '') {
            calculateSubtotal(rowIndex);
        }
        rowIndex++;
    }

    function handleProductChange(idx, selectEl) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        const defaultCost = selectedOpt.getAttribute('data-cost');
        if (defaultCost !== null && defaultCost !== undefined) {
            document.getElementById(`cost-${idx}`).value = Math.round(defaultCost);
            calculateSubtotal(idx);
        }
    }

    function calculateSubtotal(idx) {
        const row = document.getElementById(`row-${idx}`);
        if (!row) return;

        const qtyInput = row.querySelector(`input[name="items[${idx}][quantity]"]`);
        const costInput = row.querySelector(`input[name="items[${idx}][unit_cost]"]`);
        const subtotalEl = document.getElementById(`subtotal-${idx}`);

        const qty = parseFloat(qtyInput.value) || 0;
        const cost = parseFloat(costInput.value) || 0;
        const subtotal = qty * cost;

        subtotalEl.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        updateGrandTotal();
    }

    function removeRow(idx) {
        const tbody = document.getElementById('items-body');
        if (tbody.children.length <= 1) {
            alert('Minimal harus ada 1 item!');
            return;
        }
        const row = document.getElementById(`row-${idx}`);
        if (row) {
            row.remove();
            updateGrandTotal();
        }
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        const tbody = document.getElementById('items-body');
        for (let tr of tbody.children) {
            const qtyInput = tr.querySelector('input[name*="[quantity]"]');
            const costInput = tr.querySelector('input[name*="[unit_cost]"]');
            if (qtyInput && costInput) {
                const qty = parseFloat(qtyInput.value) || 0;
                const cost = parseFloat(costInput.value) || 0;
                grandTotal += (qty * cost);
            }
        }
        document.getElementById('grand-total-display').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (productsData.length > 0) {
            addItemRow(productsData[0].id, 1, Math.round(productsData[0].purchase_price));
        } else {
            addItemRow();
        }
    });
</script>
@endsection
