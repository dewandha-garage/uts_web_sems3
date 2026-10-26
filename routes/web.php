<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole(['owner', 'admin'])) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('portal.catalog');
    }

    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Customer Portal Routes (Available to all logged-in users, especially role: 'user')
Route::middleware('auth')->prefix('portal')->name('portal.')->group(function () {
    Route::get('/catalog', [CustomerPortalController::class, 'catalog'])->name('catalog');
    Route::post('/order', [CustomerPortalController::class, 'order'])->name('order');
    Route::get('/orders', [CustomerPortalController::class, 'orders'])->name('orders');
    Route::get('/orders/{sale}', [CustomerPortalController::class, 'orderDetail'])->name('orderDetail');
});

// Back-office Management (Only accessible by 'owner' and 'admin')
Route::middleware(['auth', 'role:owner,admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data Resources
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('products', ProductController::class);

    // Purchases (Stock-in)
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])
        ->name('purchases.cancel')
        ->middleware('can:cancel-transaction');

    // Sales (Stock-out / POS)
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])
        ->name('sales.cancel')
        ->middleware('can:cancel-transaction');
});

// Owner Only: User and Role Management
Route::middleware(['auth', 'role:owner'])->group(function () {
    Route::resource('users', UserController::class);
});
