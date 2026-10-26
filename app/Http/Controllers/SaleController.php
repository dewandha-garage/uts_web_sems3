<?php

namespace App\Http\Controllers;

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

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $sales = Sale::with(['user', 'items.product'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sale_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($startDate, function ($query, $startDate) {
                $query->whereDate('sold_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->whereDate('sold_at', '<=', $endDate);
            })
            ->latest('sold_at')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales', 'search', 'status', 'startDate', 'endDate'));
    }

    public function create(): View
    {
        // Only load active products with stock > 0
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();

        return view('sales.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_name' => ['nullable', 'string', 'max:150'],
            'sold_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Minimal harus menambahkan 1 produk ke daftar transaksi.',
            'items.min' => 'Minimal harus menambahkan 1 produk ke daftar transaksi.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.quantity.min' => 'Jumlah pembelian minimal 1 item.',
        ]);

        $sale = DB::transaction(function () use ($request) {
            $saleNumber = $this->generateSaleNumber();

            $sale = Sale::create([
                'user_id' => Auth::id(),
                'customer_name' => $request->customer_name ?: 'Pelanggan Umum (Walk-in)',
                'sale_number' => $saleNumber,
                'total' => 0,
                'status' => 'completed',
                'notes' => $request->notes,
                'sold_at' => Carbon::parse($request->sold_at),
            ]);

            $total = 0;

            foreach ($request->items as $itemData) {
                // Lock for update ensures atomic stock check and prevents race conditions
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);
                $qty = (int) $itemData['quantity'];

                if ($qty < 1 || $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok produk '{$product->name}' tidak mencukupi! Tersedia: {$product->stock}, diminta: {$qty}.",
                    ]);
                }

                // Price is taken directly from the database, never trusted from client
                $unitPrice = (float) $product->selling_price;
                $subtotal = $unitPrice * $qty;
                $total += $subtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // Atomically decrement stock
                $product->decrement('stock', $qty);
            }

            // Update total calculated on server
            $sale->update(['total' => $total]);

            return $sale;
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', "Transaksi penjualan berhasil disimpan dengan No. Faktur {$sale->sale_number}.");
    }

    public function show(Sale $sale): View
    {
        $sale->load(['user', 'items.product']);

        return view('sales.show', compact('sale'));
    }

    public function cancel(Sale $sale): RedirectResponse
    {
        // Slide 5: Membatalkan transaksi sesuai aturan sistem (Owner only)
        if (! Auth::user()->isOwner()) {
            abort(403, 'Hanya Owner yang berhak membatalkan transaksi.');
        }

        if ($sale->status === 'cancelled') {
            return back()->with('error', 'Transaksi penjualan ini sudah dalam status dibatalkan.');
        }

        DB::transaction(function () use ($sale) {
            // Restore product stock that was deducted during the sale
            foreach ($sale->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);
                $product->increment('stock', $item->quantity);
            }

            $sale->update(['status' => 'cancelled']);
        });

        return back()->with('success', "Transaksi penjualan {$sale->sale_number} berhasil dibatalkan dan seluruh stok dipulihkan.");
    }

    private function generateSaleNumber(): string
    {
        $today = Carbon::now()->format('Ymd');
        $prefix = "INV-{$today}-";

        $lastSale = Sale::where('sale_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSale) {
            $lastNumber = (int) substr($lastSale->sale_number, -4);
            $newNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix.$newNumber;
    }
}
