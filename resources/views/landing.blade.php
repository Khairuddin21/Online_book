<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Book.com - Find the book you're looking for, easier to read">
    
    <title>Book.com - Temukan Buku Favoritmu</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ time() }}">
</head>
<body>
    <!-- Navbar -->
    <nav class="landing-navbar">
        <div class="navbar-content">
            <a href="#" class="navbar-logo">
                <span>Book.com</span>
            </a>

            <ul class="navbar-menu">
                <li><a href="#home">Book Types</a></li>
                <li><a href="#books">Recommendations</a></li>
                <li><a href="#features">Popular</a></li>
                <li><a href="#about">About Us</a></li>
            </ul>

            <div class="navbar-actions">
                <a href="{{ route('login') }}" class="btn-login">Login</a>
                <a href="{{ route('register') }}" class="btn-start">Start For Free</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Find the book you're looking for easier to read.</h1>
                <p>The most appropriate book site to reach books</p>
                <div class="hero-search">
                    <input type="text" placeholder="Find your favorite book here...">
                    <button type="button">Search</button>
                </div>
            </div>

            <div class="hero-image">
                <!-- Floating geometric icons -->
                <div class="floating-icon icon-idea">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="floating-icon icon-medal">
                    <i class="fas fa-award"></i>
                </div>
                <div class="floating-icon icon-target">
                    <i class="fas fa-bullseye"></i>
                </div>
                
                <!-- 3D Book Stack -->
                <div class="book3d-scene" id="book3dScene">
                    <div class="book3d-stack" id="book3dStack">
                        <!-- Book 5 (bottom) - Teal -->
                        <div class="book3d" style="--book-color: #2a9d8f; --book-color-dark: #1f7a6e; --book-pages: #f5f0e8; --book-height: 36px; --book-width: 180px; --book-depth: 130px; transform: translateY(0px);">
                            <div class="book3d-face book3d-front"></div>
                            <div class="book3d-face book3d-back"></div>
                            <div class="book3d-face book3d-spine"></div>
                            <div class="book3d-face book3d-edge"></div>
                            <div class="book3d-face book3d-top"></div>
                            <div class="book3d-face book3d-bottom"></div>
                        </div>
                        <!-- Book 4 - Gold -->
                        <div class="book3d" style="--book-color: #e9b44c; --book-color-dark: #c89a3a; --book-pages: #f8f3ea; --book-height: 34px; --book-width: 170px; --book-depth: 125px; transform: translateY(-36px) rotateY(6deg);">
                            <div class="book3d-face book3d-front"></div>
                            <div class="book3d-face book3d-back"></div>
                            <div class="book3d-face book3d-spine"></div>
                            <div class="book3d-face book3d-edge"></div>
                            <div class="book3d-face book3d-top"></div>
                            <div class="book3d-face book3d-bottom"></div>
                        </div>
                        <!-- Book 3 - Pink -->
                        <div class="book3d" style="--book-color: #e8a0bf; --book-color-dark: #d18aaa; --book-pages: #faf5f8; --book-height: 30px; --book-width: 165px; --book-depth: 120px; transform: translateY(-70px) rotateY(-4deg);">
                            <div class="book3d-face book3d-front"></div>
                            <div class="book3d-face book3d-back"></div>
                            <div class="book3d-face book3d-spine"></div>
                            <div class="book3d-face book3d-edge"></div>
                            <div class="book3d-face book3d-top"></div>
                            <div class="book3d-face book3d-bottom"></div>
                        </div>
                        <!-- Book 2 - Red/Coral -->
                        <div class="book3d" style="--book-color: #e07a5f; --book-color-dark: #c4654a; --book-pages: #f5f0e8; --book-height: 28px; --book-width: 160px; --book-depth: 118px; transform: translateY(-100px) rotateY(3deg);">
                            <div class="book3d-face book3d-front"></div>
                            <div class="book3d-face book3d-back"></div>
                            <div class="book3d-face book3d-spine"></div>
                            <div class="book3d-face book3d-edge"></div>
                            <div class="book3d-face book3d-top"></div>
                            <div class="book3d-face book3d-bottom"></div>
                        </div>
                        <!-- Book 1 (top) - Blue -->
                        <div class="book3d" style="--book-color: #3d5a80; --book-color-dark: #2c4360; --book-pages: #f0ece4; --book-height: 32px; --book-width: 168px; --book-depth: 122px; transform: translateY(-128px) rotateY(-3deg);">
                            <div class="book3d-face book3d-front"></div>
                            <div class="book3d-face book3d-back"></div>
                            <div class="book3d-face book3d-spine"></div>
                            <div class="book3d-face book3d-edge"></div>
                            <div class="book3d-face book3d-top"></div>
                            <div class="book3d-face book3d-bottom"></div>
                        </div>
                    </div>
                    <div class="book3d-hint">
                        <i class="fas fa-hand-pointer"></i> Drag to rotate
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom Info Bar -->
    <section class="info-bar">
        <div class="info-bar-content">
            <div class="info-col">
                <span class="info-label">New Arrived</span>
                <h3>Have you chosen a good book?</h3>
            </div>
            <div class="info-divider"></div>
            <div class="info-col">
                <span class="info-label">Blog - 12/21</span>
                <h3>Where do you want to go today? Find it in a book.</h3>
            </div>
            <div class="info-divider"></div>
            <div class="info-col">
                <span class="info-label">Blog - 12/21</span>
                <h3>Give the gift of love - read to someone.</h3>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="features-container">
            <div class="section-header reveal">
                <h2>Mengapa Memilih Kami?</h2>
                <p>Kami memberikan pengalaman terbaik dalam berbelanja buku online</p>
            </div>

            <div class="features-grid">
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Pembayaran Aman</h3>
                    <p>Transaksi dijamin aman dengan berbagai metode pembayaran yang terpercaya dan terenkripsi.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3>Pengiriman Cepat</h3>
                    <p>Pengiriman ke seluruh Indonesia dengan berbagai pilihan kurir terpercaya.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3>Harga Terbaik</h3>
                    <p>Dapatkan harga terbaik dengan berbagai promo menarik dan diskon setiap hari.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>Koleksi Lengkap</h3>
                    <p>Ribuan judul buku dari berbagai kategori dan penulis terkenal.</p>
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
                @forelse($books ?? [] as $book)
                <div class="book-card reveal">
                    <img src="{{ $book->cover ?: 'https://via.placeholder.com/250x350?text=No+Cover' }}" 
                         alt="{{ $book->judul }}" 
                         class="book-cover"
                         onerror="this.src='https://via.placeholder.com/250x350?text=No+Cover'">
                    <div class="book-details">
                        @if($book->kategori)
                        <div class="book-category">{{ $book->kategori->nama_kategori }}</div>
                        @endif
                        <h3 class="book-title">{{ Str::limit($book->judul, 40) }}</h3>
                        <p class="book-author">{{ $book->penulis }}</p>
                        <div class="book-footer">
                            <div class="book-price">Rp {{ number_format($book->harga, 0, ',', '.') }}</div>
                            <button class="book-btn guest-cart-btn" onclick="handleGuestCart()">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="book-card reveal">
                    <img src="https://via.placeholder.com/250x350/a8d5a2/ffffff?text=Coming+Soon" alt="Book Cover" class="book-cover">
                    <div class="book-details">
                        <div class="book-category">Segera Hadir</div>
                        <h3 class="book-title">Koleksi Buku Terbaru</h3>
                        <p class="book-author">Tunggu Update Kami</p>
                        <div class="book-footer">
                            <div class="book-price">-</div>
                            <button class="book-btn guest-cart-btn" disabled>
                                <i class="fas fa-clock"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <div style="text-align: center; margin-top: 60px;">
                <a href="{{ route('register') }}" class="btn-start" style="font-size: 16px; padding: 16px 40px;">
                    <i class="fas fa-eye"></i> Lihat Semua Koleksi
                </a>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="about-section" id="about">
        <div class="about-container">
            <div class="section-header reveal">
                <h2>Tentang Kami</h2>
                <p>Kenali lebih dekat siapa kami dan apa yang kami tawarkan</p>
            </div>

            <!-- Row 1: Text Left, Visual Right -->
            <div class="about-row reveal">
                <div class="about-text">
                    <span class="about-badge"><i class="fas fa-book-open"></i> Siapa Kami</span>
                    <h3>Apa itu Book.com?</h3>
                    <p>Book.com adalah platform e-commerce yang menyediakan berbagai koleksi buku berkualitas dari berbagai genre dan kategori. Kami hadir untuk memudahkan Anda dalam menemukan dan membeli buku favorit dengan mudah dan cepat.</p>
                    <p>Misi kami adalah meningkatkan literasi dan memberikan kemudahan akses pada dunia pengetahuan di seluruh Indonesia dengan memanfaatkan teknologi.</p>
                </div>
                <div class="about-visual">
                    <div class="about-stats-grid">
                        <div class="about-stat">
                            <i class="fas fa-books"></i>
                            <h4>10,000+</h4>
                            <span>Koleksi Buku</span>
                        </div>
                        <div class="about-stat">
                            <i class="fas fa-users"></i>
                            <h4>50,000+</h4>
                            <span>Member Aktif</span>
                        </div>
                        <div class="about-stat">
                            <i class="fas fa-truck"></i>
                            <h4>100+</h4>
                            <span>Kota Terjangkau</span>
                        </div>
                        <div class="about-stat">
                            <i class="fas fa-star"></i>
                            <h4>4.9/5</h4>
                            <span>Rating Pelanggan</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Visual Left, Text Right -->
            <div class="about-row about-row-reverse reveal">
                <div class="about-visual">
                    <div class="about-social-card">
                        <h4><i class="fas fa-share-alt"></i> Ikuti Kami</h4>
                        <p>Dapatkan update terbaru dan promo menarik melalui media sosial kami</p>
                        <div class="about-social-links">
                            <a href="#" class="about-social-btn">
                                <i class="fab fa-facebook-f"></i>
                                <span>Facebook</span>
                            </a>
                            <a href="#" class="about-social-btn">
                                <i class="fab fa-instagram"></i>
                                <span>Instagram</span>
                            </a>
                            <a href="#" class="about-social-btn">
                                <i class="fab fa-twitter"></i>
                                <span>X (Twitter)</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="about-text">
                    <span class="about-badge"><i class="fas fa-headset"></i> Hubungi Kami</span>
                    <h3>Butuh Bantuan?</h3>
                    <p>Kenyamanan dan kepuasan pelanggan adalah prioritas utama kami. Tim Customer Service kami siap membantu Anda kapan saja.</p>
                    <div class="about-contact-info">
                        <div class="about-contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email</strong>
                                <span>info@book.com</span>
                            </div>
                        </div>
                        <div class="about-contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <strong>Telepon</strong>
                                <span>+62 812-3456-7890</span>
                            </div>
                        </div>
                        <div class="about-contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Lokasi</strong>
                                <span>Jakarta, Indonesia</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="about-cta reveal">
                <h3>Siap Memulai Petualangan Membaca?</h3>
                <p>Bergabunglah dengan ribuan pembaca lainnya dan dapatkan akses ke koleksi buku terlengkap.</p>
                <a href="{{ route('register') }}" class="btn-start">
                    <i class="fas fa-rocket"></i> Daftar Gratis Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer" id="contact">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Book.com</h3>
                <p>Platform buku online terpercaya dengan koleksi terlengkap dan harga terbaik.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>

            <div class="footer-section">
                <h3>Navigasi</h3>
                <ul>
                    <li><a href="#home">Book Types</a></li>
                    <li><a href="#books">Recommendations</a></li>
                    <li><a href="#features">Popular</a></li>
                    <li><a href="#about">About</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Layanan</h3>
                <ul>
                    <li><a href="#">Cara Berbelanja</a></li>
                    <li><a href="#">Metode Pembayaran</a></li>
                    <li><a href="#">Pengiriman</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Hubungi Kami</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> Jakarta, Indonesia</li>
                    <li><i class="fas fa-phone"></i> +62 812-3456-7890</li>
                    <li><i class="fas fa-envelope"></i> info@book.com</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Book.com. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/landing.js') }}?v={{ time() }}" defer></script>
    
    <script>
        function handleGuestCart() {
            window.location.href = "{{ route('login') }}";
        }
    </script>
</body>
</html>
