@extends('user.layout')

@section('title', 'Profil Saya')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <div class="page-header">
        <h1 class="page-title">Profil Saya</h1>
        <p class="page-subtitle">Informasi akun dan data diri Anda</p>
    </div>

    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
            </div>
            <h2 class="profile-name">{{ Auth::user()->nama }}</h2>
            <p class="profile-email">{{ Auth::user()->email }}</p>
        </div>
        
        <div class="profile-body">
            <div class="profile-item">
                <label><i class="fas fa-user"></i> Nama Lengkap</label>
                <p>{{ Auth::user()->nama }}</p>
            </div>
            <div class="profile-item">
                <label><i class="fas fa-envelope"></i> Email</label>
                <p>{{ Auth::user()->email }}</p>
            </div>
            <div class="profile-item">
                <label><i class="fas fa-phone"></i> No. Telepon</label>
                <p>{{ Auth::user()->no_telp ?? '-' }}</p>
            </div>
            <div class="profile-item">
                <label><i class="fas fa-map-marker-alt"></i> Alamat</label>
                <p>{{ Auth::user()->alamat ?? '-' }}</p>
            </div>
            
            <div class="form-actions">
                <button class="btn btn-green">
                    <i class="fas fa-edit"></i> Edit Profil
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
