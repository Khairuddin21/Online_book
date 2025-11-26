<?php

use Illuminate\Support\Facades\Route;

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('home');

// Auth Routes (Placeholder - akan dibuat nanti)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/login', function () {
    // Login logic akan ditambahkan nanti
})->name('login.post');

Route::post('/register', function () {
    // Register logic akan ditambahkan nanti
})->name('register.post');

Route::post('/logout', function () {
    // Logout logic akan ditambahkan nanti
})->name('logout');

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

// User Routes (Placeholder)
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/home', function () {
        return view('user.home');
    })->name('user.home');
    
    Route::get('/books', function () {})->name('user.books');
    Route::get('/categories', function () {})->name('user.categories');
    Route::get('/cart', function () {})->name('user.cart');
    Route::get('/orders', function () {})->name('user.orders');
    Route::get('/profile', function () {})->name('user.profile');
    Route::get('/contact', function () {})->name('user.contact');
});
