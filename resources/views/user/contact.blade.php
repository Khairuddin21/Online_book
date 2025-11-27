@extends('user.layout')

@section('title', 'Hubungi Kami')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <h1 class="section-title" style="text-align: center;">Hubungi Kami</h1>
    
    <div style="max-width: 800px; margin: 40px auto;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 50px;">
            <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                <i class="fas fa-phone" style="font-size: 40px; color: var(--user-accent); margin-bottom: 15px;"></i>
                <h3 style="color: var(--user-primary); margin-bottom: 10px;">Telepon</h3>
                <p style="color: #666;">+62 123 4567 890</p>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                <i class="fas fa-envelope" style="font-size: 40px; color: var(--user-accent); margin-bottom: 15px;"></i>
                <h3 style="color: var(--user-primary); margin-bottom: 10px;">Email</h3>
                <p style="color: #666;">info@tokobuku.com</p>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                <i class="fas fa-map-marker-alt" style="font-size: 40px; color: var(--user-accent); margin-bottom: 15px;"></i>
                <h3 style="color: var(--user-primary); margin-bottom: 10px;">Alamat</h3>
                <p style="color: #666;">Jakarta, Indonesia</p>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                <i class="fab fa-whatsapp" style="font-size: 40px; color: #25D366; margin-bottom: 15px;"></i>
                <h3 style="color: var(--user-primary); margin-bottom: 10px;">WhatsApp</h3>
                <p style="color: #666; margin-bottom: 15px;">+62 813 8139 1621</p>
                <a href="https://wa.me/6281381391621?text=Halo,%20saya%20ingin%20bertanya%20tentang%20buku" 
                   target="_blank"
                   class="btn btn-primary" 
                   style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; font-size: 14px; background: #25D366; border: none; text-decoration: none;">
                    <i class="fab fa-whatsapp"></i> Chat WhatsApp
                </a>
            </div>
        </div>
        
        <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <h2 style="color: var(--user-primary); margin-bottom: 20px; text-align: center;">Kirim Pesan</h2>
            
            <form method="POST" action="{{ route('user.contact.submit') }}">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: #666; margin-bottom: 8px; font-weight: 500;">Nama <span style="color: red;">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', Auth::user()->nama ?? '') }}" required
                           style="width: 100%; padding: 12px 15px; border: 1px solid {{ $errors->has('nama') ? '#e74c3c' : '#ddd' }}; border-radius: 8px; font-size: 15px;">
                    @error('nama')
                        <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: #666; margin-bottom: 8px; font-weight: 500;">Email <span style="color: red;">*</span></label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email ?? '') }}" required
                           style="width: 100%; padding: 12px 15px; border: 1px solid {{ $errors->has('email') ? '#e74c3c' : '#ddd' }}; border-radius: 8px; font-size: 15px;">
                    @error('email')
                        <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: #666; margin-bottom: 8px; font-weight: 500;">Subjek <span style="color: red;">*</span></label>
                    <input type="text" name="subjek" value="{{ old('subjek') }}" required
                           style="width: 100%; padding: 12px 15px; border: 1px solid {{ $errors->has('subjek') ? '#e74c3c' : '#ddd' }}; border-radius: 8px; font-size: 15px;">
                    @error('subjek')
                        <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: #666; margin-bottom: 8px; font-weight: 500;">Pesan <span style="color: red;">*</span></label>
                    <textarea name="pesan" rows="6" required
                              style="width: 100%; padding: 12px 15px; border: 1px solid {{ $errors->has('pesan') ? '#e74c3c' : '#ddd' }}; border-radius: 8px; font-size: 15px; resize: vertical;">{{ old('pesan') }}</textarea>
                    @error('pesan')
                        <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div style="text-align: center;">
                    <button type="submit" class="btn btn-primary" style="padding: 12px 40px; font-size: 16px;">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Show toast notification if there's a success or error message
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif
});

function showToast(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.toast-notification').forEach(t => t.remove());
    
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    const colors = {
        success: '#2ecc71',
        error: '#e74c3c',
        warning: '#f39c12',
        info: '#3498db'
    };
    
    toast.innerHTML = `
        <i class="fas ${icons[type] || icons.success}"></i>
        <span>${message}</span>
    `;
    
    toast.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        padding: 15px 25px;
        background: ${colors[type] || colors.success};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        animation: slideInRight 0.3s ease;
        max-width: 350px;
    `;
    
    document.body.appendChild(toast);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// Add animation styles if not already present
if (!document.getElementById('toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100px);
            }
        }
    `;
    document.head.appendChild(style);
}
</script>
@endpush
@endsection
