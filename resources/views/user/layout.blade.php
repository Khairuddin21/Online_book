<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') - 6BUCKS.litera</title>
    
    <!-- Import Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet">
    
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{ asset('css/user/user.css') }}?v={{ time() }}">
    
    <!-- Icon Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body>
    <!-- Bagian Navbar -->
    <nav class="user-navbar" id="mainNavbar">
        <div class="navbar-container">
            <a href="{{ route('user.home') }}" class="navbar-logo">
                <i class="fas fa-book-open"></i>
                <h1>6BUCKS.litera</h1>
            </a>

            <ul class="navbar-menu" id="navMenu">
                <li><a href="{{ route('user.home') }}" class="{{ request()->routeIs('user.home') ? 'active' : '' }}">Beranda</a></li>
                <li><a href="{{ route('user.books') }}" class="{{ request()->routeIs('user.books') ? 'active' : '' }}">Katalog</a></li>
                @auth
                <li><a href="{{ route('user.orders') }}" class="{{ request()->routeIs('user.orders') ? 'active' : '' }}">Pesanan</a></li>
                <li>
                    <a href="{{ route('user.inbox') }}" class="{{ request()->routeIs('user.inbox') ? 'active' : '' }}">
                        Chat
                        @if(($inboxNotifCount ?? 0) > 0)
                            <span class="nav-badge">{{ $inboxNotifCount }}</span>
                        @endif
                    </a>
                </li>
                @endauth
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

    <!-- Konten Utama -->
    <main>
        @yield('content')
    </main>

    <!-- Bagian Footer -->
    <footer class="user-footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3><i class="fas fa-book-open"></i> 6BUCKS.litera</h3>
                <p>Toko buku online terpercaya dengan koleksi terlengkap. Temukan buku favoritmu dan nikmati pengalaman membaca yang tak terlupakan.</p>
            </div>

            <div class="footer-section">
                <h3>Navigasi</h3>
                <ul>
                    <li><a href="{{ route('user.home') }}">Beranda</a></li>
                    <li><a href="{{ route('user.books') }}">Katalog Buku</a></li>
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
                    <li><i class="fas fa-envelope"></i> info@6bucks.litera</li>
                    <li><i class="fas fa-map-marker-alt"></i> Jakarta, Indonesia</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} 6BUCKS.litera — All rights reserved.</p>
        </div>
    </footer>

    <!-- Script JS -->
    <script>
        window.APP_URL = '{{ rtrim(url('/'), '/') }}';
    </script>
    @auth
    {{-- Chat Notification Toast --}}
    <div id="chatToast" class="chat-toast" style="display:none;" onclick="window.location.href='{{ route('user.inbox') }}'">
        <div class="chat-toast-icon">
            <i class="fas fa-headset"></i>
        </div>
        <div class="chat-toast-body">
            <div class="chat-toast-header">
                <strong>Admin 6BUCKS.litera</strong>
                <span class="chat-toast-time" id="chatToastTime"></span>
            </div>
            <p class="chat-toast-text" id="chatToastText"></p>
        </div>
        <button class="chat-toast-close" onclick="event.stopPropagation(); closeChatToast();">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <style>
        .chat-toast {
            position: fixed;
            top: 80px;
            left: 24px;
            width: 360px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            border: 1px solid #e8e4de;
            padding: 14px 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            z-index: 9999;
            cursor: pointer;
            transform: translateX(-120%);
            opacity: 0;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
        }
        .chat-toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        .chat-toast:hover {
            box-shadow: 0 8px 36px rgba(0,0,0,0.16);
        }
        .chat-toast-icon {
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #a8d5a2, #6b9e65);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .chat-toast-body {
            flex: 1;
            min-width: 0;
        }
        .chat-toast-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3px;
        }
        .chat-toast-header strong {
            font-size: 13px;
            color: #2d2d2d;
        }
        .chat-toast-time {
            font-size: 11px;
            color: #9c9588;
        }
        .chat-toast-text {
            font-size: 13px;
            color: #5a5550;
            margin: 0;
            line-height: 1.4;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .chat-toast-close {
            background: none;
            border: none;
            color: #b8b0a4;
            font-size: 14px;
            cursor: pointer;
            padding: 2px 4px;
            margin-top: -2px;
            transition: color 0.2s;
        }
        .chat-toast-close:hover {
            color: #5a5550;
        }
        @media (max-width: 480px) {
            .chat-toast {
                left: 12px;
                right: 12px;
                width: auto;
            }
        }
    </style>

    <script>
    (function() {
        // Skip polling on the inbox page itself
        if (window.location.pathname.indexOf('/inbox') !== -1) return;

        var lastShownId = 0;
        var toastTimeout = null;
        var checkUrl = '{{ route("user.inbox.checkUnread") }}';

        function closeChatToast() {
            var toast = document.getElementById('chatToast');
            if (toast) toast.classList.remove('show');
        }
        window.closeChatToast = closeChatToast;

        function showToast(message) {
            var toast = document.getElementById('chatToast');
            var toastText = document.getElementById('chatToastText');
            var toastTime = document.getElementById('chatToastTime');
            if (!toast || !toastText) return;

            toastText.textContent = message.pesan;
            toastTime.textContent = message.waktu;

            toast.style.display = 'flex';
            // Trigger reflow for animation
            void toast.offsetWidth;
            toast.classList.add('show');

            // Auto-hide after 6 seconds
            if (toastTimeout) clearTimeout(toastTimeout);
            toastTimeout = setTimeout(function() {
                closeChatToast();
            }, 6000);
        }

        function updateBadge(count) {
            // Update the notification badge in the dropdown
            var pesanLink = document.querySelector('.dropdown a[href*="inbox"]');
            if (!pesanLink) return;
            var badge = pesanLink.querySelector('span');
            if (count > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.style.cssText = 'background:#ef4444;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:50px;margin-left:4px;';
                    pesanLink.appendChild(badge);
                }
                badge.textContent = count;
            } else if (badge) {
                badge.remove();
            }
        }

        function checkUnread() {
            fetch(checkUrl)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    updateBadge(data.unread_count);
                    if (data.latest && data.latest.id_chat > lastShownId) {
                        lastShownId = data.latest.id_chat;
                        showToast(data.latest);
                    }
                })
                .catch(function() {});
        }

        // Initial check after 1.5s (gives page time to load)
        setTimeout(checkUnread, 1500);
        // Then poll every 5 seconds
        setInterval(checkUnread, 5000);
    })();
    </script>
    @endauth

    <script src="{{ asset('js/user/user.js') }}?v={{ time() }}"></script>
    @stack('scripts')
</body>
</html>
