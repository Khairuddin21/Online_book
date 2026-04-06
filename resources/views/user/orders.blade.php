@extends('user.layout')

@section('title', 'Pesanan Saya')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <div class="page-header">
        <h1 class="page-title">Pesanan Saya</h1>
        <p class="page-subtitle">Lacak dan kelola riwayat pesanan Anda</p>
    </div>

    <div class="orders-empty">
        <i class="fas fa-box-open"></i>
        <h2>Belum Ada Pesanan</h2>
        <p>Anda belum memiliki riwayat pesanan</p>
        <a href="{{ route('user.books') }}" class="btn btn-green">
            <i class="fas fa-shopping-bag"></i> Mulai Berbelanja
        </a>
    </div>
</div>
@endsection
