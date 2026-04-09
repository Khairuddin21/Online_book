@extends('auth.layout')

@section('title', 'Register')

@section('content')
<div class="auth-container">
    <a href="{{ route('home') }}" class="auth-close-btn" title="Kembali ke Beranda">
        <i class="fas fa-times"></i>
    </a>
    <div class="auth-wrapper">
        <!-- Panel Kiri - Background Gradient -->
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

        <!-- Panel Kanan - Form Daftar -->
        <div class="auth-right">
            <div class="auth-form-container">
                <div class="auth-header">
                    <span class="logo-asterisk-dark">&#10035;</span>
                    <h1>Buat Akun Baru</h1>
                    <p>Akses tugas, catatan, dan proyek Anda kapan saja, di mana saja.</p>
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
                        <label for="nama">Nama Lengkap</label>
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
                        <label for="password">Kata Sandi</label>
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
                        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
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
    
    const caseReq = document.getElementById('req-case');
    if (/[A-Z]/.test(password) && /[a-z]/.test(password)) {
        caseReq.classList.remove('invalid');
        caseReq.classList.add('valid');
        caseReq.querySelector('i').className = 'fas fa-check-circle';
    } else {
        caseReq.classList.remove('valid');
        caseReq.classList.add('invalid');
        caseReq.querySelector('i').className = 'fas fa-times-circle';
    }
    
    const numberReq = document.getElementById('req-number');
    if (/[0-9]/.test(password) && /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
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
