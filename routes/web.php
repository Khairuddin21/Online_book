<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

// Landing Page
Route::get('/', function () {
    $books = \App\Models\Buku::with('kategori')
        ->orderBy('id_buku', 'desc')
        ->take(8)
        ->get();
    return view('landing', compact('books'));
})->name('home');

// About Page
Route::get('/about', function () {
    return view('about');
})->name('about');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Kategori CRUD
    Route::get('/kategori', [AdminController::class, 'indexKategori'])->name('admin.kategori.index');
    Route::get('/kategori/create', [AdminController::class, 'createKategori'])->name('admin.kategori.create');
    Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('admin.kategori.store');
    Route::get('/kategori/{id}/edit', [AdminController::class, 'editKategori'])->name('admin.kategori.edit');
    Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])->name('admin.kategori.update');
    Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('admin.kategori.destroy');
    
    // Buku CRUD
    Route::get('/buku', [AdminController::class, 'indexBuku'])->name('admin.buku.index');
    Route::get('/buku/create', [AdminController::class, 'createBuku'])->name('admin.buku.create');
    Route::post('/buku', [AdminController::class, 'storeBuku'])->name('admin.buku.store');
    Route::get('/buku/{id}/edit', [AdminController::class, 'editBuku'])->name('admin.buku.edit');
    Route::put('/buku/{id}', [AdminController::class, 'updateBuku'])->name('admin.buku.update');
    Route::delete('/buku/{id}', [AdminController::class, 'destroyBuku'])->name('admin.buku.destroy');
    
    // Pesanan
    Route::get('/pesanan', function () {})->name('admin.pesanan.index');
    Route::get('/pesanan/{id}', [AdminController::class, 'showPesanan'])->name('admin.pesanan.show');
    Route::delete('/pesanan/{id}', [AdminController::class, 'deletePesanan'])->name('admin.pesanan.delete');
    
    // Pembayaran
    Route::get('/pembayaran', function () {})->name('admin.pembayaran.index');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'indexUsers'])->name('admin.users.index');
    Route::post('/users/{id}/update-role', [AdminController::class, 'updateUserRole'])->name('admin.users.updateRole');
    
    // Pesan Kontak
    Route::get('/pesan', [AdminController::class, 'indexPesan'])->name('admin.pesan.index');
    Route::get('/pesan/{id}', [AdminController::class, 'showPesan'])->name('admin.pesan.show');
    Route::post('/pesan/{id}/reply', [AdminController::class, 'replyPesan'])->name('admin.pesan.reply');
    Route::delete('/pesan/{id}', [AdminController::class, 'deletePesan'])->name('admin.pesan.delete');
    
    // Laporan
    Route::get('/laporan', function () {})->name('admin.laporan');
});

// User Routes
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/home', [UserController::class, 'home'])->name('user.home');
    Route::get('/books', [UserController::class, 'books'])->name('user.books');
    Route::get('/categories', [UserController::class, 'categories'])->name('user.categories');
    Route::get('/cart', [UserController::class, 'cart'])->name('user.cart');
    Route::get('/orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::get('/inbox', [UserController::class, 'inbox'])->name('user.inbox');
    Route::get('/contact', [UserController::class, 'contact'])->name('user.contact');
    Route::post('/contact/submit', [UserController::class, 'submitContact'])->name('user.contact.submit');
    
    // Checkout & Payment Routes
    Route::get('/checkout', [UserController::class, 'showCheckout'])->name('user.checkout');
    Route::post('/checkout/process', [UserController::class, 'processCheckout'])->name('user.checkout.process');
    Route::get('/payment/{order_id}', [UserController::class, 'showPayment'])->name('user.payment');
    Route::post('/payment/{order_id}/process', [UserController::class, 'processPayment'])->name('user.payment.process');
    
    // Address Management Routes
    Route::post('/address/store', [UserController::class, 'storeAddress'])->name('user.address.store');
    Route::post('/address/update/{id}', [UserController::class, 'updateAddress'])->name('user.address.update');
    Route::delete('/address/delete/{id}', [UserController::class, 'deleteAddress'])->name('user.address.delete');
    
    // Cart API Routes
    Route::post('/api/cart/add', [UserController::class, 'addToCart'])->name('api.cart.add');
    Route::get('/api/cart/count', [UserController::class, 'getCartCount'])->name('api.cart.count');
    Route::post('/api/cart/update', [UserController::class, 'updateCart'])->name('api.cart.update');
    Route::delete('/api/cart/delete/{id}', [UserController::class, 'removeFromCart'])->name('api.cart.delete');
});
