@extends('user.layout')

@section('title', 'Profil Saya')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <h1 class="section-title">Profil Saya</h1>
    
    <div style="max-width: 600px; margin: 40px auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--user-accent); color: white; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 15px; font-weight: bold;">
                {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
            </div>
            <h2 style="color: var(--user-primary); margin-bottom: 5px;">{{ Auth::user()->nama }}</h2>
            <p style="color: #999;">{{ Auth::user()->email }}</p>
        </div>
        
        <div style="border-top: 1px solid #eee; padding-top: 30px;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; color: #666; margin-bottom: 8px; font-weight: 500;">
                    <i class="fas fa-user"></i> Nama Lengkap
                </label>
                <p style="color: var(--user-dark); font-size: 16px;">{{ Auth::user()->nama }}</p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; color: #666; margin-bottom: 8px; font-weight: 500;">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <p style="color: var(--user-dark); font-size: 16px;">{{ Auth::user()->email }}</p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; color: #666; margin-bottom: 8px; font-weight: 500;">
                    <i class="fas fa-phone"></i> No. Telepon
                </label>
                <p style="color: var(--user-dark); font-size: 16px;">{{ Auth::user()->no_telp ?? '-' }}</p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; color: #666; margin-bottom: 8px; font-weight: 500;">
                    <i class="fas fa-map-marker-alt"></i> Alamat
                </label>
                <p style="color: var(--user-dark); font-size: 16px;">{{ Auth::user()->alamat ?? '-' }}</p>
            </div>
            
            <div style="margin-top: 30px; text-align: center;">
                <button class="btn btn-primary" style="padding: 12px 30px;">
                    <i class="fas fa-edit"></i> Edit Profil
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
