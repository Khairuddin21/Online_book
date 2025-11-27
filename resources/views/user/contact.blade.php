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
        </div>
        
        <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <h2 style="color: var(--user-primary); margin-bottom: 20px; text-align: center;">Kirim Pesan</h2>
            
            <form method="POST" action="#">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: #666; margin-bottom: 8px; font-weight: 500;">Nama</label>
                    <input type="text" name="nama" required
                           style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: #666; margin-bottom: 8px; font-weight: 500;">Email</label>
                    <input type="email" name="email" required
                           style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: #666; margin-bottom: 8px; font-weight: 500;">Subjek</label>
                    <input type="text" name="subjek" required
                           style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: #666; margin-bottom: 8px; font-weight: 500;">Pesan</label>
                    <textarea name="pesan" rows="6" required
                              style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; resize: vertical;"></textarea>
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
@endsection
