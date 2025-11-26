@extends('admin.layout')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-info">
            <h3>{{ $totalBuku ?? 0 }}</h3>
            <p>Total Buku</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-book"></i>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-info">
            <h3>{{ $totalPesanan ?? 0 }}</h3>
            <p>Total Pesanan</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-info">
            <h3>{{ $pesananMenunggu ?? 0 }}</h3>
            <p>Pesanan Menunggu</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
    </div>

    <div class="stat-card danger">
        <div class="stat-info">
            <h3>Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h3>
            <p>Total Pendapatan</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-money-bill-wave"></i>
        </div>
    </div>
</div>

<div class="data-table">
    <h2>Pesanan Terbaru</h2>
    <table>
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesananTerbaru ?? [] as $pesanan)
            <tr>
                <td>#{{ $pesanan->id_pesanan }}</td>
                <td>{{ $pesanan->user->nama }}</td>
                <td>{{ $pesanan->tanggal_pesanan->format('d/m/Y H:i') }}</td>
                <td>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                <td>
                    <span class="badge badge-{{ $pesanan->status }}">
                        {{ ucfirst($pesanan->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" class="btn btn-primary btn-sm">
                        Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Belum ada pesanan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
