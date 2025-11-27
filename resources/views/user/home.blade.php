@extends('user.layout')

@section('title', 'Beranda')

@section('content')
@if(session('success'))
<div class="alert alert-success" style="margin: 20px auto; max-width: 1200px; padding: 15px 20px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
    <i class="fas fa-check-circle" style="font-size: 20px;"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Hero Section -->
<section class="hero-section">
    <h2>Selamat Datang di Toko Buku Online</h2>
    <p>Temukan ribuan koleksi buku dari berbagai kategori dengan harga terbaik</p>
    <a href="{{ route('user.books') }}" class="btn btn-accent" style="font-size: 18px; padding: 15px 40px;">
        <i class="fas fa-book"></i> Jelajahi Koleksi
    </a>
</section>

<!-- Categories Section -->
<div class="user-container">
    <section class="categories-section">
        <h2 class="section-title">Kategori Populer</h2>
        <div class="categories-grid">
            @forelse($categories ?? [] as $category)
            <div class="category-card">
                <i class="fas fa-book"></i>
                <h3>{{ $category->nama_kategori }}</h3>
                <p>{{ $category->buku->count() }} Buku</p>
            </div>
            @empty
            <div class="category-card">
                <i class="fas fa-book"></i>
                <h3>Fiksi</h3>
            </div>
            <div class="category-card">
                <i class="fas fa-graduation-cap"></i>
                <h3>Pendidikan</h3>
            </div>
            <div class="category-card">
                <i class="fas fa-heart"></i>
                <h3>Romance</h3>
            </div>
            <div class="category-card">
                <i class="fas fa-rocket"></i>
                <h3>Sci-Fi</h3>
            </div>
            @endforelse
        </div>
    </section>

    <!-- Books Section -->
    <section class="books-section">
        <h2 class="section-title">Buku Terbaru</h2>
        <div class="books-grid">
            @forelse($books ?? [] as $book)
            <div class="book-card">
                <img src="{{ $book->cover ? asset('storage/' . $book->cover) : 'https://via.placeholder.com/220x300?text=No+Cover' }}" 
                     alt="{{ $book->judul }}" 
                     class="book-cover">
                <div class="book-info">
                    <h3 class="book-title">{{ $book->judul }}</h3>
                    <p class="book-author">{{ $book->penulis }}</p>
                    <div class="book-price">Rp {{ number_format($book->harga, 0, ',', '.') }}</div>
                    <div class="book-actions">
                        <button class="btn btn-primary btn-block add-to-cart" data-book-id="{{ $book->id_buku }}">
                            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <p style="grid-column: 1/-1; text-align: center; color: #999;">
                Belum ada buku tersedia
            </p>
            @endforelse
        </div>
    </section>
</div>
@endsection
