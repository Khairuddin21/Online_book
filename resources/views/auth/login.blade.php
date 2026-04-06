@extends('auth.layout')

@section('title', 'Login')

@section('content')
<div class="auth-container">
    <a href="{{ route('home') }}" class="auth-close-btn" title="Kembali ke Beranda">
        <i class="fas fa-times"></i>
    </a>
    <div class="auth-wrapper">
        <!-- Left Panel - Gradient -->
        <div class="auth-left">
            <div class="auth-left-content">
                <div class="auth-logo-white">
                    <span class="logo-asterisk">&#10035;</span>
                </div>
                <div class="auth-left-bottom">
                    <p class="auth-left-subtitle">Anda dapat dengan mudah</p>
                    <h2 class="auth-left-title">Pusat Pribadi Anda untuk Kejelasan dan Produktivitas</h2>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="auth-right">
            <div class="auth-form-container">
                <div class="auth-header">
                    <span class="logo-asterisk-dark">&#10035;</span>
                    <h1>Masuk ke Akun Anda</h1>
                    <p>Akses hub pribadi Anda untuk kejelasan dan produktivitas.</p>
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
                        <div class="alert-content">{{ session('success') }}</div>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="auth-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email">Email Anda</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="namaanda@email.com"
                            value="{{ old('email') }}" 
                            required 
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
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
                        <span>Mulai Sekarang</span>
                    </button>
                </form>

                <div class="auth-divider">
                    <span>atau lanjutkan dengan</span>
                </div>

                <div class="social-login">
                    <a href="{{ route('auth.google') }}" class="btn-social">
                        <i class="fab fa-google"></i>
                    </a>
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
