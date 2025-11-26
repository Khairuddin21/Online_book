@extends('auth.layout')

@section('title', 'Login')

@section('content')
<div class="auth-container">
    <a href="{{ route('home') }}" class="auth-close-btn" title="Kembali ke Beranda">
        <i class="fas fa-times"></i>
    </a>
    <div class="auth-wrapper">
        <!-- Left Side - Illustration -->
        <div class="auth-left">
        <div class="auth-brand">
            <a href="{{ route('home') }}">
                <i class="fas fa-book-reader"></i>
                <span>Toko Buku</span>
            </a>
        </div>
        
        <div class="auth-illustration">
            <img src="{{ asset('gambar/human-landing.png') }}" alt="Welcome Illustration">
            <div class="illustration-bg"></div>
        </div>
        
        <div class="auth-info">
            <h2>Selamat Datang Kembali!</h2>
            <p>Login untuk mengakses ribuan koleksi buku terbaik dan nikmati pengalaman membaca yang menyenangkan</p>
        </div>
        
        <div class="auth-social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-tiktok"></i></a>
        </div>
        
        <div class="auth-copyright">
            <p>© 2025 Toko Buku Online</p>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="auth-right">
        <div class="auth-form-container">
            <div class="auth-header">
                <h1>Masuk Akun</h1>
                <p>Silakan masukkan kredensial Anda untuk melanjutkan</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div class="alert-content">
                        <strong>Login gagal!</strong>
                        <ul class="error-list">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div class="alert-content">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="auth-form">
                @csrf
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="nama@email.com"
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Kata Sandi
                    </label>
                    <div class="password-input">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Masukkan kata sandi"
                            required
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="forgot-password">Lupa Kata Sandi?</a>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Masuk</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="auth-divider">
                <span>Atau</span>
            </div>

            <div class="social-login">
                <button type="button" class="btn-social btn-google">
                    <i class="fab fa-google"></i>
                    Masuk dengan Google
                </button>
            </div>

            <div class="auth-footer">
                <p>Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
            </div>
        </div>
    </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
@endsection
