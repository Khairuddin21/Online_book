@extends('user.layout')

@section('title', 'Pesanan Saya')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <h1 class="section-title">Pesanan Saya</h1>
    
    <div style="text-align: center; padding: 80px 20px;">
        <i class="fas fa-box" style="font-size: 80px; color: var(--user-accent); margin-bottom: 20px;"></i>
        <h2 style="color: var(--user-primary); margin-bottom: 15px;">Belum Ada Pesanan</h2>
        <p style="color: #666; margin-bottom: 30px;">Anda belum memiliki riwayat pesanan</p>
        <a href="{{ route('user.books') }}" class="btn btn-primary" style="padding: 12px 30px;">
            <i class="fas fa-shopping-bag"></i> Mulai Berbelanja
        </a>
    </div>
</div>
@endsection
