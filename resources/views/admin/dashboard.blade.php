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
            <h3>{{ $pesananDibatalkan ?? 0 }}</h3>
            <p>Pesanan Dibatalkan</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-times-circle"></i>
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
    
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    
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
                    @if($pesanan->status == 'menunggu')
                        <span style="background: #ffc107; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Belum Bayar</span>
                    @elseif($pesanan->status == 'diproses')
                        <span style="background: #17a2b8; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Diproses</span>
                    @elseif($pesanan->status == 'dikirim')
                        <span style="background: #007bff; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Dikirim</span>
                    @elseif($pesanan->status == 'selesai')
                        <span style="background: #28a745; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Selesai</span>
                    @else
                        <span style="background: #dc3545; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Dibatalkan</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" 
                           class="btn btn-primary btn-sm"
                           style="background: #007bff; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                        <form action="{{ route('admin.pesanan.delete', $pesanan->id_pesanan) }}" 
                              method="POST" 
                              style="display: inline;"
                              onsubmit="return confirm('Yakin ingin menghapus pesanan #{{ $pesanan->id_pesanan }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-danger btn-sm"
                                    style="background: #dc3545; color: white; padding: 6px 12px; border-radius: 5px; border: none; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 10px; opacity: 0.3; display: block;"></i>
                    Belum ada pesanan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
