@extends('user.layout')

@section('title', 'Katalog Buku')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <div class="page-header">
        <h1 class="page-title">Katalog Buku</h1>
        <p class="page-subtitle">Temukan buku favoritmu dari koleksi terlengkap kami</p>
    </div>

    <!-- Pencarian & Filter -->
    <div class="catalog-controls">
        <form method="GET" action="{{ route('user.books') }}" class="catalog-search">
            <input type="text" 
                   name="search" 
                   placeholder="Cari buku, penulis, penerbit..." 
                   value="{{ request('search') }}">
            
            <select name="kategori">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                <option value="{{ $category->id_kategori }}" {{ request('kategori') == $category->id_kategori ? 'selected' : '' }}>
                    {{ $category->nama_kategori }}
                </option>
                @endforeach
            </select>
            
            <button type="submit" class="btn btn-green btn-sm">
                <i class="fas fa-search"></i> Cari
            </button>
            
            @if(request('search') || request('kategori'))
            <a href="{{ route('user.books') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-times"></i> Reset
            </a>
            @endif
        </form>
    </div>

    @if(request('search'))
    <p class="search-result-text">
        Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
    </p>
    @endif

    <!-- Grid Daftar Buku -->
    <section class="books-section">
        <div class="books-grid">
            @forelse($books as $book)
            <a href="{{ route('user.book.detail', $book->id_buku) }}" class="book-card book-card-link">
                <div class="book-cover-wrapper">
                    <img src="{{ $book->cover ?: 'https://via.placeholder.com/220x300?text=No+Cover' }}" 
                         alt="{{ $book->judul }}" 
                         class="book-cover"
                         onerror="this.src='https://via.placeholder.com/220x300?text=No+Cover'">
                </div>
                <div class="book-info">
                    <h3 class="book-title">{{ $book->judul }}</h3>
                    <p class="book-author">{{ $book->penulis }}</p>
                    @if($book->kategori)
                    <span class="book-category-tag">
                        <i class="fas fa-tag"></i> {{ $book->kategori->nama_kategori }}
                    </span>
                    @endif
                    <div class="book-price">Rp {{ number_format($book->harga, 0, ',', '.') }}</div>
                    <div class="book-stock">
                        <i class="fas fa-box"></i> Stok: {{ $book->stok }}
                    </div>
                    <div class="book-actions">
                        @if($book->stok > 0)
                        <button class="btn btn-green btn-sm btn-block add-to-cart" data-book-id="{{ $book->id_buku }}" onclick="event.preventDefault()">
                            <i class="fas fa-cart-plus"></i> Keranjang
                        </button>
                        @else
                        <button class="btn btn-outline btn-sm btn-block" disabled onclick="event.preventDefault()">
                            <i class="fas fa-times"></i> Stok Habis
                        </button>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 80px 20px;">
                <i class="fas fa-search" style="font-size: 56px; color: var(--green-pastel); margin-bottom: 16px; display: block;"></i>
                <h3 style="color: var(--text-dark); margin-bottom: 8px;">Tidak ada buku ditemukan</h3>
                <p style="color: var(--text-light);">Coba ubah kata kunci atau filter pencarian Anda</p>
            </div>
            @endforelse
        </div>
        
        <!-- Navigasi Halaman -->
        @if($books->hasPages())
        <div class="pagination-wrapper">
            <div class="custom-pagination">
                @if($books->onFirstPage())
                    <span class="pagination-btn disabled">
                        <i class="fas fa-chevron-left"></i> Previous
                    </span>
                @else
                    <a href="{{ $books->appends(request()->query())->previousPageUrl() }}" class="pagination-btn">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                @endif
                
                <span class="pagination-info">
                    Halaman {{ $books->currentPage() }} dari {{ $books->lastPage() }}
                </span>
                
                @if($books->hasMorePages())
                    <a href="{{ $books->appends(request()->query())->nextPageUrl() }}" class="pagination-btn">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="pagination-btn disabled">
                        Next <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
        @endif
    </section>
</div>
@endsection
