@extends('auth.layout')

@section('title', 'Register')

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
                <img src="{{ asset('gambar/double-human.png') }}" alt="Register Illustration">
                <div class="illustration-bg"></div>
            </div>
            
            <div class="auth-info">
                <h2>Bergabung Bersama Kami!</h2>
                <p>Daftar sekarang untuk mengakses ribuan koleksi buku terbaik dan nikmati berbagai promo menarik khusus member baru</p>
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

        <!-- Right Side - Register Form -->
        <div class="auth-right">
            <div class="auth-form-container">
                <div class="auth-header">
                    <h1>Daftar Akun</h1>
                    <p>Isi formulir di bawah untuk membuat akun baru</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <div class="alert-content">
                            <strong>Registrasi gagal!</strong>
                            <ul class="error-list">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST" class="auth-form">
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
                        <label for="nama">
                            <i class="fas fa-user"></i>
                            Nama Lengkap
                        </label>
                        <input 
                            type="text" 
                            id="nama" 
                            name="nama" 
                            placeholder="Masukkan nama lengkap"
                            value="{{ old('nama') }}" 
                            required
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
                                placeholder="Minimal 8 karakter"
                                required
                                oninput="validatePassword()"
                            >
                            <button type="button" class="toggle-password" onclick="togglePasswordRegister('password')">
                                <i class="fas fa-eye" id="toggleIconPassword"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">
                            <i class="fas fa-lock"></i>
                            Konfirmasi Kata Sandi
                        </label>
                        <div class="password-input">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                placeholder="Ulangi kata sandi"
                                required
                            >
                            <button type="button" class="toggle-password" onclick="togglePasswordRegister('password_confirmation')">
                                <i class="fas fa-eye" id="toggleIconPasswordConfirmation"></i>
                            </button>
                        </div>
                    </div>

                    <div class="password-requirements">
                        <p><i class="fas fa-info-circle"></i> Persyaratan Kata Sandi:</p>
                        <ul>
                            <li id="req-length" class="invalid">
                                <i class="fas fa-times-circle"></i> 
                                <span>Minimum 8 karakter</span>
                            </li>
                            <li id="req-case" class="invalid">
                                <i class="fas fa-times-circle"></i> 
                                <span>Sertakan huruf kapital & non-kapital</span>
                            </li>
                            <li id="req-number" class="invalid">
                                <i class="fas fa-times-circle"></i> 
                                <span>Sertakan angka & simbol</span>
                            </li>
                        </ul>
                    </div>

                    <div class="form-checkbox">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" required>
                            <span>Dengan mendaftar, kamu menyetujui <a href="#" class="terms-link">Syarat & Ketentuan</a> dan <a href="#" class="terms-link">Kebijakan Privasi</a></span>
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>Daftar</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordRegister(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const iconId = fieldId === 'password' ? 'toggleIconPassword' : 'toggleIconPasswordConfirmation';
    const toggleIcon = document.getElementById(iconId);
    
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

function validatePassword() {
    const password = document.getElementById('password').value;
    
    // Check length (minimum 8 characters)
    const lengthReq = document.getElementById('req-length');
    if (password.length >= 8) {
        lengthReq.classList.remove('invalid');
        lengthReq.classList.add('valid');
        lengthReq.querySelector('i').className = 'fas fa-check-circle';
    } else {
        lengthReq.classList.remove('valid');
        lengthReq.classList.add('invalid');
        lengthReq.querySelector('i').className = 'fas fa-times-circle';
    }
    
    // Check uppercase and lowercase
    const caseReq = document.getElementById('req-case');
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    if (hasUpperCase && hasLowerCase) {
        caseReq.classList.remove('invalid');
        caseReq.classList.add('valid');
        caseReq.querySelector('i').className = 'fas fa-check-circle';
    } else {
        caseReq.classList.remove('valid');
        caseReq.classList.add('invalid');
        caseReq.querySelector('i').className = 'fas fa-times-circle';
    }
    
    // Check number and symbol
    const numberReq = document.getElementById('req-number');
    const hasNumber = /[0-9]/.test(password);
    const hasSymbol = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
    if (hasNumber && hasSymbol) {
        numberReq.classList.remove('invalid');
        numberReq.classList.add('valid');
        numberReq.querySelector('i').className = 'fas fa-check-circle';
    } else {
        numberReq.classList.remove('valid');
        numberReq.classList.add('invalid');
        numberReq.querySelector('i').className = 'fas fa-times-circle';
    }
}
</script>
@endsection
