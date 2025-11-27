<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Toko Buku Online - Temukan koleksi buku terlengkap dengan harga terbaik">
    
    <title>Toko Buku Online - Koleksi Buku Terlengkap</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ time() }}">
</head>
<body>
    <!-- Scroll Progress Bar -->
    <div class="scroll-progress" style="position: fixed; top: 0; left: 0; height: 4px; background: linear-gradient(90deg, #1e3a8a, #3b82f6); z-index: 9999; transition: width 0.3s;"></div>

    <!-- Navbar -->
    <nav class="landing-navbar">
        <div class="navbar-content">
            <a href="#" class="navbar-logo">
                <i class="fas fa-book-reader"></i>
                <span>Toko Buku</span>
            </a>

            <ul class="navbar-menu">
                <li><a href="#home">Beranda</a></li>
                <li><a href="#features">Fitur</a></li>
                <li><a href="#books">Katalog</a></li>
                <li><a href="{{ route('about') }}">Tentang</a></li>
                <li><a href="#contact">Kontak</a></li>
            </ul>

            <div class="navbar-actions">
                <a href="{{ route('login') }}" class="btn btn-outline">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </a>
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="hero-content">
            <div class="hero-text">
                <h1>
                    Jelajahi Dunia <br>
                    Melalui <span>Buku</span>
                </h1>
                <p>
                    Temukan ribuan koleksi buku dari berbagai genre dan penulis terbaik. 
                    Dapatkan pengetahuan baru dengan harga terjangkau dan pengiriman cepat ke seluruh Indonesia.
                </p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="btn btn-white">
                        <i class="fas fa-rocket"></i> Mulai Sekarang
                    </a>
                    <a href="#books" class="btn btn-outline">
                        <i class="fas fa-book"></i> Lihat Katalog
                    </a>
                </div>
            </div>

            <div class="hero-image">
                <!-- Orbit Particles Animation -->
                <div class="orbit-container">
                    <!-- Orbit Rings -->
                    <div class="orbit-ring orbit-ring-1">
                        <div class="orbit-particle orbit-particle-1"></div>
                        <div class="orbit-particle orbit-particle-2"></div>
                    </div>
                    <div class="orbit-ring orbit-ring-2">
                        <div class="orbit-particle orbit-particle-3"></div>
                        <div class="orbit-particle orbit-particle-4"></div>
                    </div>
                    <div class="orbit-ring orbit-ring-3">
                        <div class="orbit-particle orbit-particle-5"></div>
                        <div class="orbit-particle orbit-particle-6"></div>
                        <div class="orbit-particle orbit-particle-7"></div>
                        <div class="orbit-particle orbit-particle-8"></div>
                    </div>
                    
                    <!-- Floating Stars -->
                    <div class="orbit-star orbit-star-1"></div>
                    <div class="orbit-star orbit-star-2"></div>
                    <div class="orbit-star orbit-star-3"></div>
                    <div class="orbit-star orbit-star-4"></div>
                    <div class="orbit-star orbit-star-5"></div>
                    <div class="orbit-star orbit-star-6"></div>
                </div>
                
                <img src="{{ asset('gambar/books-landing.png') }}" alt="Koleksi Buku">
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="stats-container">
            <div class="stat-item reveal">
                <i class="fas fa-book"></i>
                <h3 class="stat-number" data-target="5000">0</h3>
                <p>Koleksi Buku</p>
            </div>
            <div class="stat-item reveal">
                <i class="fas fa-users"></i>
                <h3 class="stat-number" data-target="10000">0</h3>
                <p>Pengguna Aktif</p>
            </div>
            <div class="stat-item reveal">
                <i class="fas fa-star"></i>
                <h3 class="stat-number" data-target="4500">0</h3>
                <p>Review Positif</p>
            </div>
            <div class="stat-item reveal">
                <i class="fas fa-truck"></i>
                <h3 class="stat-number" data-target="15000">0</h3>
                <p>Pengiriman Sukses</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="features-container">
            <div class="section-header reveal">
                <h2>Mengapa Memilih Kami?</h2>
                <p>Kami memberikan pengalaman terbaik dalam berbelanja buku online dengan berbagai keunggulan</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Pembayaran Aman</h3>
                    <p>Transaksi dijamin aman dengan berbagai metode pembayaran yang terpercaya dan terenkripsi.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3>Pengiriman Cepat</h3>
                    <p>Pengiriman ke seluruh Indonesia dengan berbagai pilihan kurir terpercaya dan tracking real-time.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3>Harga Terbaik</h3>
                    <p>Dapatkan harga terbaik dengan berbagai promo menarik dan diskon hingga 50% setiap hari.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>Koleksi Lengkap</h3>
                    <p>Ribuan judul buku dari berbagai kategori, penulis terkenal, dan penerbit terpercaya.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Customer Service 24/7</h3>
                    <p>Tim customer service kami siap membantu Anda kapan saja dengan respon yang cepat dan ramah.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h3>Garansi Pengembalian</h3>
                    <p>Jaminan pengembalian 100% jika produk tidak sesuai atau mengalami kerusakan saat pengiriman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Books Showcase Section -->
    <section class="books-section" id="books">
        <div class="books-container">
            <div class="section-header reveal">
                <h2>Koleksi Buku Populer</h2>
                <p>Jelajahi koleksi buku terpopuler dan terlaris minggu ini</p>
            </div>

            <div class="books-grid">
                <!-- Sample Book Card 1 -->
                <div class="book-card reveal">
                    <img src="https://via.placeholder.com/250x350/3b82f6/ffffff?text=Novel+Fiksi" alt="Book Cover" class="book-cover">
                    <div class="book-details">
                        <div class="book-category">Fiksi</div>
                        <h3 class="book-title">Laskar Pelangi</h3>
                        <p class="book-author">Andrea Hirata</p>
                        <div class="book-footer">
                            <div class="book-price">Rp 89.000</div>
                            <button class="book-btn">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sample Book Card 2 -->
                <div class="book-card reveal">
                    <img src="https://via.placeholder.com/250x350/1e40af/ffffff?text=Pendidikan" alt="Book Cover" class="book-cover">
                    <div class="book-details">
                        <div class="book-category">Pendidikan</div>
                        <h3 class="book-title">Algoritma & Pemrograman</h3>
                        <p class="book-author">Dr. Budi Raharjo</p>
                        <div class="book-footer">
                            <div class="book-price">Rp 125.000</div>
                            <button class="book-btn">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sample Book Card 3 -->
                <div class="book-card reveal">
                    <img src="https://via.placeholder.com/250x350/60a5fa/ffffff?text=Romance" alt="Book Cover" class="book-cover">
                    <div class="book-details">
                        <div class="book-category">Romance</div>
                        <h3 class="book-title">Hujan Bulan Juni</h3>
                        <p class="book-author">Sapardi Djoko Damono</p>
                        <div class="book-footer">
                            <div class="book-price">Rp 75.000</div>
                            <button class="book-btn">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sample Book Card 4 -->
                <div class="book-card reveal">
                    <img src="https://via.placeholder.com/250x350/0ea5e9/ffffff?text=Motivasi" alt="Book Cover" class="book-cover">
                    <div class="book-details">
                        <div class="book-category">Motivasi</div>
                        <h3 class="book-title">Atomic Habits</h3>
                        <p class="book-author">James Clear</p>
                        <div class="book-footer">
                            <div class="book-price">Rp 98.000</div>
                            <button class="book-btn">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sample Book Card 5 -->
                <div class="book-card reveal">
                    <img src="https://via.placeholder.com/250x350/1e3a8a/ffffff?text=Sejarah" alt="Book Cover" class="book-cover">
                    <div class="book-details">
                        <div class="book-category">Sejarah</div>
                        <h3 class="book-title">Sapiens: Riwayat Singkat Umat Manusia</h3>
                        <p class="book-author">Yuval Noah Harari</p>
                        <div class="book-footer">
                            <div class="book-price">Rp 135.000</div>
                            <button class="book-btn">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sample Book Card 6 -->
                <div class="book-card reveal">
                    <img src="https://via.placeholder.com/250x350/3b82f6/ffffff?text=Bisnis" alt="Book Cover" class="book-cover">
                    <div class="book-details">
                        <div class="book-category">Bisnis</div>
                        <h3 class="book-title">Rich Dad Poor Dad</h3>
                        <p class="book-author">Robert T. Kiyosaki</p>
                        <div class="book-footer">
                            <div class="book-price">Rp 110.000</div>
                            <button class="book-btn">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sample Book Card 7 -->
                <div class="book-card reveal">
                    <img src="https://via.placeholder.com/250x350/60a5fa/ffffff?text=Teknologi" alt="Book Cover" class="book-cover">
                    <div class="book-details">
                        <div class="book-category">Teknologi</div>
                        <h3 class="book-title">AI Artificial Intelligence</h3>
                        <p class="book-author">Stuart Russell</p>
                        <div class="book-footer">
                            <div class="book-price">Rp 145.000</div>
                            <button class="book-btn">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sample Book Card 8 -->
                <div class="book-card reveal">
                    <img src="https://via.placeholder.com/250x350/0ea5e9/ffffff?text=Sastra" alt="Book Cover" class="book-cover">
                    <div class="book-details">
                        <div class="book-category">Sastra</div>
                        <h3 class="book-title">Bumi Manusia</h3>
                        <p class="book-author">Pramoedya Ananta Toer</p>
                        <div class="book-footer">
                            <div class="book-price">Rp 95.000</div>
                            <button class="book-btn">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div style="text-align: center; margin-top: 60px;">
                <a href="{{ route('register') }}" class="btn btn-primary" style="font-size: 18px; padding: 18px 45px;">
                    <i class="fas fa-eye"></i> Lihat Semua Koleksi
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="about">
        <div class="cta-container">
            <h2>Siap Memulai Petualangan Membaca?</h2>
            <p>
                Bergabunglah dengan ribuan pembaca lainnya dan dapatkan akses ke koleksi buku terlengkap. 
                Daftar sekarang dan nikmati berbagai promo menarik khusus untuk member baru!
            </p>
            <div class="cta-buttons">
                <a href="{{ route('register') }}" class="btn btn-cta-primary">
                    <i class="fas fa-rocket"></i> Daftar Gratis Sekarang
                </a>
                <a href="#books" class="btn btn-cta-outline">
                    <i class="fas fa-compass"></i> Jelajahi Katalog
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer" id="contact">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Toko Buku Online</h3>
                <p>
                    Toko buku online terpercaya dengan koleksi buku terlengkap dan harga terbaik. 
                    Kami berkomitmen memberikan pengalaman berbelanja yang menyenangkan.
                </p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="footer-section">
                <h3>Navigasi</h3>
                <ul>
                    <li><a href="#home">Beranda</a></li>
                    <li><a href="#features">Fitur</a></li>
                    <li><a href="#books">Katalog Buku</a></li>
                    <li><a href="#about">Tentang Kami</a></li>
                    <li><a href="#contact">Kontak</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Layanan</h3>
                <ul>
                    <li><a href="#">Cara Berbelanja</a></li>
                    <li><a href="#">Metode Pembayaran</a></li>
                    <li><a href="#">Pengiriman</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">Syarat & Ketentuan</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Hubungi Kami</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> Jakarta, Indonesia</li>
                    <li><i class="fas fa-phone"></i> +62 812-3456-7890</li>
                    <li><i class="fas fa-envelope"></i> info@tokobuku.com</li>
                    <li><i class="fas fa-clock"></i> Senin - Sabtu: 09:00 - 18:00</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Toko Buku Online. All Rights Reserved. Made with <i class="fas fa-heart" style="color: #ef4444;"></i> in Indonesia</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/landing.js') }}?v={{ time() }}" defer></script>
</body>
</html>
