@extends('user.layout')

@section('title', 'Katalog Buku')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <!-- Search & Filter Section -->
    <section style="margin-bottom: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <h1 class="section-title">Katalog Buku</h1>
            
            <form method="GET" action="{{ route('user.books') }}" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <input type="text" 
                       name="search" 
                       placeholder="Cari buku, penulis, penerbit..." 
                       value="{{ request('search') }}"
                       style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; min-width: 250px;">
                
                <select name="kategori" 
                        style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; min-width: 150px;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id_kategori }}" {{ request('kategori') == $category->id_kategori ? 'selected' : '' }}>
                        {{ $category->nama_kategori }}
                    </option>
                    @endforeach
                </select>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
                
                @if(request('search') || request('kategori'))
                <a href="{{ route('user.books') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Reset
                </a>
                @endif
            </form>
        </div>
    </section>

    <!-- Books Grid -->
    <section class="books-section">
        @if(request('search'))
        <p style="color: #666; margin-bottom: 20px;">
            Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
        </p>
        @endif
        
        <div class="books-grid">
            @forelse($books as $book)
            <div class="book-card">
                <img src="{{ $book->cover ?: 'https://via.placeholder.com/220x300?text=No+Cover' }}" 
                     alt="{{ $book->judul }}" 
                     class="book-cover"
                     onerror="this.src='https://via.placeholder.com/220x300?text=No+Cover'">
                <div class="book-info">
                    <h3 class="book-title">{{ $book->judul }}</h3>
                    <p class="book-author">{{ $book->penulis }}</p>
                    @if($book->kategori)
                    <p style="font-size: 12px; color: var(--user-accent); margin-bottom: 10px;">
                        <i class="fas fa-tag"></i> {{ $book->kategori->nama_kategori }}
                    </p>
                    @endif
                    <div class="book-price">Rp {{ number_format($book->harga, 0, ',', '.') }}</div>
                    <div style="font-size: 13px; color: #999; margin-bottom: 15px;">
                        <i class="fas fa-box"></i> Stok: {{ $book->stok }}
                    </div>
                    <div class="book-actions">
                        @if($book->stok > 0)
                        <button class="btn btn-primary btn-block add-to-cart" data-book-id="{{ $book->id_buku }}">
                            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                        </button>
                        @else
                        <button class="btn btn-outline btn-block" disabled>
                            <i class="fas fa-times"></i> Stok Habis
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1/-1; text-align: center; color: #999; padding: 60px 20px;">
                <i class="fas fa-search" style="font-size: 64px; display: block; margin-bottom: 20px; opacity: 0.3;"></i>
                <h3 style="margin-bottom: 10px;">Tidak ada buku ditemukan</h3>
                <p>Coba ubah kata kunci atau filter pencarian Anda</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
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

@push('styles')
<style>
/* Custom Pagination Styles */
.pagination-wrapper {
    margin-top: 50px;
    display: flex;
    justify-content: center;
}

.custom-pagination {
    display: flex;
    align-items: center;
    gap: 20px;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: white;
    color: var(--user-primary);
    border: 2px solid var(--user-primary);
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s;
    cursor: pointer;
}

.pagination-btn:hover:not(.disabled) {
    background: var(--user-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
}

.pagination-btn.disabled {
    background: #f5f5f5;
    color: #ccc;
    border-color: #e0e0e0;
    cursor: not-allowed;
}

.pagination-info {
    padding: 0 15px;
    color: #666;
    font-weight: 500;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .custom-pagination {
        gap: 10px;
    }
    
    .pagination-btn {
        padding: 10px 18px;
        font-size: 14px;
    }
    
    .pagination-info {
        font-size: 13px;
        padding: 0 8px;
    }
}

</style>
@endpush

@endsection
