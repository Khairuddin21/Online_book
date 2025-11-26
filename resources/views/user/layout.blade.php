<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') - Toko Buku Online</title>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/user/user.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="user-navbar">
        <div class="navbar-container">
            <div class="navbar-logo">
                <i class="fas fa-book-reader"></i>
                <h1>Toko Buku</h1>
            </div>

            <ul class="navbar-menu">
                <li><a href="{{ route('user.home') }}" class="{{ request()->routeIs('user.home') ? 'active' : '' }}">Beranda</a></li>
                <li><a href="{{ route('user.books') }}" class="{{ request()->routeIs('user.books') ? 'active' : '' }}">Katalog Buku</a></li>
                <li><a href="{{ route('user.categories') }}" class="{{ request()->routeIs('user.categories') ? 'active' : '' }}">Kategori</a></li>
                @auth
                <li><a href="{{ route('user.orders') }}" class="{{ request()->routeIs('user.orders') ? 'active' : '' }}">Pesanan Saya</a></li>
                @endauth
                <li><a href="{{ route('user.contact') }}" class="{{ request()->routeIs('user.contact') ? 'active' : '' }}">Kontak</a></li>
            </ul>

            <div class="navbar-actions">
                <div class="navbar-search">
                    <input type="text" placeholder="Cari buku...">
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                @auth
                <a href="{{ route('user.cart') }}" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">0</span>
                </a>

                <div class="user-menu">
                    <button class="btn btn-outline">
                        <i class="fas fa-user"></i> {{ Auth::user()->nama }}
                    </button>
                    <div class="dropdown">
                        <a href="{{ route('user.profile') }}">Profil</a>
                        <a href="{{ route('user.orders') }}">Pesanan</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="btn btn-outline">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Daftar
                </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="user-footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>Tentang Kami</h3>
                <p>Toko Buku Online terpercaya dengan koleksi buku terlengkap dan harga terbaik.</p>
            </div>

            <div class="footer-section">
                <h3>Navigasi</h3>
                <ul>
                    <li><a href="{{ route('user.home') }}">Beranda</a></li>
                    <li><a href="{{ route('user.books') }}">Katalog Buku</a></li>
                    <li><a href="{{ route('user.categories') }}">Kategori</a></li>
                    <li><a href="{{ route('user.contact') }}">Kontak</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Layanan Pelanggan</h3>
                <ul>
                    <li><a href="#">Cara Berbelanja</a></li>
                    <li><a href="#">Metode Pembayaran</a></li>
                    <li><a href="#">Pengiriman</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Hubungi Kami</h3>
                <ul>
                    <li><i class="fas fa-phone"></i> +62 123 4567 890</li>
                    <li><i class="fas fa-envelope"></i> info@tokobuku.com</li>
                    <li><i class="fas fa-map-marker-alt"></i> Jakarta, Indonesia</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Toko Buku Online. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/user/user.js') }}"></script>
    @stack('scripts')
</body>
</html>
