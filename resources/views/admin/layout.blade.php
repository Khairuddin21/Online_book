<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Book.com</title>

    <!-- Import Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Icon Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Style Admin -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}?v={{ time() }}">

    @stack('styles')
</head>
<body>
    <!-- Overlay Sidebar buat HP -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar Menu -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="logo">
            <h2><i class="fas fa-book-open"></i> Book.com</h2>
        </div>

        <nav>
            <ul>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                </li>

                <li class="sidebar-label">Manajemen Buku</li>
                <li>
                    <a href="{{ route('admin.kategori.index') }}" class="{{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i> Kategori Buku
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.buku.index') }}" class="{{ request()->routeIs('admin.buku.*') ? 'active' : '' }}">
                        <i class="fas fa-book-open"></i> Kelola Buku
                    </a>
                </li>

                <li class="sidebar-label">Transaksi</li>
                <li>
                    <a href="{{ route('admin.pesanan.index') }}" class="{{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-bag"></i> Pesanan
                    </a>
                </li>

                <li class="sidebar-label">Pengguna</li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Pengguna
                    </a>
                </li>

                <li class="sidebar-label">Komunikasi</li>
                <li>
                    <a href="{{ route('admin.pesan.index') }}" class="{{ request()->routeIs('admin.pesan.*') ? 'active' : '' }}">
                        <i class="fas fa-comments"></i> Chat
                        @if(($adminChatNotifCount ?? 0) > 0)
                            <span style="background:#ef4444;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:50px;margin-left:6px;">{{ $adminChatNotifCount }}</span>
                        @endif
                    </a>
                </li>

                <li class="sidebar-label">Laporan</li>
                <li>
                    <a href="{{ route('admin.laporan') }}" class="{{ request()->routeIs('admin.laporan') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i> Laporan Bulanan
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Konten Utama -->
    <main class="admin-main">
        <!-- Bagian Header -->
        <header class="admin-header">
            <div style="display: flex; align-items: center; gap: 12px;">
                <button class="mobile-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="user-info">
                <span>{{ Auth::user()->nama }}</span>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&background=6b9e65&color=fff&rounded=true&size=38" alt="Avatar">
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-logout btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Isi Konten -->
        <div class="admin-content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Script JS Admin -->
    <script src="{{ asset('js/admin/admin.js') }}?v={{ time() }}"></script>
    @stack('scripts')
</body>
</html>
