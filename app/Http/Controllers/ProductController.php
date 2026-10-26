<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $stockStatus = $request->query('stock_status');

        $categories = Category::orderBy('name')->get();

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($stockStatus, function ($query, $stockStatus) {
                if ($stockStatus === 'low') {
                    $query->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0);
                } elseif ($stockStatus === 'out') {
                    $query->where('stock', '<=', 0);
                } elseif ($stockStatus === 'safe') {
                    $query->whereColumn('stock', '>', 'minimum_stock');
                }
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId', 'stockStatus'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU ini sudah digunakan oleh produk lain.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'purchase_price.min' => 'Harga beli tidak boleh negatif.',
            'selling_price.min' => 'Harga jual tidak boleh negatif.',
            'stock.min' => 'Stok awal tidak boleh bernilai negatif.',
            'minimum_stock.min' => 'Batas minimum stok tidak boleh negatif.',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk baru berhasil didaftarkan ke inventaris.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'purchaseItems.purchase.supplier', 'saleItems.sale.user']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,'.$product->id],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU ini sudah digunakan oleh produk lain.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'purchase_price.min' => 'Harga beli tidak boleh bernilai negatif.',
            'selling_price.min' => 'Harga jual tidak boleh bernilai negatif.',
            'stock.min' => 'Stok tidak boleh bernilai negatif.',
            'minimum_stock.min' => 'Batas minimum stok tidak boleh negatif.',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Informasi produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $hasTransactions = $product->purchaseItems()->exists() || $product->saleItems()->exists();

        // Soft delete guarantees data integrity while preserving transaction history
        $product->delete();

        $message = $hasTransactions
            ? 'Produk berhasil diarsipkan (soft delete) karena memiliki riwayat transaksi.'
            : 'Produk berhasil dihapus dari inventaris.';

        return redirect()->route('products.index')->with('success', $message);
    }
}
