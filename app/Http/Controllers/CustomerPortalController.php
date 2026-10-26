<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerPortalController extends Controller
{
    public function catalog(Request $request): View
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        $categories = Category::withCount('products')->get();

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('stock', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('portal.catalog', compact('products', 'categories', 'search', 'categoryId'));
    }

    public function order(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'quantity.min' => 'Jumlah pesanan minimal 1 item.',
        ]);

        $sale = DB::transaction(function () use ($request) {
            $product = Product::lockForUpdate()->findOrFail($request->product_id);
            $qty = (int) $request->quantity;

            if ($product->stock < $qty) {
                throw ValidationException::withMessages([
                    'quantity' => "Maaf, stok produk '{$product->name}' tidak mencukupi (Tersedia: {$product->stock}).",
                ]);
            }

            $user = Auth::user();
            $today = Carbon::now()->format('Ymd');
            $prefix = "ORD-{$today}-";

            $lastSale = Sale::where('sale_number', 'like', "{$prefix}%")
                ->orderBy('id', 'desc')
                ->first();

            $newNumber = $lastSale
                ? str_pad((string) (((int) substr($lastSale->sale_number, -4)) + 1), 4, '0', STR_PAD_LEFT)
                : '0001';

            $unitPrice = (float) $product->selling_price;
            $subtotal = $unitPrice * $qty;

            $sale = Sale::create([
                'user_id' => $user->id,
                'customer_name' => $user->name,
                'sale_number' => $prefix.$newNumber,
                'total' => $subtotal,
                'status' => 'completed',
                'notes' => $request->notes ?: 'Pemesanan mandiri melalui portal pelanggan.',
                'sold_at' => Carbon::now(),
            ]);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ]);

            $product->decrement('stock', $qty);

            return $sale;
        });

        return redirect()->route('portal.orderDetail', $sale)
            ->with('success', "Pesanan Anda berhasil dibuat dengan No. Order {$sale->sale_number}!");
    }

    public function orders(): View
    {
        $orders = Sale::with(['items.product'])
            ->where('user_id', Auth::id())
            ->latest('sold_at')
            ->paginate(10);

        return view('portal.orders', compact('orders'));
    }

    public function orderDetail(Sale $sale): View
    {
        if ($sale->user_id !== Auth::id() && ! Auth::user()->hasRole(['owner', 'admin'])) {
            abort(403, 'Anda tidak memiliki hak untuk melihat pesanan ini.');
        }

        $sale->load(['user', 'items.product']);

        return view('portal.order-detail', compact('sale'));
    }
}
