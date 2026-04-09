@extends('user.layout')

@section('title', 'Beranda')

@section('content')
<!-- Banner Selamat Datang -->
<div class="user-container" style="padding-bottom: 0;">
    <div class="welcome-banner">
        <div class="welcome-text">
            <h2>Selamat Datang di 6BUCKS.litera</h2>
            <p>Temukan ribuan koleksi buku terbaik dari berbagai genre. Mulai petualangan membacamu hari ini!</p>
            <div class="welcome-actions">
                <a href="{{ route('user.books') }}" class="btn btn-primary">
                    <i class="fas fa-compass"></i> Jelajahi Katalog
                </a>
                <a href="#categories" class="btn btn-outline">
                    <i class="fas fa-layer-group"></i> Kategori
                </a>
            </div>
        </div>
        <div class="welcome-stats">
            <div class="welcome-stat">
                <h4>{{ isset($books) ? $books->count() : '100' }}+</h4>
                <span>Koleksi Buku</span>
            </div>
            <div class="welcome-stat">
                <h4>{{ isset($categories) ? count($categories) : '5' }}</h4>
                <span>Kategori</span>
            </div>
        </div>
    </div>
</div>

<!-- Bagian Slider Hero -->
<div class="user-container" style="padding-top: 0;">
    <div class="hero-slider-wrapper">
        <!-- Carousel Utama -->
        <div class="main-carousel">
            <div class="carousel-slides">
                <div class="carousel-slide active">
                    <img src="{{ asset('gambar/home asset/iklan kitkat.jpg') }}" alt="Promo 1">
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('gambar/home asset/iklan sprite.jpg') }}" alt="Promo 2">
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('gambar/home asset/iklan teajus.jpg') }}" alt="Promo 3">
                </div>
            </div>
            <div class="carousel-dots">
                <span class="dot active" data-slide="0"></span>
                <span class="dot" data-slide="1"></span>
                <span class="dot" data-slide="2"></span>
            </div>
            <div class="carousel-footer">
                <a href="#" class="see-all-promos">Semua Promo</a>
            </div>
        </div>
        
        <!-- Kartu Promo Samping -->
        <div class="side-promos">
            <div class="promo-card-small" style="background: url('{{ asset('gambar/home asset/milo.jpg') }}') center/cover;">
            </div>
            
            <div class="promo-card-small" style="background: url('{{ asset('gambar/home asset/power.jpg') }}') center/cover;">
            </div>
        </div>
    </div>
</div>

<!-- Kontainer Konten Utama -->
<div class="user-container">
    <!-- Bagian Kategori -->
    <section class="categories-section" id="categories">
        <h2 class="section-title">Kategori Populer</h2>
        <div class="categories-grid">
            @php
                $categoryImages = [
                    'Novel Indonesia' => 'Novel.png',
                    'Novel Romance' => 'Novel-romance.png',
                    'Novel Terjemahan' => 'Novel-terjemahan.png',
                    'Puisi & Sastra' => 'puisi_dansastra.png',
                ];
            @endphp
            @forelse($categories ?? [] as $category)
            <div class="category-card-img" onclick="window.location.href='{{ route('user.books', ['kategori' => $category->id_kategori]) }}'"
                 style="background-image: url('{{ asset('gambar/' . ($categoryImages[$category->nama_kategori] ?? 'Novel.png')) }}');">
                <div class="category-card-overlay">
                    <h3>{{ $category->nama_kategori }}</h3>
                </div>
            </div>
            @empty
            <div class="category-card-img" style="background-image: url('{{ asset('gambar/Novel.png') }}');">
                <div class="category-card-overlay"><h3>Novel Indonesia</h3></div>
            </div>
            <div class="category-card-img" style="background-image: url('{{ asset('gambar/Novel-romance.png') }}');">
                <div class="category-card-overlay"><h3>Novel Romance</h3></div>
            </div>
            <div class="category-card-img" style="background-image: url('{{ asset('gambar/Novel-terjemahan.png') }}');">
                <div class="category-card-overlay"><h3>Novel Terjemahan</h3></div>
            </div>
            <div class="category-card-img" style="background-image: url('{{ asset('gambar/puisi_dansastra.png') }}');">
                <div class="category-card-overlay"><h3>Puisi & Sastra</h3></div>
            </div>
            @endforelse
        </div>
    </section>

    <!-- Slider Banner Promo -->
    <section class="promo-banners-section">
        <div class="promo-banners-slider">
            <div class="promo-banner-track">
                <div class="promo-banner-slide active">
                    <img src="{{ asset('gambar/home asset/banner1.jpg') }}" alt="Promo Banner 1">
                </div>
                <div class="promo-banner-slide">
                    <img src="{{ asset('gambar/banner buku.jpg') }}" alt="Promo Banner 2">
                </div>
                <div class="promo-banner-slide">
                    <img src="{{ asset('gambar/home asset/banner3.jpg') }}" alt="Promo Banner 3">
                </div>
            </div>
            <button class="promo-banner-prev"><i class="fas fa-chevron-left"></i></button>
            <button class="promo-banner-next"><i class="fas fa-chevron-right"></i></button>
            <div class="promo-banner-dots">
                <span class="promo-banner-dot active" data-slide="0"></span>
                <span class="promo-banner-dot" data-slide="1"></span>
                <span class="promo-banner-dot" data-slide="2"></span>
            </div>
        </div>
    </section>

    <!-- Bagian Buku Unggulan -->
    <section class="books-section">
        <div class="books-section-header">
            <h2 class="section-title">Buku Terbaru & Terpopuler</h2>
            <a href="{{ route('user.books') }}" class="see-all-link">Lihat Semua</a>
        </div>
        <div class="books-carousel-wrapper">
            <button class="books-carousel-btn books-carousel-prev" id="booksCarouselPrev">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="books-carousel" id="booksCarousel">
                @forelse($books ?? [] as $book)
                <a href="{{ route('user.book.detail', $book->id_buku) }}" class="book-card book-card-link">
                    <div class="book-cover-wrapper">
                        <img src="{{ $book->cover ?: 'https://via.placeholder.com/220x300?text=No+Cover' }}" 
                             alt="{{ $book->judul }}" 
                             class="book-cover"
                             onerror="this.src='https://via.placeholder.com/220x300?text=No+Cover'">
                    </div>
                    <div class="book-info">
                        <p class="book-author">{{ $book->penulis }}</p>
                        <h3 class="book-title">{{ $book->judul }}</h3>
                        <div class="book-price">Rp{{ number_format($book->harga, 0, ',', '.') }}</div>
                    </div>
                </a>
                @empty
                <div style="text-align: center; padding: 60px 20px; width: 100%;">
                    <i class="fas fa-book-open" style="font-size: 56px; color: var(--green-pastel); margin-bottom: 16px; display: block;"></i>
                    <p style="color: var(--text-light);">Belum ada buku tersedia saat ini</p>
                </div>
                @endforelse
            </div>
            <button class="books-carousel-btn books-carousel-next" id="booksCarouselNext">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </section>

    <!-- Fitur Promo -->
    <section class="promo-features">
        <div class="promo-feature-card">
            <div class="promo-feature-icon"><i class="fas fa-shipping-fast"></i></div>
            <h3>Pengiriman Cepat</h3>
            <p>Gratis ongkir untuk pembelian di atas Rp 100.000</p>
        </div>
        <div class="promo-feature-card">
            <div class="promo-feature-icon"><i class="fas fa-shield-alt"></i></div>
            <h3>Pembayaran Aman</h3>
            <p>Transaksi aman dengan berbagai metode pembayaran</p>
        </div>
        <div class="promo-feature-card">
            <div class="promo-feature-icon"><i class="fas fa-headset"></i></div>
            <h3>Layanan 24/7</h3>
            <p>Customer service siap membantu Anda kapan saja</p>
        </div>
    </section>

    <!-- Banner Utama -->
    <section class="featured-section">
        <div class="featured-content">
            <div class="featured-text">
                <h2>Baca Buku, Buka Dunia Baru</h2>
                <p>Temukan inspirasi dan pengetahuan dari ribuan koleksi buku pilihan kami. Dari novel bestseller hingga buku-buku pendidikan berkualitas, semuanya ada di sini.</p>
                <p>Dapatkan diskon spesial untuk member baru dan nikmati pengalaman berbelanja yang menyenangkan!</p>
                <a href="{{ route('user.books') }}" class="btn btn-primary">
                    <i class="fas fa-book-open"></i> Mulai Membaca
                </a>
            </div>
            <div class="featured-image">
                <img src="{{ asset('gambar/home asset/gambar-promo.jpg') }}" alt="Books Collection">
            </div>
        </div>
    </section>

    <!-- Bagian Testimoni -->
    <section class="testimonial-section">
        <h2 class="section-title" style="text-align: center;">Apa Kata Mereka</h2>
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">"Koleksi bukunya lengkap dan harga terjangkau. Pengiriman juga cepat. Sangat recommended!"</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">A</div>
                    <div class="testimonial-info">
                        <h4>Ahmad Rizki</h4>
                        <p>Pelanggan Setia</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">"Website-nya mudah digunakan, proses checkout sangat simple. Puas banget belanja di sini!"</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">S</div>
                    <div class="testimonial-info">
                        <h4>Siti Nurhaliza</h4>
                        <p>Pembaca Aktif</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">"Buku-bukunya original dan berkualitas. Customer service juga responsif. Terima kasih!"</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">B</div>
                    <div class="testimonial-info">
                        <h4>Budi Santoso</h4>
                        <p>Kolektor Buku</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bagian Blog -->
    <section class="blog-section">
        <div class="blog-header">
            <h2 class="section-title" style="margin-bottom: 0;">Blog & Artikel</h2>
            <a href="#" class="blog-view-all">Lihat Semua <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="blog-grid">
            <div class="blog-card">
                <div class="blog-image">
                    <img src="{{ asset('gambar/home asset/blog1.jpg') }}" alt="Baskara Putra">
                </div>
                <div class="blog-content">
                    <h3 class="blog-title">Baskara Putra</h3>
                    <p class="blog-excerpt">Biografi Singkat Baskara Putra. Daniel Baskara Putra, atau lebih dikenal dengan monomin Hindia adalah penyanyi-penulis lagu dan produser rekaman Indonesia.</p>
                    <div class="blog-meta">
                        <span class="blog-date"><i class="fas fa-calendar"></i> 24 Nov 2025</span>
                    </div>
                </div>
            </div>

            <div class="blog-card">
                <div class="blog-image">
                    <img src="{{ asset('gambar/home asset/blog2.jpg') }}" alt="Zootopia 2">
                </div>
                <div class="blog-content">
                    <h3 class="blog-title">Zootopia 2 Segera Meluncur ke Bioskop</h3>
                    <p class="blog-excerpt">Aksi apa yang akan dilakukan duo Judy dan Nick dalam Zootopia 2 nanti? Simak selengkapnya di sini!</p>
                    <div class="blog-meta">
                        <span class="blog-date"><i class="fas fa-calendar"></i> 21 Nov 2025</span>
                    </div>
                </div>
            </div>

            <div class="blog-card">
                <div class="blog-image">
                    <img src="{{ asset('gambar/home asset/blog3.jpg') }}" alt="Filosofi Teras">
                </div>
                <div class="blog-content">
                    <h3 class="blog-title">Filosofi Teras</h3>
                    <p class="blog-excerpt">Sering sekali, banyak masalah sepele tidak perlu di cari solusinya, cukup dihindari, seperti sekedar membuang ketimun pahit.</p>
                    <div class="blog-meta">
                        <span class="blog-date"><i class="fas fa-calendar"></i> 16 Apr 2024</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bagian Langganan Newsletter -->
    <section class="newsletter-section">
        <div class="newsletter-deco newsletter-deco-1"></div>
        <div class="newsletter-deco newsletter-deco-2"></div>
        <div class="newsletter-content">
            <div class="newsletter-icon-wrap"><i class="fas fa-bell"></i></div>
            <h2>Dapatkan Info Buku Terbaru</h2>
            <p>Daftarkan email Anda dan jadilah yang pertama tahu promo, diskon, dan koleksi buku terbaru kami!</p>
            <form class="newsletter-form" id="newsletterForm">
                <div class="newsletter-input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" placeholder="Masukkan email Anda..." required>
                </div>
                <button type="submit">
                    <i class="fas fa-paper-plane"></i> Daftar Sekarang
                </button>
            </form>
            <p class="newsletter-note"><i class="fas fa-lock"></i> Privasi Anda terjaga. Tidak ada spam.</p>
        </div>
    </section>
</div>

<!-- Modal Sukses Newsletter -->
<div class="nl-modal-overlay" id="nlModalOverlay">
    <div class="nl-modal" id="nlModal">
        <button class="nl-modal-close" id="nlModalClose"><i class="fas fa-times"></i></button>
        <div class="nl-modal-icon">
            <div class="nl-checkmark">
                <i class="fas fa-check"></i>
            </div>
        </div>
        <h3>Terima Kasih Telah Mendaftar!</h3>
        <p>Info buku terbaru, promo eksklusif, dan penawaran spesial akan segera hadir di inbox Anda.</p>
        <div class="nl-modal-tags">
            <span><i class="fas fa-book"></i> Buku Terbaru</span>
            <span><i class="fas fa-tag"></i> Promo Spesial</span>
            <span><i class="fas fa-gift"></i> Diskon Eksklusif</span>
        </div>
        <button class="nl-modal-btn" id="nlModalBtn">Siap, Terima Kasih!</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Hero Carousel Auto-Rotate
(function() {
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dots .dot');
    let currentSlide = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => slide.classList.toggle('active', i === index));
        dots.forEach((dot, i) => dot.classList.toggle('active', i === index));
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentSlide = index;
            showSlide(currentSlide);
        });
    });

    setInterval(nextSlide, 4000);
})();

// Newsletter form + popup modal
(function() {
    const form = document.getElementById('newsletterForm');
    const overlay = document.getElementById('nlModalOverlay');
    const modal = document.getElementById('nlModal');
    const closeBtn = document.getElementById('nlModalClose');
    const confirmBtn = document.getElementById('nlModalBtn');

    function openModal() {
        overlay.classList.add('active');
        setTimeout(() => modal.classList.add('active'), 10);
    }

    function closeModal() {
        modal.classList.remove('active');
        setTimeout(() => overlay.classList.remove('active'), 300);
    }

    form?.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mendaftar...';
        setTimeout(() => {
            openModal();
            this.reset();
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Daftar Sekarang';
            btn.disabled = false;
        }, 900);
    });

    closeBtn?.addEventListener('click', closeModal);
    confirmBtn?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', function(e) {
        if (e.target === overlay) closeModal();
    });

    // Auto-close after 6 seconds
    document.addEventListener('click', function check(e) {
        if (overlay?.classList.contains('active')) {
            setTimeout(closeModal, 6000);
            document.removeEventListener('click', check);
        }
    });
})();
</script>
@endpush
