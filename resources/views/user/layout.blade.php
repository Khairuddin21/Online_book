<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') - Book.com</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/user/user.css') }}?v={{ time() }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="user-navbar" id="mainNavbar">
        <div class="navbar-container">
            <a href="{{ route('user.home') }}" class="navbar-logo">
                <i class="fas fa-book-open"></i>
                <h1>Book.com</h1>
            </a>

            <ul class="navbar-menu" id="navMenu">
                <li><a href="{{ route('user.home') }}" class="{{ request()->routeIs('user.home') ? 'active' : '' }}">Beranda</a></li>
                <li><a href="{{ route('user.books') }}" class="{{ request()->routeIs('user.books') ? 'active' : '' }}">Katalog</a></li>
                <li><a href="{{ route('user.categories') }}" class="{{ request()->routeIs('user.categories') ? 'active' : '' }}">Kategori</a></li>
                @auth
                <li><a href="{{ route('user.orders') }}" class="{{ request()->routeIs('user.orders') ? 'active' : '' }}">Pesanan</a></li>
                @endauth
                <li><a href="{{ route('user.contact') }}" class="{{ request()->routeIs('user.contact') ? 'active' : '' }}">Kontak</a></li>
            </ul>

            <div class="navbar-actions">
                <div class="navbar-search">
                    <input type="text" placeholder="Cari buku..." id="navSearchInput">
                    <button type="button" id="navSearchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                @auth
                <a href="{{ route('user.cart') }}" class="cart-icon">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-badge">0</span>
                </a>

                <div class="user-menu" aria-haspopup="true" aria-expanded="false">
                    <button class="btn btn-outline-green btn-sm" type="button" id="userMenuButton">
                        <i class="fas fa-user"></i> {{ Auth::user()->nama }}
                    </button>
                    <div class="dropdown" role="menu" aria-labelledby="userMenuButton">
                        <a href="{{ route('user.profile') }}"><i class="fas fa-user-circle"></i> Profil</a>
                        <a href="{{ route('user.orders') }}"><i class="fas fa-box"></i> Pesanan</a>
                        <a href="{{ route('user.inbox') }}"><i class="fas fa-envelope"></i> Pesan</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="btn btn-outline-green btn-sm">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="{{ route('register') }}" class="btn btn-green btn-sm">
                    <i class="fas fa-user-plus"></i> Daftar
                </a>
                @endauth

                <button class="mobile-menu-toggle" id="menuToggle" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Alerts -->
    @if(session('success'))
    <div class="user-container" style="padding-bottom:0;">
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="user-container" style="padding-bottom:0;">
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="user-footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3><i class="fas fa-book-open"></i> Book.com</h3>
                <p>Toko buku online terpercaya dengan koleksi terlengkap. Temukan buku favoritmu dan nikmati pengalaman membaca yang tak terlupakan.</p>
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
                <h3>Layanan</h3>
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
                    <li><i class="fas fa-envelope"></i> info@book.com</li>
                    <li><i class="fas fa-map-marker-alt"></i> Jakarta, Indonesia</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Book.com — All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/user/user.js') }}?v={{ time() }}"></script>
    @stack('scripts')
</body>
</html>
