@extends('user.layout')

@section('title', 'Pembayaran')

@section('content')
<div class="user-container" style="min-height: 60vh; padding: 40px 20px;">
    <h1 class="section-title" style="text-align: center; margin-bottom: 40px;">Pembayaran</h1>
    
    <!-- Progress Steps -->
    <div class="checkout-steps">
        <div class="step completed">
            <div class="step-number">
                <i class="fas fa-check"></i>
            </div>
            <div class="step-label">Pengiriman</div>
        </div>
        <div class="step-line active"></div>
        <div class="step active">
            <div class="step-number">2</div>
            <div class="step-label">Pembayaran</div>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success" style="margin: 20px 0;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-error" style="margin: 20px 0;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    
    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 30px; margin-top: 40px; align-items: start;">
        <!-- Order Details -->
        <div>
            <div class="payment-section">
                <h3 class="payment-section-title">
                    <i class="fas fa-shopping-bag"></i> Detail Pesanan
                </h3>
                
                <div class="order-items-list">
                    @foreach($pesanan->details as $detail)
                    <div class="order-item-row">
                        <div class="order-item-img">
                            @if($detail->buku->cover)
                                <img src="{{ asset('storage/' . $detail->buku->cover) }}" alt="{{ $detail->buku->judul }}">
                            @else
                                <div class="order-item-placeholder">
                                    <i class="fas fa-book"></i>
                                </div>
                            @endif
                        </div>
                        <div class="order-item-info">
                            <h4>{{ $detail->buku->judul }}</h4>
                            <p class="order-item-author">{{ $detail->buku->penulis }}</p>
                            <p class="order-item-qty">{{ $detail->qty }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</p>
                        </div>
                        <div class="order-item-subtotal">
                            Rp {{ number_format($detail->harga_satuan * $detail->qty, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Info -->
            <div class="payment-section" style="margin-top: 25px;">
                <h3 class="payment-section-title">
                    <i class="fas fa-shield-alt"></i> Pembayaran Aman
                </h3>
                <div class="payment-info-box">
                    <div class="payment-info-icon">
                        <i class="fas fa-lock" style="font-size: 32px; color: var(--green-dark, #6b9e65);"></i>
                    </div>
                    <div class="payment-info-text">
                        <p>Pembayaran diproses melalui <strong>Midtrans</strong> secara aman.</p>
                        <p class="payment-info-sub">Pilih metode pembayaran (Transfer Bank, E-Wallet, Kartu Kredit, QRIS, dll) pada popup pembayaran Midtrans.</p>
                    </div>
                </div>
                <div class="payment-methods-grid">
                    <div class="pm-badge"><i class="fas fa-university"></i> Bank Transfer</div>
                    <div class="pm-badge"><i class="fas fa-wallet"></i> GoPay</div>
                    <div class="pm-badge"><i class="fas fa-wallet"></i> ShopeePay</div>
                    <div class="pm-badge"><i class="fas fa-credit-card"></i> Kartu Kredit</div>
                    <div class="pm-badge"><i class="fas fa-qrcode"></i> QRIS</div>
                    <div class="pm-badge"><i class="fas fa-store"></i> Indomaret</div>
                </div>
            </div>
        </div>
        
        <!-- Payment Summary (Sticky) -->
        <div class="payment-summary">
            <h3 class="payment-section-title" style="margin-bottom: 20px;">Rincian Pembayaran</h3>
            
            <div class="summary-row">
                <span>Total Harga ({{ $pesanan->details->sum('qty') }} Barang)</span>
                <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
            
            <div class="summary-row">
                <span>Total Biaya Pengiriman</span>
                <span style="color: var(--green-dark, #6b9e65);">Gratis</span>
            </div>
            
            <div class="summary-row total-row">
                <span>Total Pembayaran</span>
                <span class="total-amount">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
            
            <button type="button" id="btnPayment" class="btn-pay-now">
                <i class="fas fa-lock"></i> Bayar Sekarang
            </button>
            <p class="payment-secure-note">
                <i class="fas fa-shield-alt"></i> Transaksi aman & terenkripsi
            </p>
        </div>
    </div>
</div>

<!-- Midtrans Snap JS -->
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $clientKey }}"></script>

@push('styles')
<style>
.checkout-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    max-width: 500px;
    margin: 0 auto 50px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.step-number {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #9ca3af;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 18px;
    border: 3px solid #e5e7eb;
}

.step.active .step-number {
    background: var(--green-dark, #6b9e65);
    color: white;
    border-color: var(--green-dark, #6b9e65);
}

.step.completed .step-number {
    background: #27ae60;
    color: white;
    border-color: #27ae60;
}

.step-label {
    font-size: 14px;
    font-weight: 600;
    color: #9ca3af;
}

.step.active .step-label,
.step.completed .step-label {
    color: var(--green-dark, #6b9e65);
}

.step-line {
    width: 120px;
    height: 3px;
    background: #e5e7eb;
    margin: 0 20px;
    margin-bottom: 35px;
}

.step-line.active {
    background: var(--green-dark, #6b9e65);
}

.payment-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.payment-section-title {
    color: var(--green-dark, #6b9e65);
    margin-bottom: 25px;
    font-size: 20px;
}

/* Order Items */
.order-items-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.order-item-row {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f9fafb;
    border-radius: 10px;
    transition: all 0.3s;
}

.order-item-row:hover {
    background: #f0fdf4;
}

.order-item-img {
    width: 65px;
    height: 85px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.order-item-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.order-item-placeholder {
    width: 100%;
    height: 100%;
    background: var(--green-light, #d4edda);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--green-dark, #6b9e65);
    font-size: 24px;
}

.order-item-info {
    flex: 1;
}

.order-item-info h4 {
    font-size: 15px;
    font-weight: 600;
    color: #374151;
    margin: 0 0 4px;
}

.order-item-author {
    font-size: 13px;
    color: #6b7280;
    margin: 0 0 6px;
}

.order-item-qty {
    font-size: 14px;
    color: #4b5563;
    margin: 0;
}

.order-item-subtotal {
    font-weight: 700;
    font-size: 15px;
    color: var(--green-dark, #6b9e65);
    white-space: nowrap;
}

/* Payment Info */
.payment-info-box {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-radius: 12px;
    border: 1px solid var(--green-pastel, #a8d5a2);
}

.payment-info-icon {
    flex-shrink: 0;
    width: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.payment-info-text p {
    font-size: 14px;
    color: #374151;
    margin: 0 0 6px;
}

.payment-info-sub {
    font-size: 13px !important;
    color: #6b7280 !important;
}

.payment-methods-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 15px;
}

.pm-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    font-size: 12px;
    color: #4b5563;
    font-weight: 500;
}

.pm-badge i {
    color: var(--green-dark, #6b9e65);
    font-size: 11px;
}

/* Payment Summary */
.payment-summary {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    position: sticky;
    top: 100px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    font-size: 15px;
    color: #374151;
}

.total-row {
    border-top: 2px solid var(--green-dark, #6b9e65);
    margin-top: 15px;
    padding-top: 15px;
}

.total-row span:first-child {
    font-weight: 700;
    font-size: 18px;
}

.total-amount {
    font-weight: 700;
    font-size: 20px;
    color: var(--green-dark, #6b9e65);
}

.btn-pay-now {
    width: 100%;
    padding: 16px;
    font-size: 16px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    margin-top: 25px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    background: linear-gradient(135deg, var(--green-dark, #6b9e65), var(--green-deeper, #4a7c44));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(74, 124, 68, 0.3);
}

.btn-pay-now:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(74, 124, 68, 0.4);
}

.btn-pay-now:active {
    transform: translateY(0);
}

.payment-secure-note {
    text-align: center;
    font-size: 13px;
    color: #6b7280;
    margin-top: 12px;
}

.payment-secure-note i {
    color: var(--green-dark, #6b9e65);
}

@media (max-width: 992px) {
    .user-container > div {
        grid-template-columns: 1fr !important;
    }
    
    .payment-summary {
        position: static;
        margin-top: 30px;
    }

    .payment-info-box {
        flex-direction: column;
        text-align: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnPayment = document.getElementById('btnPayment');
    
    btnPayment.addEventListener('click', function() {
        // Open Midtrans Snap popup
        window.snap.pay('{{ $pesanan->snap_token }}', {
            onSuccess: function(result) {
                btnPayment.disabled = true;
                btnPayment.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                savePaymentResult(result);
            },
            onPending: function(result) {
                alert('Pembayaran pending. Silakan selesaikan pembayaran Anda sesuai instruksi.');
                window.location.href = '{{ route("user.orders") }}';
            },
            onError: function(result) {
                alert('Pembayaran gagal. Silakan coba lagi.');
                console.error('Payment error:', result);
            },
            onClose: function() {
                console.log('Payment popup closed');
            }
        });
    });

    function savePaymentResult(result) {
        fetch('{{ route("user.payment.process", $pesanan->id_pesanan) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                transaction_id: result.transaction_id,
                order_id: result.order_id,
                payment_type: result.payment_type,
                transaction_status: result.transaction_status,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert(data.message || 'Terjadi kesalahan.');
                btnPayment.disabled = false;
                btnPayment.innerHTML = '<i class="fas fa-lock"></i> Bayar Sekarang';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.location.href = '{{ route("user.orders") }}';
        });
    }
});
</script>
@endpush
@endsection
