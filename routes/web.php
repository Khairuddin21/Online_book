<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

// Halaman Landing
Route::get('/', function () {
    $books = \App\Models\Buku::with('kategori')
        ->orderBy('id_buku', 'desc')
        ->take(8)
        ->get();
    return view('landing', compact('books'));
})->name('home');

// Halaman About - redirect ke bagian about di landing page
Route::get('/about', function () {
    return redirect('/#about');
})->name('about');

// Rute Autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Login pake Google
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/callback/google', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Webhook Notifikasi Midtrans (tanpa auth, CSRF di-skip di middleware)
Route::post('/midtrans/notification', [UserController::class, 'midtransNotification'])->name('midtrans.notification');

// Rute Admin
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // CRUD Kategori
    Route::get('/kategori', [AdminController::class, 'indexKategori'])->name('admin.kategori.index');
    Route::get('/kategori/create', [AdminController::class, 'createKategori'])->name('admin.kategori.create');
    Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('admin.kategori.store');
    Route::get('/kategori/{id}/edit', [AdminController::class, 'editKategori'])->name('admin.kategori.edit');
    Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])->name('admin.kategori.update');
    Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('admin.kategori.destroy');
    
    // CRUD Buku
    Route::get('/buku', [AdminController::class, 'indexBuku'])->name('admin.buku.index');
    Route::get('/buku/create', [AdminController::class, 'createBuku'])->name('admin.buku.create');
    Route::post('/buku', [AdminController::class, 'storeBuku'])->name('admin.buku.store');
    Route::get('/buku/{id}/edit', [AdminController::class, 'editBuku'])->name('admin.buku.edit');
    Route::put('/buku/{id}', [AdminController::class, 'updateBuku'])->name('admin.buku.update');
    Route::delete('/buku/{id}', [AdminController::class, 'destroyBuku'])->name('admin.buku.destroy');
    
    // Pesanan
    Route::get('/pesanan', [AdminController::class, 'indexPesanan'])->name('admin.pesanan.index');
    Route::get('/pesanan/{id}', [AdminController::class, 'showPesanan'])->name('admin.pesanan.show');
    Route::put('/pesanan/{id}/status', [AdminController::class, 'updateStatusPesanan'])->name('admin.pesanan.updateStatus');
    Route::put('/pesanan/{id}/verify-offline', [AdminController::class, 'verifyOffline'])->name('admin.pesanan.verifyOffline');
    Route::post('/pesanan/{id}/offline-payment', [AdminController::class, 'processOfflinePayment'])->name('admin.pesanan.offlinePayment');
    Route::post('/pesanan/{id}/offline-midtrans-callback', [AdminController::class, 'processOfflineMidtransCallback'])->name('admin.pesanan.offlineMidtransCallback');
    Route::delete('/pesanan/{id}', [AdminController::class, 'deletePesanan'])->name('admin.pesanan.delete');
    
    // Pembayaran
    Route::get('/pembayaran', function () {})->name('admin.pembayaran.index');
    
    // Manajemen User
    Route::get('/users', [AdminController::class, 'indexUsers'])->name('admin.users.index');
    Route::post('/users/{id}/update-role', [AdminController::class, 'updateUserRole'])->name('admin.users.updateRole');
    
    // Chat / Pesan
    Route::get('/pesan', [AdminController::class, 'indexPesan'])->name('admin.pesan.index');
    Route::post('/pesan/send', [AdminController::class, 'sendPesan'])->name('admin.pesan.send');
    Route::delete('/pesan/{id}', [AdminController::class, 'deletePesan'])->name('admin.pesan.delete');
    Route::get('/pesan/new-messages', [AdminController::class, 'getNewMessages'])->name('admin.pesan.newMessages');
    
    // Laporan
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('admin.laporan');
    Route::get('/laporan/download', [AdminController::class, 'downloadLaporan'])->name('admin.laporan.download');
});

// Rute User
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/home', [UserController::class, 'home'])->name('user.home');
    Route::get('/books', [UserController::class, 'books'])->name('user.books');
    Route::get('/cart', [UserController::class, 'cart'])->name('user.cart');
    Route::get('/orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('/profile/password', [UserController::class, 'changePassword'])->name('user.profile.password');
    Route::get('/inbox', [UserController::class, 'inbox'])->name('user.inbox');
    Route::post('/inbox/send', [UserController::class, 'sendMessage'])->name('user.inbox.send');
    Route::get('/inbox/new-messages', [UserController::class, 'getNewMessages'])->name('user.inbox.newMessages');
    Route::get('/inbox/check-unread', [UserController::class, 'checkUnreadMessages'])->name('user.inbox.checkUnread');
    Route::delete('/inbox/{id}', [UserController::class, 'deleteInboxMessage'])->name('user.inbox.delete');
    // Detail Buku & yang terkait
    Route::get('/book/{id}', [UserController::class, 'bookDetail'])->name('user.book.detail');
    Route::post('/book/{id}/favorite', [UserController::class, 'toggleFavorite'])->name('user.book.favorite');
    Route::post('/book/{id}/review', [UserController::class, 'submitReview'])->name('user.book.review');
    
    // Rute Checkout & Pembayaran
    Route::get('/checkout', [UserController::class, 'showCheckout'])->name('user.checkout');
    Route::post('/checkout/process', [UserController::class, 'processCheckout'])->name('user.checkout.process');
    Route::get('/payment/{order_id}', [UserController::class, 'showPayment'])->name('user.payment');
    Route::post('/payment/{order_id}/process', [UserController::class, 'processPayment'])->name('user.payment.process');
    Route::post('/orders/{order_id}/cancel', [UserController::class, 'cancelOrder'])->name('user.order.cancel');
    Route::post('/orders/{order_id}/upload-offline', [UserController::class, 'uploadBuktiOffline'])->name('user.order.uploadOffline');
    Route::get('/orders/{order_id}/invoice', [UserController::class, 'downloadInvoice'])->name('user.order.invoice');
    
    // Rute Manajemen Alamat
    Route::post('/address/store', [UserController::class, 'storeAddress'])->name('user.address.store');
    Route::post('/address/update/{id}', [UserController::class, 'updateAddress'])->name('user.address.update');
    Route::delete('/address/delete/{id}', [UserController::class, 'deleteAddress'])->name('user.address.delete');
    
    // Rute API Keranjang
    Route::post('/api/cart/add', [UserController::class, 'addToCart'])->name('api.cart.add');
    Route::get('/api/cart/count', [UserController::class, 'getCartCount'])->name('api.cart.count');
    Route::post('/api/cart/update', [UserController::class, 'updateCart'])->name('api.cart.update');
    Route::delete('/api/cart/delete/{id}', [UserController::class, 'removeFromCart'])->name('api.cart.delete');
});
