@extends('user.layout')

@section('title', $book->judul)

@section('content')
<div class="user-container">
    <!-- Breadcrumb -->
    <nav class="detail-breadcrumb">
        <a href="{{ route('user.home') }}">Beranda</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('user.books') }}">Katalog</a>
        <i class="fas fa-chevron-right"></i>
        @if($book->kategori)
        <a href="{{ route('user.books', ['kategori' => $book->id_kategori]) }}">{{ $book->kategori->nama_kategori }}</a>
        <i class="fas fa-chevron-right"></i>
        @endif
        <span>{{ $book->judul }}</span>
    </nav>

    <!-- Book Detail Main -->
    <div class="book-detail-main">
        <!-- Left: Cover Image -->
        <div class="book-detail-cover">
            <div class="cover-image-wrapper">
                <img src="{{ $book->cover ?: 'https://via.placeholder.com/400x560?text=No+Cover' }}" 
                     alt="{{ $book->judul }}"
                     onerror="this.src='https://via.placeholder.com/400x560?text=No+Cover'">
            </div>
        </div>

        <!-- Right: Book Info -->
        <div class="book-detail-info">
            <p class="detail-author">{{ $book->penulis }}</p>
            <h1 class="detail-title">{{ $book->judul }}</h1>

            <!-- Rating Summary -->
            <div class="detail-rating-summary">
                <div class="stars-display">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($avgRating))
                            <i class="fas fa-star"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                </div>
                <span class="rating-number">{{ number_format($avgRating, 1) }}</span>
                <span class="rating-count">({{ $reviewCount }} ulasan)</span>
            </div>

            <div class="detail-price">Rp{{ number_format($book->harga, 0, ',', '.') }}</div>

            <!-- Action Buttons -->
            <div class="detail-action-row">
                <button class="btn-favorite {{ $isFavorited ? 'favorited' : '' }}" id="favoriteBtn" data-book-id="{{ $book->id_buku }}">
                    <i class="{{ $isFavorited ? 'fas' : 'far' }} fa-heart"></i>
                    <span>{{ $isFavorited ? 'Favorit' : 'Favorit' }}</span>
                </button>
                <button class="btn-share" onclick="navigator.clipboard.writeText(window.location.href).then(()=>alert('Link disalin!'))">
                    <i class="fas fa-share-alt"></i>
                    <span>Bagikan</span>
                </button>
            </div>

            <!-- Format Selection -->
            <div class="detail-section">
                <h3 class="detail-section-label">Format Buku</h3>
                <div class="format-options">
                    <button class="format-btn active" data-format="softcover">Soft Cover</button>
                    <button class="format-btn" data-format="hardcover">Hard Cover</button>
                </div>
            </div>

            <!-- Store Info -->
            <div class="detail-section">
                <h3 class="detail-section-label">Toko</h3>
                <div class="store-info-card">
                    <div class="store-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="store-details">
                        <h4>Toko Buku Berkah 1</h4>
                        <span class="store-location"><i class="fas fa-map-marker-alt"></i> Jakarta</span>
                    </div>
                </div>
            </div>

            <!-- Stock Info -->
            <div class="detail-stock {{ $book->stok > 0 ? 'in-stock' : 'out-stock' }}">
                <i class="fas {{ $book->stok > 0 ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                {{ $book->stok > 0 ? 'Stok Tersedia ('.$book->stok.')' : 'Stok Habis' }}
            </div>

            <!-- Add to Cart -->
            <div class="detail-cart-actions">
                @if($book->stok > 0)
                <div class="qty-selector">
                    <button class="qty-btn" id="qtyMinus">-</button>
                    <input type="number" id="qtyInput" value="1" min="1" max="{{ $book->stok }}" readonly>
                    <button class="qty-btn" id="qtyPlus">+</button>
                </div>
                <button class="btn btn-green btn-lg btn-add-to-cart-detail" id="addToCartDetail" data-book-id="{{ $book->id_buku }}">
                    <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                </button>
                @else
                <button class="btn btn-outline btn-lg" disabled>
                    <i class="fas fa-times"></i> Stok Habis
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Description & Details Tabs -->
    <div class="book-detail-tabs">
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="description">Deskripsi</button>
            <button class="tab-btn" data-tab="details">Detail Buku</button>
            <button class="tab-btn" data-tab="reviews">Ulasan ({{ $reviewCount }})</button>
        </div>

        <!-- Description Tab -->
        <div class="tab-content active" id="tab-description">
            <div class="description-content">
                @if($book->deskripsi)
                    {!! nl2br(e($book->deskripsi)) !!}
                @else
                    <p class="no-description">Belum ada deskripsi untuk buku ini.</p>
                @endif
            </div>
        </div>

        <!-- Details Tab -->
        <div class="tab-content" id="tab-details">
            <table class="book-details-table">
                <tr>
                    <th>Judul</th>
                    <td>{{ $book->judul }}</td>
                </tr>
                <tr>
                    <th>Penulis</th>
                    <td>{{ $book->penulis }}</td>
                </tr>
                <tr>
                    <th>Penerbit</th>
                    <td>{{ $book->penerbit }}</td>
                </tr>
                <tr>
                    <th>ISBN</th>
                    <td>{{ $book->isbn ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tahun Terbit</th>
                    <td>{{ $book->tahun_terbit ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Kategori</th>
                    <td>{{ $book->kategori->nama_kategori ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Stok</th>
                    <td>{{ $book->stok }}</td>
                </tr>
            </table>
        </div>

        <!-- Reviews Tab -->
        <div class="tab-content" id="tab-reviews">
            <!-- Review Summary -->
            <div class="review-summary">
                <div class="review-avg">
                    <span class="avg-number">{{ number_format($avgRating, 1) }}</span>
                    <div class="avg-stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avgRating))
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="avg-count">{{ $reviewCount }} ulasan</span>
                </div>
                <div class="review-bars">
                    @for($star = 5; $star >= 1; $star--)
                        @php
                            $count = $book->ulasan->where('rating', $star)->count();
                            $pct = $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0;
                        @endphp
                        <div class="review-bar-row">
                            <span class="bar-label">{{ $star }} <i class="fas fa-star"></i></span>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="bar-count">{{ $count }}</span>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Write Review -->
            <div class="write-review-section">
                <h3>{{ $userReview ? 'Edit Ulasan Anda' : 'Tulis Ulasan' }}</h3>
                <form action="{{ route('user.book.review', $book->id_buku) }}" method="POST" class="review-form">
                    @csrf
                    <div class="star-rating-input">
                        <span>Rating:</span>
                        @for($i = 1; $i <= 5; $i++)
                            <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" {{ ($userReview && $userReview->rating == $i) ? 'checked' : '' }}>
                            <label for="star{{ $i }}"><i class="far fa-star"></i></label>
                        @endfor
                    </div>
                    <textarea name="komentar" rows="4" placeholder="Bagikan pengalaman membaca buku ini..." maxlength="1000">{{ $userReview->komentar ?? '' }}</textarea>
                    <button type="submit" class="btn btn-green">
                        <i class="fas fa-paper-plane"></i> {{ $userReview ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
                    </button>
                </form>
            </div>

            <!-- Review List -->
            <div class="review-list">
                @forelse($book->ulasan->sortByDesc('created_at') as $review)
                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer-avatar">
                            {{ strtoupper(substr($review->user->nama ?? 'U', 0, 1)) }}
                        </div>
                        <div class="reviewer-info">
                            <h4>{{ $review->user->nama ?? 'Pengguna' }}</h4>
                            <div class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <span class="review-date">{{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</span>
                    </div>
                    @if($review->komentar)
                    <p class="review-text">{{ $review->komentar }}</p>
                    @endif
                </div>
                @empty
                <div class="no-reviews">
                    <i class="far fa-comment-dots"></i>
                    <p>Belum ada ulasan. Jadilah yang pertama!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Related Books -->
    @if($relatedBooks->count() > 0)
    <section class="related-books-section">
        <h2 class="section-title">Buku Terkait</h2>
        <div class="related-books-grid">
            @foreach($relatedBooks as $related)
            <a href="{{ route('user.book.detail', $related->id_buku) }}" class="book-card book-card-link">
                <div class="book-cover-wrapper">
                    <img src="{{ $related->cover ?: 'https://via.placeholder.com/220x300?text=No+Cover' }}" 
                         alt="{{ $related->judul }}" 
                         class="book-cover"
                         onerror="this.src='https://via.placeholder.com/220x300?text=No+Cover'">
                </div>
                <div class="book-info">
                    <p class="book-author">{{ $related->penulis }}</p>
                    <h3 class="book-title">{{ $related->judul }}</h3>
                    <div class="book-price">Rp{{ number_format($related->harga, 0, ',', '.') }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
        });
    });

    // Quantity selector
    const qtyInput = document.getElementById('qtyInput');
    const qtyMinus = document.getElementById('qtyMinus');
    const qtyPlus = document.getElementById('qtyPlus');
    if (qtyInput) {
        const maxQty = parseInt(qtyInput.getAttribute('max'));
        qtyMinus?.addEventListener('click', () => {
            let val = parseInt(qtyInput.value);
            if (val > 1) qtyInput.value = val - 1;
        });
        qtyPlus?.addEventListener('click', () => {
            let val = parseInt(qtyInput.value);
            if (val < maxQty) qtyInput.value = val + 1;
        });
    }

    // Add to cart
    const addBtn = document.getElementById('addToCartDetail');
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            const bookId = this.dataset.bookId;
            const qty = parseInt(qtyInput.value);
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            addBtn.disabled = true;
            addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menambahkan...';

            fetch('{{ route("api.cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ book_id: bookId, quantity: qty })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    addBtn.innerHTML = '<i class="fas fa-check"></i> Ditambahkan!';
                    setTimeout(() => {
                        addBtn.innerHTML = '<i class="fas fa-cart-plus"></i> Tambah ke Keranjang';
                        addBtn.disabled = false;
                    }, 2000);
                    // Update cart count in navbar
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        fetch('{{ route("api.cart.count") }}')
                            .then(r => r.json())
                            .then(d => cartCount.textContent = d.count);
                    }
                } else {
                    alert(data.message || 'Gagal menambah ke keranjang');
                    addBtn.innerHTML = '<i class="fas fa-cart-plus"></i> Tambah ke Keranjang';
                    addBtn.disabled = false;
                }
            })
            .catch(() => {
                alert('Terjadi kesalahan');
                addBtn.innerHTML = '<i class="fas fa-cart-plus"></i> Tambah ke Keranjang';
                addBtn.disabled = false;
            });
        });
    }

    // Favorite toggle
    const favBtn = document.getElementById('favoriteBtn');
    if (favBtn) {
        favBtn.addEventListener('click', function() {
            const bookId = this.dataset.bookId;
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('{{ url("/book/") }}/' + bookId + '/favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const icon = favBtn.querySelector('i');
                    if (data.favorited) {
                        favBtn.classList.add('favorited');
                        icon.className = 'fas fa-heart';
                    } else {
                        favBtn.classList.remove('favorited');
                        icon.className = 'far fa-heart';
                    }
                }
            });
        });
    }

    // Format buttons
    document.querySelectorAll('.format-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.format-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Star rating input
    document.querySelectorAll('.star-rating-input label').forEach(label => {
        label.addEventListener('click', function() {
            const radio = this.previousElementSibling;
            radio.checked = true;
            const val = parseInt(radio.value);
            document.querySelectorAll('.star-rating-input label i').forEach((star, idx) => {
                star.className = idx < val ? 'fas fa-star' : 'far fa-star';
            });
        });
    });
    // Initialize star display if editing
    const checkedStar = document.querySelector('.star-rating-input input:checked');
    if (checkedStar) {
        const val = parseInt(checkedStar.value);
        document.querySelectorAll('.star-rating-input label i').forEach((star, idx) => {
            star.className = idx < val ? 'fas fa-star' : 'far fa-star';
        });
    }
});
</script>
@endpush
@endsection
