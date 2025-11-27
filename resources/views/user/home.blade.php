@extends('user.layout')

@section('title', 'Beranda')

@section('content')
@if(session('success'))
<div class="alert alert-success" style="margin: 20px auto; max-width: 1200px;">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Announcement Banner (compact, no full-width gradient) -->
<div class="user-container" style="padding-top: 20px;">
    <div class="announcement-banner">
        <div class="announcement-icon"><i class="fas fa-bullhorn"></i></div>
        <p>Selamat datang di Toko Buku Online — temukan koleksi terbaik setiap hari.</p>
    </div>
</div>

<!-- Hero Slider Section -->
<div class="user-container" style="padding-top: 20px; padding-bottom: 20px;">
    <div class="hero-slider-wrapper">
        <!-- Main Carousel -->
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
            
            <!-- Navigation Dots -->
            <div class="carousel-dots">
                <span class="dot active" data-slide="0"></span>
                <span class="dot" data-slide="1"></span>
                <span class="dot" data-slide="2"></span>
            </div>
            
            <!-- See All Promos Link (inside carousel) -->
            <div class="carousel-footer">
                <a href="#" class="see-all-promos">Semua Promo</a>
            </div>
        </div>
        
        <!-- Side Promo Cards -->
        <div class="side-promos">
            <div class="promo-card-small" style="background: url('{{ asset('gambar/home asset/milo.jpg') }}') center/cover; position: relative;">
                <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(34, 139, 34, 0.7) 0%, rgba(0, 100, 0, 0.8) 100%); border-radius: 12px;"></div>
                <div style="position: relative; z-index: 1;">
                    <div class="promo-badge">Special Offer</div>
                    <h3>PASARAMPOK</h3>
                    <p class="promo-price"><span class="old-price">Rp125.000</span> <span class="new-price">Rp 115.000</span></p>
                    <p class="promo-date">26 November - 2 Desember 2025</p>
                </div>
            </div>
            
            <div class="promo-card-small" style="background: url('{{ asset('gambar/home asset/power.jpg') }}') center/cover; position: relative;">
                <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(59, 130, 246, 0.7) 0%, rgba(29, 78, 216, 0.8) 100%); border-radius: 12px;"></div>
                <div style="position: relative; z-index: 1;">
                    <div class="promo-badge">Special Offer</div>
                    <h3>DURI & KUTUK</h3>
                    <p class="promo-price"><span class="new-price">Rp 109.000</span></p>
                    <p class="promo-date">28 November - 6 Desember 2025</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Container -->
<div class="user-container">
    <!-- Categories Section -->
    <section class="categories-section">
        <h2 class="section-title">Kategori Populer</h2>
        <div class="categories-grid">
            @forelse($categories ?? [] as $category)
            <div class="category-card">
                <img src="{{ asset('gambar/home asset/icon.png') }}" alt="Category Icon" style="width: 50px; height: 50px; object-fit: contain;">
                <h3>{{ $category->nama_kategori }}</h3>
                <p>{{ $category->buku->count() }} Buku</p>
            </div>
            @empty
            <div class="category-card">
                <img src="{{ asset('gambar/home asset/icon.png') }}" alt="Category Icon" style="width: 50px; height: 50px; object-fit: contain;">
                <h3>Fiksi</h3>
                <p>Segera Hadir</p>
            </div>
            <div class="category-card">
                <img src="{{ asset('gambar/home asset/icon.png') }}" alt="Category Icon" style="width: 50px; height: 50px; object-fit: contain;">
                <h3>Pendidikan</h3>
                <p>Segera Hadir</p>
            </div>
            <div class="category-card">
                <img src="{{ asset('gambar/home asset/icon.png') }}" alt="Category Icon" style="width: 50px; height: 50px; object-fit: contain;">
                <h3>Romance</h3>
                <p>Segera Hadir</p>
            </div>
            <div class="category-card">
                <img src="{{ asset('gambar/home asset/icon.png') }}" alt="Category Icon" style="width: 50px; height: 50px; object-fit: contain;">
                <h3>Sci-Fi</h3>
                <p>Segera Hadir</p>
            </div>
            @endforelse
        </div>
    </section>

    <!-- Promotional Banners Slider -->
    <section class="promo-banners-section">
        <div class="promo-banners-slider">
            <div class="promo-banner-track">
                <div class="promo-banner-slide active">
                    <img src="{{ asset('gambar/home asset/banner1.jpg') }}" alt="Promo Banner 1">
                </div>
                <div class="promo-banner-slide">
                    <img src="{{ asset('gambar/home asset/banner2.jpg') }}" alt="Promo Banner 2">
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

    <!-- Featured Books Section -->
    <section class="books-section">
        <h2 class="section-title">Buku Terbaru & Terpopuler</h2>
        <div class="books-grid">
            @forelse($books ?? [] as $book)
            <div class="book-card">
                <img src="{{ $book->cover ?: 'https://via.placeholder.com/220x300?text=No+Cover' }}" 
                     alt="{{ $book->judul }}" 
                     class="book-cover"
                     onerror="this.src='https://via.placeholder.com/220x300?text=No+Cover'">
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
            <p style="grid-column: 1/-1; text-align: center; color: #999; padding: 40px;">
                <i class="fas fa-book-open" style="font-size: 48px; display: block; margin-bottom: 15px;"></i>
                Belum ada buku tersedia saat ini
            </p>
            @endforelse
        </div>
        
        @if(isset($books) && $books->count() > 0)
        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('user.books') }}" class="btn btn-accent" style="font-size: 16px; padding: 12px 35px;">
                Lihat Semua Buku <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        @endif
    </section>
</div>

<!-- Promotional Features -->
<section class="promo-section" style="max-width: 1400px; margin: 50px auto; padding: 0 30px;">
    <div class="promo-grid">
        <div class="promo-card">
            <div class="promo-icon">
                <i class="fas fa-shipping-fast"></i>
            </div>
            <h3 style="color: #000000;">Pengiriman Cepat</h3>
            <p style="color: #000000;">Gratis ongkir untuk pembelian di atas Rp 100.000</p>
        </div>
        <div class="promo-card">
            <div class="promo-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3 style="color: #000000;">Pembayaran Aman</h3>
            <p style="color: #000000;">Transaksi aman dengan berbagai metode pembayaran</p>
        </div>
        <div class="promo-card">
            <div class="promo-icon">
                <i class="fas fa-headset"></i>
            </div>
            <h3 style="color: #000000;">Layanan 24/7</h3>
            <p style="color: #000000;">Customer service siap membantu Anda kapan saja</p>
        </div>
    </div>
</section>

<!-- Featured Banner -->
<section class="featured-section" style="max-width: 1400px; margin: 50px auto;">
    <div class="featured-content">
        <div class="featured-text">
            <h2 style="color: #000000;">Baca Buku, Buka Dunia Baru</h2>
            <p style="color: #000000;">Temukan inspirasi dan pengetahuan dari ribuan koleksi buku pilihan kami. Dari novel bestseller hingga buku-buku pendidikan berkualitas, semuanya ada di sini.</p>
            <p style="color: #000000;">Dapatkan diskon spesial untuk member baru dan nikmati pengalaman berbelanja yang menyenangkan!</p>
            <a href="{{ route('user.books') }}" class="btn btn-primary" style="padding: 12px 30px; font-size: 16px;">
                <i class="fas fa-book-open"></i> Mulai Membaca
            </a>
        </div>
        <div class="featured-image">
            <img src="{{ asset('gambar/home asset/gambar-promo.jpg') }}" 
                 alt="Books Collection" 
                 style="border-radius: 15px;">
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonial-section" style="max-width: 1400px; margin: 50px auto;">
    <h2 class="section-title" style="text-align: center;">Apa Kata Mereka</h2>
    <div class="testimonial-grid">
        <div class="testimonial-card">
            <div class="testimonial-rating">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
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
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
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
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
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

<!-- Blog Section -->
<section class="blog-section" style="max-width: 1400px; margin: 50px auto; padding: 0 30px;">
    <div class="blog-header" style="text-align: center; margin-bottom: 40px;">
        <h2 class="section-title" style="text-align: center;">Blog</h2>
        <a href="#" class="blog-view-all">Lihat Semua</a>
    </div>
    
    <div class="blog-grid">
        <div class="blog-card">
            <div class="blog-image">
                <img src="{{ asset('gambar/home asset/blog1.jpg') }}" alt="Baskara Putra">
            </div>
            <div class="blog-content">
                <h3 class="blog-title">Baskara Putra</h3>
                <p class="blog-excerpt">Biografi Singkat Baskara Putra. Daniel Baskara Putra, atau lebih dikenal dengan monomin Hindia (lahir 22 Februari 1993) adalah penyanyi-penulis lagu, produser rekaman, dan komposer Indonesia.</p>
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
                <h3 class="blog-title">Zootopia 2 Segera Meluncur ke Bioskop. Tantangan Baru Mengintai di Balik...</h3>
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
                <h3 class="blog-title">Filosofi Teras.</h3>
                <p class="blog-excerpt">Sering sekali, banyak masalah sepele tidak perlu di cari solusinya, cukup dihindari, seperti sekedar membuang ketimun pahit atau mengambil jalan memutar. Gitu aja kok repot?</p>
                <div class="blog-meta">
                    <span class="blog-date"><i class="fas fa-calendar"></i> 16 Apr 2024</span>
                    <span class="blog-author">Henry Manampiring</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section" style="max-width: 1400px; margin: 50px auto;">
    <div class="newsletter-content">
        <h2><i class="fas fa-envelope"></i> Dapatkan Penawaran Terbaik</h2>
        <p>Daftarkan email Anda untuk mendapatkan info promo, diskon, dan buku-buku terbaru!</p>
        <form class="newsletter-form" id="newsletterForm">
            <input type="email" placeholder="Masukkan email Anda..." required>
            <button type="submit">
                <i class="fas fa-paper-plane"></i> Daftar
            </button>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Hero Carousel Auto-Rotate
(function() {
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dots .dot');
    let currentSlide = 0;
    const slideInterval = 4000; // 4 seconds

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    // Dot click handlers
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentSlide = index;
            showSlide(currentSlide);
        });
    });

    // Auto-rotate
    setInterval(nextSlide, slideInterval);
})();

// Add to cart functionality is handled by user.js globally
// No need for duplicate event listener here

// Newsletter form
document.getElementById('newsletterForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const button = this.querySelector('button');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<span class="loading"></span> Mendaftar...';
    button.disabled = true;
    
    setTimeout(() => {
        alert('Terima kasih! Anda telah berlangganan newsletter kami.');
        this.reset();
        button.innerHTML = originalText;
        button.disabled = false;
    }, 1000);
});
</script>
@endpush

