@extends('user.layout')

@section('title', 'Hubungi Kami')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <div class="page-header">
        <h1 class="page-title">Hubungi Kami</h1>
        <p class="page-subtitle">Punya pertanyaan? Kami siap membantu Anda</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="contact-wrapper">
        <!-- Info Cards -->
        <div class="contact-grid">
            <div class="contact-card">
                <i class="fas fa-phone"></i>
                <h3>Telepon</h3>
                <p>+62 123 4567 890</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <p>info@bookcom.id</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Alamat</h3>
                <p>Jakarta, Indonesia</p>
            </div>
            <div class="contact-card whatsapp-card">
                <i class="fab fa-whatsapp"></i>
                <h3>WhatsApp</h3>
                <p>+62 813 8139 1621</p>
                <a href="https://wa.me/6281381391621?text=Halo,%20saya%20ingin%20bertanya%20tentang%20buku" 
                   target="_blank"
                   class="btn btn-sm whatsapp-btn">
                    <i class="fab fa-whatsapp"></i> Chat WhatsApp
                </a>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-card">
            <h2 class="contact-form-title"><i class="fas fa-paper-plane"></i> Kirim Pesan</h2>
            
            <form method="POST" action="{{ route('user.contact.submit') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama <span class="required">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', Auth::user()->nama ?? '') }}" required
                           class="form-input {{ $errors->has('nama') ? 'is-invalid' : '' }}"
                           placeholder="Masukkan nama lengkap">
                    @error('nama')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email <span class="required">*</span></label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email ?? '') }}" required
                           class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           placeholder="contoh@email.com">
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Subjek <span class="required">*</span></label>
                    <input type="text" name="subjek" value="{{ old('subjek') }}" required
                           class="form-input {{ $errors->has('subjek') ? 'is-invalid' : '' }}"
                           placeholder="Subjek pesan Anda">
                    @error('subjek')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Pesan <span class="required">*</span></label>
                    <textarea name="pesan" rows="6" required
                              class="form-input {{ $errors->has('pesan') ? 'is-invalid' : '' }}"
                              placeholder="Tulis pesan Anda di sini...">{{ old('pesan') }}</textarea>
                    @error('pesan')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-green">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
