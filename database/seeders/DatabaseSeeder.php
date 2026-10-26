<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with demo accounts, master data, and initial transactions.
     */
    public function run(): void
    {
        // 1. Users for 3 roles: Owner, Admin (Staff), and User (Customer)
        $owner = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Hendra Wijaya (Owner)',
                'phone' => '081234567890',
                'role' => User::ROLE_OWNER,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Siti Rahmah (Staff Operasional)',
                'phone' => '081298765432',
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Budi Santoso (Pelanggan Grosir)',
                'phone' => '081377889900',
                'role' => User::ROLE_USER,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Categories
        $categoriesData = [
            ['name' => 'Elektronik & Komputer', 'description' => 'Aksesoris komputer, peripheral, dan media penyimpanan.'],
            ['name' => 'Makanan & Minuman', 'description' => 'Bahan pangan kering, kopi kemasan, dan minuman kemasan.'],
            ['name' => 'Alat Tulis Kantor', 'description' => 'Kertas, perlengkapan arsip, pulpen, dan alat tulis instansi.'],
            ['name' => 'Perlengkapan Toko', 'description' => 'Kertas struk thermal, barcode ribbon, dan kebutuhan kasir.'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // 3. Suppliers
        $suppliersData = [
            [
                'name' => 'PT Sumber Makmur Distribusi',
                'email' => 'contact@sumbermakmur.co.id',
                'phone' => '021-5551234',
                'address' => 'Kawasan Industri Pulo Gadung Blok B No. 12, Jakarta Timur',
            ],
            [
                'name' => 'CV Berkah Jaya Logistik',
                'email' => 'order@berkahjaya.com',
                'phone' => '031-8889911',
                'address' => 'Jl. Raya Darmo No. 45, Surabaya',
            ],
            [
                'name' => 'PT Global Mega Supplier',
                'email' => 'sales@globalsupplier.id',
                'phone' => '022-7772345',
                'address' => 'Jl. Soekarno Hatta No. 88, Bandung',
            ],
        ];

        $suppliers = [];
        foreach ($suppliersData as $sup) {
            $suppliers[] = Supplier::firstOrCreate(['name' => $sup['name']], $sup);
        }

        // 4. Products
        $productsData = [
            [
                'category_id' => $categories['Elektronik & Komputer']->id,
                'sku' => 'PRD-ELC-001',
                'name' => 'Mouse Wireless Logitech M220 Silent',
                'purchase_price' => 110000,
                'selling_price' => 155000,
                'stock' => 35,
                'minimum_stock' => 10,
                'description' => 'Mouse nirkabel dengan tombol senyap dan daya tahan baterai 18 bulan.',
            ],
            [
                'category_id' => $categories['Elektronik & Komputer']->id,
                'sku' => 'PRD-ELC-002',
                'name' => 'Keyboard Mechanical RGB Red Switch',
                'purchase_price' => 280000,
                'selling_price' => 395000,
                'stock' => 18,
                'minimum_stock' => 5,
                'description' => 'Keyboard mechanical tenkeyless dengan backlight RGB dan switch responsif.',
            ],
            [
                'category_id' => $categories['Elektronik & Komputer']->id,
                'sku' => 'PRD-ELC-003',
                'name' => 'Flashdisk SanDisk Ultra 64GB USB 3.0',
                'purchase_price' => 65000,
                'selling_price' => 95000,
                'stock' => 4,
                'minimum_stock' => 10, // LOW STOCK
                'description' => 'Transfer data berkecepatan tinggi hingga 100MB/detik.',
            ],
            [
                'category_id' => $categories['Elektronik & Komputer']->id,
                'sku' => 'PRD-ELC-004',
                'name' => 'Headset Gaming Stereo Bass 3.5mm',
                'purchase_price' => 145000,
                'selling_price' => 210000,
                'stock' => 2,
                'minimum_stock' => 8, // LOW STOCK
                'description' => 'Kenyamanan bantalan telinga busa memori dengan mikrofon noise-cancelling.',
            ],
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-FNB-001',
                'name' => 'Kopi Arabika Gayo Single Origin 250g',
                'purchase_price' => 38000,
                'selling_price' => 55000,
                'stock' => 50,
                'minimum_stock' => 15,
                'description' => 'Biji kopi sangrai medium roasts dengan aroma buah segar dan rempah.',
            ],
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-FNB-002',
                'name' => 'Teh Melati Premium Box isi 100 Tea Bags',
                'purchase_price' => 22000,
                'selling_price' => 32500,
                'stock' => 42,
                'minimum_stock' => 10,
                'description' => 'Kantong teh celup aroma melati murni standar kafe dan restoran.',
            ],
            [
                'category_id' => $categories['Alat Tulis Kantor']->id,
                'sku' => 'PRD-ATK-001',
                'name' => 'Kertas HVS PaperOne A4 80gr (Rim 500 Lembar)',
                'purchase_price' => 44000,
                'selling_price' => 58000,
                'stock' => 120,
                'minimum_stock' => 25,
                'description' => 'Kertas fotokopi & print putih cerah bebas jam berkualitas tinggi.',
            ],
            [
                'category_id' => $categories['Alat Tulis Kantor']->id,
                'sku' => 'PRD-ATK-002',
                'name' => 'Pulpen Gel Hitam 0.5mm Box isi 12 Pcs',
                'purchase_price' => 28000,
                'selling_price' => 42000,
                'stock' => 55,
                'minimum_stock' => 15,
                'description' => 'Tinta gel hitam pekat anti blobor untuk dokumen resmi.',
            ],
            [
                'category_id' => $categories['Perlengkapan Toko']->id,
                'sku' => 'PRD-TKO-001',
                'name' => 'Kertas Kasir Thermal Roll 80 x 80 mm',
                'purchase_price' => 12000,
                'selling_price' => 18500,
                'stock' => 3,
                'minimum_stock' => 20, // LOW STOCK
                'description' => 'Kertas struk kasir POS tebal, hasil cetak tajam dan tahan lama.',
            ],
        ];

        $products = [];
        foreach ($productsData as $pData) {
            $products[$pData['sku']] = Product::firstOrCreate(['sku' => $pData['sku']], $pData);
        }

        // 5. Initial Purchase Transactions (with items)
        $purchase1 = Purchase::firstOrCreate(
            ['purchase_number' => 'PO-20261001-0001'],
            [
                'supplier_id' => $suppliers[0]->id,
                'user_id' => $admin->id,
                'total' => 2200000,
                'status' => 'completed',
                'notes' => 'Pengadaan stok awal mouse dan flashdisk bulan Oktober.',
                'purchased_at' => Carbon::now()->subDays(15),
            ]
        );

        if ($purchase1->wasRecentlyCreated) {
            PurchaseItem::create([
                'purchase_id' => $purchase1->id,
                'product_id' => $products['PRD-ELC-001']->id,
                'quantity' => 15,
                'unit_cost' => 110000,
                'subtotal' => 1650000,
            ]);

            PurchaseItem::create([
                'purchase_id' => $purchase1->id,
                'product_id' => $products['PRD-ELC-003']->id,
                'quantity' => 8,
                'unit_cost' => 65000,
                'subtotal' => 520000,
            ]);
        }

        $purchase2 = Purchase::firstOrCreate(
            ['purchase_number' => 'PO-20261010-0002'],
            [
                'supplier_id' => $suppliers[1]->id,
                'user_id' => $admin->id,
                'total' => 1760000,
                'status' => 'completed',
                'notes' => 'Restock kertas HVS PaperOne untuk kebutuhan kantor.',
                'purchased_at' => Carbon::now()->subDays(7),
            ]
        );

        if ($purchase2->wasRecentlyCreated) {
            PurchaseItem::create([
                'purchase_id' => $purchase2->id,
                'product_id' => $products['PRD-ATK-001']->id,
                'quantity' => 40,
                'unit_cost' => 44000,
                'subtotal' => 1760000,
            ]);
        }

        // 6. Initial Sales Transactions (with items)
        $sale1 = Sale::firstOrCreate(
            ['sale_number' => 'INV-20261015-0001'],
            [
                'user_id' => $admin->id,
                'customer_name' => 'Bapak Slamet (Toko Sentosa)',
                'total' => 615000,
                'status' => 'completed',
                'notes' => 'Pembelian langsung kasir secara tunai.',
                'sold_at' => Carbon::now()->subDays(5),
            ]
        );

        if ($sale1->wasRecentlyCreated) {
            SaleItem::create([
                'sale_id' => $sale1->id,
                'product_id' => $products['PRD-ELC-001']->id,
                'quantity' => 2,
                'unit_price' => 155000,
                'subtotal' => 310000,
            ]);

            SaleItem::create([
                'sale_id' => $sale1->id,
                'product_id' => $products['PRD-FNB-001']->id,
                'quantity' => 3,
                'unit_price' => 55000,
                'subtotal' => 165000,
            ]);

            SaleItem::create([
                'sale_id' => $sale1->id,
                'product_id' => $products['PRD-ELC-004']->id,
                'quantity' => 1,
                'unit_price' => 210000,
                'subtotal' => 210000,
            ]);
        }

        $sale2 = Sale::firstOrCreate(
            ['sale_number' => 'INV-20261020-0002'],
            [
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'total' => 395000,
                'status' => 'completed',
                'notes' => 'Order online mandiri oleh customer grosir.',
                'sold_at' => Carbon::now()->subDays(2),
            ]
        );

        if ($sale2->wasRecentlyCreated) {
            SaleItem::create([
                'sale_id' => $sale2->id,
                'product_id' => $products['PRD-ELC-002']->id,
                'quantity' => 1,
                'unit_price' => 395000,
                'subtotal' => 395000,
            ]);
        }
    }
}
