<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->owner = User::create([
        'name' => 'Owner Test',
        'email' => 'owner.test@example.com',
        'role' => User::ROLE_OWNER,
        'password' => Hash::make('secret123'),
    ]);

    $this->staff = User::create([
        'name' => 'Staff Test',
        'email' => 'staff.test@example.com',
        'role' => User::ROLE_ADMIN,
        'password' => Hash::make('secret123'),
    ]);

    $this->customer = User::create([
        'name' => 'Customer Test',
        'email' => 'customer.test@example.com',
        'role' => User::ROLE_USER,
        'password' => Hash::make('secret123'),
    ]);

    $this->category = Category::create([
        'name' => 'Kategori Uji',
        'description' => 'Untuk unit testing',
    ]);

    $this->supplier = Supplier::create([
        'name' => 'Supplier Uji',
        'email' => 'supplier@test.com',
        'phone' => '08123456789',
        'address' => 'Gudang Pusat Testing',
    ]);
});

/*
|--------------------------------------------------------------------------
| Skenario 1: Autentikasi (Slide 19)
| Guest tidak dapat membuka dashboard; password salah ditolak.
|--------------------------------------------------------------------------
*/
test('guest tidak dapat membuka dashboard dan diarahkan ke login', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('login dengan password salah ditolak', function () {
    $response = $this->post('/login', [
        'email' => $this->owner->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('login dengan kredensial benar berhasil masuk', function () {
    $response = $this->post('/login', [
        'email' => $this->owner->email,
        'password' => 'secret123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($this->owner);
});

/*
|--------------------------------------------------------------------------
| Skenario 2: Validasi Produk (Slide 19)
| SKU duplikat ditolak; harga negatif ditolak.
|--------------------------------------------------------------------------
*/
test('produk dengan SKU duplikat ditolak oleh sistem', function () {
    Product::create([
        'category_id' => $this->category->id,
        'sku' => 'SKU-TEST-001',
        'name' => 'Produk Asli',
        'purchase_price' => 10000,
        'selling_price' => 15000,
        'stock' => 10,
        'minimum_stock' => 2,
    ]);

    $response = $this->actingAs($this->owner)->post('/products', [
        'category_id' => $this->category->id,
        'sku' => 'SKU-TEST-001', // Duplikat
        'name' => 'Produk Kembar',
        'purchase_price' => 12000,
        'selling_price' => 18000,
        'stock' => 5,
        'minimum_stock' => 2,
    ]);

    $response->assertSessionHasErrors('sku');
    expect(Product::where('sku', 'SKU-TEST-001')->count())->toBe(1);
});

test('produk dengan harga beli atau harga jual negatif ditolak', function () {
    $response = $this->actingAs($this->owner)->post('/products', [
        'category_id' => $this->category->id,
        'sku' => 'SKU-NEG-001',
        'name' => 'Produk Minus',
        'purchase_price' => -5000, // Nilai negatif
        'selling_price' => -10000, // Nilai negatif
        'stock' => 10,
        'minimum_stock' => 2,
    ]);

    $response->assertSessionHasErrors(['purchase_price', 'selling_price']);
});

/*
|--------------------------------------------------------------------------
| Skenario 3: Pembelian / Stock-In (Slide 19)
| Pembelian sukses menambah stok sesuai kuantitas.
|--------------------------------------------------------------------------
*/
test('transaksi pembelian sukses menambah stok produk secara otomatis', function () {
    $product = Product::create([
        'category_id' => $this->category->id,
        'sku' => 'SKU-BUY-001',
        'name' => 'Barang Masuk',
        'purchase_price' => 20000,
        'selling_price' => 30000,
        'stock' => 10,
        'minimum_stock' => 2,
    ]);

    $response = $this->actingAs($this->staff)->post('/purchases', [
        'supplier_id' => $this->supplier->id,
        'purchased_at' => now()->format('Y-m-d H:i:s'),
        'notes' => 'Restock awal',
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 15,
                'unit_cost' => 20000,
            ],
        ],
    ]);

    $response->assertSessionHasNoErrors();
    $product->refresh();
    expect($product->stock)->toBe(25); // 10 + 15 = 25
    expect(Purchase::count())->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Skenario 4: Penjualan / Stock-Out (Slide 19)
| Penjualan sukses mengurangi stok sesuai kuantitas.
|--------------------------------------------------------------------------
*/
test('transaksi penjualan sukses mengurangi stok produk secara atomik', function () {
    $product = Product::create([
        'category_id' => $this->category->id,
        'sku' => 'SKU-SALE-001',
        'name' => 'Barang Keluar',
        'purchase_price' => 20000,
        'selling_price' => 35000,
        'stock' => 20,
        'minimum_stock' => 5,
    ]);

    $response = $this->actingAs($this->staff)->post('/sales', [
        'customer_name' => 'Toko Barokah',
        'sold_at' => now()->format('Y-m-d H:i:s'),
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 7,
            ],
        ],
    ]);

    $response->assertSessionHasNoErrors();
    $product->refresh();
    expect($product->stock)->toBe(13); // 20 - 7 = 13
    expect(Sale::count())->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Skenario 5: Insufficient Stock (Slide 19)
| Penjualan dengan stok tidak cukup ditolak tanpa perubahan data.
|--------------------------------------------------------------------------
*/
test('penjualan ditolak jika stok barang tidak mencukupi', function () {
    $product = Product::create([
        'category_id' => $this->category->id,
        'sku' => 'SKU-LIMIT-001',
        'name' => 'Barang Terbatas',
        'purchase_price' => 10000,
        'selling_price' => 15000,
        'stock' => 4, // Hanya ada 4
        'minimum_stock' => 1,
    ]);

    $response = $this->actingAs($this->staff)->post('/sales', [
        'customer_name' => 'Pembeli Borongan',
        'sold_at' => now()->format('Y-m-d H:i:s'),
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 10, // Melebihi stok (stok 4 diminta 10)
            ],
        ],
    ]);

    $response->assertSessionHasErrors('items');
    $product->refresh();
    expect($product->stock)->toBe(4); // Stok tidak berubah sama sekali
    expect(Sale::count())->toBe(0); // Transaksi tidak dibuat
});

/*
|--------------------------------------------------------------------------
| Skenario 6: Atomicity & Rollback (Slide 19)
| Jika satu item transaksi gagal, seluruh transaksi rollback.
|--------------------------------------------------------------------------
*/
test('transaksi multi-item rollback penuh jika ada satu item yang stoknya kurang', function () {
    $productA = Product::create([
        'category_id' => $this->category->id,
        'sku' => 'SKU-A-001',
        'name' => 'Barang A Cukup',
        'purchase_price' => 10000,
        'selling_price' => 15000,
        'stock' => 50,
        'minimum_stock' => 5,
    ]);

    $productB = Product::create([
        'category_id' => $this->category->id,
        'sku' => 'SKU-B-001',
        'name' => 'Barang B Kurang',
        'purchase_price' => 20000,
        'selling_price' => 30000,
        'stock' => 2, // Hanya 2
        'minimum_stock' => 1,
    ]);

    $response = $this->actingAs($this->staff)->post('/sales', [
        'customer_name' => 'Pak Ahmad',
        'sold_at' => now()->format('Y-m-d H:i:s'),
        'items' => [
            [
                'product_id' => $productA->id,
                'quantity' => 5, // Ini valid
            ],
            [
                'product_id' => $productB->id,
                'quantity' => 10, // Ini tidak valid (minta 10 padahal cuma ada 2)
            ],
        ],
    ]);

    $response->assertSessionHasErrors('items');
    $productA->refresh();
    $productB->refresh();

    // Rollback guarantees: product A's stock is untouched, no sale is saved
    expect($productA->stock)->toBe(50);
    expect($productB->stock)->toBe(2);
    expect(Sale::count())->toBe(0);
});

/*
|--------------------------------------------------------------------------
| Skenario 7: Otorisasi & Pembatasan Akses 3 Peran (Slide 19)
| Staff tidak dapat membuka fungsi Owner; User tidak dapat membuka backoffice.
|--------------------------------------------------------------------------
*/
test('staff dilarang mengakses halaman kelola pengguna milik owner', function () {
    $response = $this->actingAs($this->staff)->get('/users');
    $response->assertStatus(403);
});

test('customer/user dilarang membuka panel dashboard backoffice', function () {
    $response = $this->actingAs($this->customer)->get('/dashboard');
    $response->assertStatus(403);
});

test('hanya owner yang dapat membatalkan transaksi penjualan dan memulihkan stok', function () {
    $product = Product::create([
        'category_id' => $this->category->id,
        'sku' => 'SKU-REV-001',
        'name' => 'Barang Retur',
        'purchase_price' => 10000,
        'selling_price' => 15000,
        'stock' => 10,
        'minimum_stock' => 2,
    ]);

    $this->actingAs($this->staff)->post('/sales', [
        'customer_name' => 'Pelanggan Retur',
        'sold_at' => now()->format('Y-m-d H:i:s'),
        'items' => [
            ['product_id' => $product->id, 'quantity' => 3],
        ],
    ]);

    $product->refresh();
    expect($product->stock)->toBe(7);

    $sale = Sale::first();

    // Staff tries to cancel -> Forbidden
    $staffCancelResponse = $this->actingAs($this->staff)->post("/sales/{$sale->id}/cancel");
    $staffCancelResponse->assertStatus(403);

    // Owner cancels -> Allowed & stock restored
    $ownerCancelResponse = $this->actingAs($this->owner)->post("/sales/{$sale->id}/cancel");
    $ownerCancelResponse->assertSessionHas('success');

    $product->refresh();
    $sale->refresh();

    expect($product->stock)->toBe(10); // Restored!
    expect($sale->status)->toBe('cancelled');
});

/*
|--------------------------------------------------------------------------
| Skenario 8: Pencarian, Filter & Paginasi (Slide 19)
| Filter dan pagination menghasilkan data yang sesuai.
|--------------------------------------------------------------------------
*/
test('fitur pencarian dan filter produk berfungsi akurat', function () {
    Product::create([
        'category_id' => $this->category->id,
        'sku' => 'SEARCH-ABC',
        'name' => 'Kopi Robusta Lampung',
        'purchase_price' => 25000,
        'selling_price' => 35000,
        'stock' => 30,
        'minimum_stock' => 5,
    ]);

    Product::create([
        'category_id' => $this->category->id,
        'sku' => 'SEARCH-XYZ',
        'name' => 'Mouse Gaming Laser',
        'purchase_price' => 150000,
        'selling_price' => 210000,
        'stock' => 2,
        'minimum_stock' => 5, // Low stock
    ]);

    // Search by name
    $response = $this->actingAs($this->owner)->get('/products?search=Robusta');
    $response->assertSee('Kopi Robusta Lampung');
    $response->assertDontSee('Mouse Gaming Laser');

    // Filter by low stock
    $responseLow = $this->actingAs($this->owner)->get('/products?stock_status=low');
    $responseLow->assertSee('Mouse Gaming Laser');
    $responseLow->assertDontSee('Kopi Robusta Lampung');
});
