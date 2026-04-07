@extends('admin.layout')

@section('title', 'Kelola Pesanan')
@section('page-title', 'Pesanan')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: var(--text-dark);">Daftar Pesanan</h2>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">{{ $pesanan->total() }} pesanan ditemukan</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form action="{{ route('admin.pesanan.index') }}" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID pesanan, nama, email..." style="flex: 1; min-width: 200px;">
        <select name="status" style="min-width: 170px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
            <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
            <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-ghost btn-sm"><i class="fas fa-times"></i> Reset</a>
        @endif
    </form>
</div>

<!-- Orders Table -->
<div class="data-table">
    @if($pesanan->count() > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th style="text-align: right;">Total</th>
                <th style="text-align: center;">Pembayaran</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesanan as $order)
            <tr>
                <td><span style="font-weight: 700; color: var(--text-muted);">#{{ $order->id_pesanan }}</span></td>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="avatar" style="width: 34px; height: 34px; font-size: 13px;">{{ strtoupper(substr($order->user->nama ?? 'U', 0, 1)) }}</div>
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: var(--text-dark);">{{ $order->user->nama ?? '-' }}</div>
                            <div style="font-size: 12px; color: var(--text-muted);">{{ $order->user->email ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span style="font-size: 13px; color: var(--text-dark);">{{ $order->tanggal_pesanan ? $order->tanggal_pesanan->format('d M Y') : '-' }}</span>
                    <div style="font-size: 11px; color: var(--text-light);">{{ $order->tanggal_pesanan ? $order->tanggal_pesanan->format('H:i') : '' }}</div>
                </td>
                <td style="text-align: right;">
                    <span style="font-weight: 700; color: var(--green-deeper);">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </td>
                <td style="text-align: center;">
                    @if($order->metode_pembayaran === 'cod')
                        <span class="badge badge-yellow" style="font-size:10px; margin-bottom:4px; display:inline-block;"><i class="fas fa-money-bill-wave"></i> COD</span><br>
                    @endif
                    @if($order->pembayaran)
                        @if($order->pembayaran->status_verifikasi == 'valid')
                            <span class="badge badge-green"><i class="fas fa-check"></i> Valid</span>
                        @elseif($order->pembayaran->status_verifikasi == 'menunggu')
                            <span class="badge badge-yellow"><i class="fas fa-clock"></i> Menunggu</span>
                        @else
                            <span class="badge badge-red"><i class="fas fa-times"></i> {{ ucfirst($order->pembayaran->status_verifikasi) }}</span>
                        @endif
                    @else
                        <span class="badge badge-gray">Belum Bayar</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <span class="badge status-{{ $order->status }}">
                        @switch($order->status)
                            @case('menunggu') <i class="fas fa-clock"></i> @break
                            @case('diproses') <i class="fas fa-spinner"></i> @break
                            @case('dikirim') <i class="fas fa-truck"></i> @break
                            @case('selesai') <i class="fas fa-check-circle"></i> @break
                            @case('dibatalkan') <i class="fas fa-times-circle"></i> @break
                        @endswitch
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td style="text-align: center;">
                    <div style="display: flex; justify-content: center; gap: 6px;">
                        <a href="{{ route('admin.pesanan.show', $order->id_pesanan) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                        <form action="{{ route('admin.pesanan.delete', $order->id_pesanan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan #{{ $order->id_pesanan }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    @if($pesanan->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination">
            {{ $pesanan->appends(request()->query())->links('pagination::simple-bootstrap-4') }}
        </div>
    </div>
    @endif

    @else
    <div class="empty-state">
        <i class="fas fa-shopping-bag"></i>
        <p>Belum ada pesanan ditemukan.</p>
    </div>
    @endif
</div>
@endsection
