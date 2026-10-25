<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Semua route di bawah ini wajib login
Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Menu Katalog Pelanggan & Storefront
    Route::get('/menu', [StorefrontController::class, 'index'])->name('storefront.index');
    Route::get('/menu-katalog', [StorefrontController::class, 'index'])->name('menu.index');
    
    // Memperbaiki route checkout agar mendukung akses GET (form) dan POST (proses)
    Route::match(['get', 'post'], '/menu/checkout', [StorefrontController::class, 'checkout'])->name('storefront.checkout');
    Route::post('/menu/order', [StorefrontController::class, 'storeOrder'])->name('storefront.order');
    Route::get('/menu/receipt/{sale}', [StorefrontController::class, 'receipt'])->name('storefront.receipt');

    // Produk: daftar & stok rendah
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');

    // Penjualan (Sales)
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');

    // Pelanggan / Customers
    Route::resource('customers', CustomerController::class);

    // Master data: hanya Admin / Owner
    Route::middleware('can:manage-master')->group(function () {
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
    });

    // Detail produk
    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->whereNumber('product')
        ->name('products.show');

    // Pengguna / Administrasi
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});