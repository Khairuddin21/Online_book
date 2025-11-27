<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Landing Page
Route::get('/', function () {
    return view('landing');
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

// Admin Routes (Placeholder)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Kategori
    Route::get('/kategori', function () {})->name('admin.kategori.index');
    
    // Buku
    Route::get('/buku', function () {})->name('admin.buku.index');
    
    // Pesanan
    Route::get('/pesanan', function () {})->name('admin.pesanan.index');
    Route::get('/pesanan/{id}', function () {})->name('admin.pesanan.show');
    
    // Pembayaran
    Route::get('/pembayaran', function () {})->name('admin.pembayaran.index');
    
    // Users
    Route::get('/users', function () {})->name('admin.users.index');
    
    // Pesan
    Route::get('/pesan', function () {})->name('admin.pesan.index');
    
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
    Route::get('/contact', [UserController::class, 'contact'])->name('user.contact');
    
    // Cart API Routes
    Route::post('/api/cart/add', [UserController::class, 'addToCart'])->name('api.cart.add');
    Route::get('/api/cart/count', [UserController::class, 'getCartCount'])->name('api.cart.count');
    Route::post('/api/cart/update', [UserController::class, 'updateCart'])->name('api.cart.update');
    Route::delete('/api/cart/delete/{id}', [UserController::class, 'removeFromCart'])->name('api.cart.delete');
});
