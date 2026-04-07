@extends('user.layout')

@section('title', 'Pesanan Saya')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <div class="page-header">
        <h1 class="page-title">Pesanan Saya</h1>
        <p class="page-subtitle">Lacak dan kelola riwayat pesanan Anda</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom: 24px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if($orders->count() > 0)
        <div class="orders-list">
            @foreach($orders as $order)
            <div class="order-card">
                <!-- Order Header -->
                <div class="order-card-header">
                    <div class="order-meta">
                        <span class="order-id">#{{ $order->id_pesanan }}</span>
                        <span class="order-date">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $order->tanggal_pesanan ? $order->tanggal_pesanan->format('d M Y, H:i') : '-' }}
                        </span>
                    </div>
                    <div class="order-status-badge status-{{ $order->status }}">
                        @switch($order->status)
                            @case('menunggu')
                                <i class="fas fa-clock"></i> Menunggu Pembayaran
                                @break
                            @case('diproses')
                                <i class="fas fa-box"></i> Sedang Dikemas
                                @break
                            @case('dikirim')
                                <i class="fas fa-truck"></i> Dikirim
                                @break
                            @case('selesai')
                                <i class="fas fa-check-circle"></i> Selesai
                                @break
                            @case('dibatalkan')
                                <i class="fas fa-times-circle"></i> Dibatalkan
                                @break
                            @default
                                {{ $order->status }}
                        @endswitch
                    </div>
                </div>

                <!-- Order Items -->
                <div class="order-items">
                    @foreach($order->details->take(3) as $detail)
                    <div class="order-item-mini">
                        <div class="order-item-mini-img">
                            @if($detail->buku && $detail->buku->cover)
                                <img src="{{ Str::startsWith($detail->buku->cover, 'http') ? $detail->buku->cover : asset('storage/' . $detail->buku->cover) }}"
                                     alt="{{ $detail->buku->judul }}"
                                     onerror="this.src='https://via.placeholder.com/50x65?text=Buku'">
                            @else
                                <div class="order-item-mini-placeholder"><i class="fas fa-book"></i></div>
                            @endif
                        </div>
                        <div class="order-item-mini-info">
                            <span class="order-item-mini-title">{{ $detail->buku->judul ?? 'Buku' }}</span>
                            <span class="order-item-mini-qty">{{ $detail->qty }}x — Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                    @if($order->details->count() > 3)
                        <p class="order-more-items">+{{ $order->details->count() - 3 }} buku lainnya</p>
                    @endif
                </div>

                <!-- Order Footer -->
                <div class="order-card-footer">
                    <div class="order-total">
                        <span>Total:</span>
                        <strong>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong>
                    </div>
                    <div class="order-actions">
                        @if($order->status === 'menunggu')
                            <a href="{{ route('user.payment', $order->id_pesanan) }}" class="btn-order-action btn-pay">
                                <i class="fas fa-credit-card"></i> Bayar Sekarang
                            </a>
                            <form action="{{ route('user.order.cancel', $order->id_pesanan) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin membatalkan pesanan #{{ $order->id_pesanan }}?')">
                                @csrf
                                <button type="submit" class="btn-order-action btn-cancel">
                                    <i class="fas fa-times"></i> Batalkan
                                </button>
                            </form>
                        @endif
                        @if($order->status === 'diproses')
                            <span class="order-info-note">
                                <i class="fas fa-info-circle"></i> Pesanan sedang dikemas
                            </span>
                        @endif
                        @if($order->status === 'dikirim')
                            <span class="order-info-note">
                                <i class="fas fa-truck"></i> Dalam perjalanan
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="orders-empty">
            <i class="fas fa-box-open"></i>
            <h2>Belum Ada Pesanan</h2>
            <p>Anda belum memiliki riwayat pesanan</p>
            <a href="{{ route('user.books') }}" class="btn btn-green">
                <i class="fas fa-shopping-bag"></i> Mulai Berbelanja
            </a>
        </div>
    @endif
</div>

@push('styles')
<style>
.orders-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.order-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    overflow: hidden;
    transition: box-shadow 0.3s;
}

.order-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.order-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    background: #f9fafb;
    border-bottom: 1px solid #f0f0f0;
    flex-wrap: wrap;
    gap: 10px;
}

.order-meta {
    display: flex;
    align-items: center;
    gap: 16px;
}

.order-id {
    font-weight: 700;
    font-size: 16px;
    color: var(--green-dark, #6b9e65);
}

.order-date {
    font-size: 13px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 5px;
}

.order-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.status-menunggu {
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #fed7aa;
}

.status-diproses {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

.status-dikirim {
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

.status-selesai {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #86efac;
}

.status-dibatalkan {
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
}

.order-items {
    padding: 16px 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.order-item-mini {
    display: flex;
    align-items: center;
    gap: 12px;
}

.order-item-mini-img {
    width: 50px;
    height: 65px;
    border-radius: 6px;
    overflow: hidden;
    flex-shrink: 0;
}

.order-item-mini-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.order-item-mini-placeholder {
    width: 100%;
    height: 100%;
    background: var(--green-light, #d4edda);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--green-dark, #6b9e65);
    font-size: 18px;
}

.order-item-mini-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.order-item-mini-title {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
}

.order-item-mini-qty {
    font-size: 13px;
    color: #6b7280;
}

.order-more-items {
    font-size: 13px;
    color: var(--green-dark, #6b9e65);
    font-weight: 500;
    padding-left: 4px;
}

.order-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    border-top: 1px solid #f0f0f0;
    flex-wrap: wrap;
    gap: 12px;
}

.order-total {
    font-size: 15px;
    color: #374151;
    display: flex;
    gap: 8px;
    align-items: center;
}

.order-total strong {
    font-size: 18px;
    font-weight: 700;
    color: var(--green-dark, #6b9e65);
}

.order-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.btn-order-action {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-pay {
    background: linear-gradient(135deg, var(--green-dark, #6b9e65), var(--green-deeper, #4a7c44));
    color: white;
    box-shadow: 0 3px 10px rgba(74, 124, 68, 0.3);
}

.btn-pay:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(74, 124, 68, 0.4);
}

.btn-cancel {
    background: white;
    color: #dc2626;
    border: 1.5px solid #fca5a5;
    font-family: inherit;
}

.btn-cancel:hover {
    background: #fef2f2;
    border-color: #dc2626;
}

.order-info-note {
    font-size: 13px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 6px;
}

.orders-empty {
    text-align: center;
    padding: 80px 20px;
    color: #6b7280;
}

.orders-empty i {
    font-size: 64px;
    color: #d1d5db;
    margin-bottom: 20px;
    display: block;
}

.orders-empty h2 {
    font-size: 24px;
    font-weight: 700;
    color: #374151;
    margin-bottom: 10px;
}

.orders-empty p {
    font-size: 15px;
    margin-bottom: 24px;
}

@media (max-width: 600px) {
    .order-card-header,
    .order-card-footer {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endpush
@endsection
