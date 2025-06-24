<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ConversionController::class, 'index'])->name('dashboard');
    
    // Conversion Routes (Jual PayPal/Skrill)
    Route::post('/conversion/get-rate', [ConversionController::class, 'getRate'])->name('conversion.rate');
    Route::post('/conversion', [ConversionController::class, 'store'])->name('conversion.store');
    Route::get('/conversion/{orderCode}', [ConversionController::class, 'show'])->name('conversion.show');
    Route::post('/conversion/{orderCode}/upload-proof', [ConversionController::class, 'uploadProof'])->name('conversion.upload-proof');
    
    // Purchase Routes (Beli PayPal/Skrill)
    Route::post('/purchase/get-rate', [PurchaseController::class, 'getRate'])->name('purchase.rate');
    Route::post('/purchase', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/{orderCode}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{orderCode}/upload-proof', [PurchaseController::class, 'uploadProof'])->name('purchase.upload-proof');
    
    // General Transaction Route - TAMBAHAN BARU
    Route::get('/transaction/{orderCode}', function($orderCode) {
        // Cek di conversion orders dulu (jual PayPal/Skrill)
        $conversionOrder = \App\Models\ConversionOrder::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->first();
            
        if ($conversionOrder) {
            return redirect()->route('conversion.show', $orderCode);
        }
        
        // Cek di purchase orders (beli PayPal/Skrill)
        $purchaseOrder = \App\Models\PurchaseOrder::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->first();
            
        if ($purchaseOrder) {
            return redirect()->route('purchase.show', $orderCode);
        }
        
        // Jika tidak ditemukan di kedua tabel
        abort(404, 'Transaksi tidak ditemukan atau Anda tidak memiliki akses ke transaksi ini.');
    })->name('transaction.show');
});

// Admin Routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Orders Management (Conversion - Jual)
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::put('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::post('/orders/{id}/upload-proof', [AdminController::class, 'uploadProof'])->name('orders.upload-proof');
    Route::delete('/orders/{id}', [AdminController::class, 'destroyOrder'])->name('orders.destroy');
    
    // Purchase Orders Management (Beli)
    Route::get('/purchase-orders', [AdminController::class, 'purchaseOrders'])->name('purchase-orders');
    Route::get('/purchase-orders/{id}', [AdminController::class, 'showPurchaseOrder'])->name('purchase-orders.show');
    Route::put('/purchase-orders/{id}/status', [AdminController::class, 'updatePurchaseOrderStatus'])->name('purchase-orders.update-status');
    Route::post('/purchase-orders/{id}/upload-proof', [AdminController::class, 'uploadPurchaseProof'])->name('purchase-orders.upload-proof');
    Route::delete('/purchase-orders/{id}', [AdminController::class, 'destroyPurchaseOrder'])->name('purchase-orders.destroy');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users.show');
    Route::put('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::put('/users/{id}/make-admin', [AdminController::class, 'makeAdmin'])->name('users.make-admin');
    
    // Exchange Rates Management
    Route::get('/rates', [AdminController::class, 'rates'])->name('rates');
    Route::post('/rates', [AdminController::class, 'storeRate'])->name('rates.store');
    Route::put('/rates/{id}', [AdminController::class, 'updateRate'])->name('rates.update');
    Route::put('/rates/{id}/toggle', [AdminController::class, 'toggleRate'])->name('rates.toggle');
    Route::delete('/rates/{id}', [AdminController::class, 'destroyRate'])->name('rates.destroy');
    
    // Payment Methods Management
    Route::post('/payment-methods', [AdminController::class, 'storePaymentMethod'])->name('payment-methods.store');
    Route::put('/payment-methods/{id}/toggle', [AdminController::class, 'togglePaymentMethod'])->name('payment-methods.toggle');
    Route::delete('/payment-methods/{id}', [AdminController::class, 'destroyPaymentMethod'])->name('payment-methods.destroy');
    
    // Admin Accounts Management
    Route::get('/accounts', [AdminController::class, 'accounts'])->name('accounts');
    Route::post('/accounts', [AdminController::class, 'storeAccount'])->name('accounts.store');
    Route::put('/accounts/{id}/toggle', [AdminController::class, 'toggleAccount'])->name('accounts.toggle');
    Route::delete('/accounts/{id}', [AdminController::class, 'destroyAccount'])->name('accounts.destroy');
    
    // Balance Orders Management
    Route::get('/balance-orders', [AdminController::class, 'balanceOrders'])->name('balance-orders');
    
    // Admin Transaction Route - TAMBAHAN BARU
    Route::get('/transaction/{orderCode}', function($orderCode) {
        // Admin bisa lihat semua transaksi
        $conversionOrder = \App\Models\ConversionOrder::where('order_code', $orderCode)->first();
        
        if ($conversionOrder) {
            return redirect()->route('admin.orders.show', $conversionOrder->id);
        }
        
        $purchaseOrder = \App\Models\PurchaseOrder::where('order_code', $orderCode)->first();
        
        if ($purchaseOrder) {
            return redirect()->route('admin.purchase-orders.show', $purchaseOrder->id);
        }
        
        abort(404, 'Transaksi tidak ditemukan.');
    })->name('transaction.show');
});
