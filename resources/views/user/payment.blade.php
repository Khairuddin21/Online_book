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
        <!-- Payment Form -->
        <div>
            <!-- Payment Methods -->
            <div class="payment-section">
                <h3 style="color: var(--user-primary); margin-bottom: 25px; font-size: 20px;">
                    <i class="fas fa-credit-card"></i> Metode Pembayaran
                </h3>
                
                <form action="{{ route('user.payment.process', $pesanan->id_pesanan) }}" method="POST" id="paymentForm">
                    @csrf
                    
                    <!-- E-Money -->
                    <div class="payment-method-group">
                        <label class="payment-method-item">
                            <input type="radio" name="metode_pembayaran" value="e-wallet" required>
                            <div class="payment-method-content">
                                <div class="payment-method-header">
                                    <i class="fas fa-wallet" style="font-size: 24px; color: var(--user-primary);"></i>
                                    <span class="payment-method-title">Uang Elektronik</span>
                                </div>
                                <i class="fas fa-chevron-down payment-method-toggle"></i>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Virtual Account -->
                    <div class="payment-method-group">
                        <label class="payment-method-item">
                            <input type="radio" name="metode_pembayaran" value="transfer" required>
                            <div class="payment-method-content">
                                <div class="payment-method-header">
                                    <i class="fas fa-university" style="font-size: 24px; color: var(--user-primary);"></i>
                                    <span class="payment-method-title">Virtual Account</span>
                                </div>
                                <i class="fas fa-chevron-down payment-method-toggle"></i>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Credit/Debit Card -->
                    <div class="payment-method-group">
                        <label class="payment-method-item">
                            <input type="radio" name="metode_pembayaran" value="kartu_kredit" required>
                            <div class="payment-method-content">
                                <div class="payment-method-header">
                                    <i class="fas fa-credit-card" style="font-size: 24px; color: var(--user-primary);"></i>
                                    <span class="payment-method-title">Kartu Kredit/Debit</span>
                                </div>
                                <i class="fas fa-chevron-down payment-method-toggle"></i>
                            </div>
                        </label>
                    </div>
                    
                    @error('metode_pembayaran')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </form>
            </div>
            
            <!-- Promo Code Section (Dummy) -->
            <div class="payment-section" style="margin-top: 25px;">
                <h3 style="color: var(--user-primary); margin-bottom: 20px; font-size: 18px;">
                    <i class="fas fa-tag"></i> Promo
                    <span style="float: right; font-size: 14px; font-weight: 500; color: var(--user-accent); cursor: pointer;">
                        Lihat Semua
                    </span>
                </h3>
                
                <div class="promo-input-group">
                    <input type="text" 
                           class="form-input" 
                           placeholder="Kode Promo"
                           style="flex: 1;">
                    <button type="button" class="btn btn-outline" style="padding: 12px 25px;">
                        Gunakan
                    </button>
                </div>
                
                <!-- Example Promo (Dummy) -->
                <div class="promo-card">
                    <div class="promo-icon">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="promo-details">
                        <h4>Voucher Potongan Ongkir 20K - Nominal 100.000</h4>
                        <p><strong>Diskon Rp20.000</strong></p>
                        <ul>
                            <li>Diskon Ongkir</li>
                            <li>Berlaku s/d 30 November 2025</li>
                            <li>Min. Belanja Rp100.000</li>
                        </ul>
                        <div class="promo-code">
                            Kode: <strong>SERUNOV</strong>
                            <button class="copy-btn" onclick="copyPromoCode('SERUNOV')">
                                <i class="far fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <a href="#" class="promo-info-link">Info</a>
                </div>
            </div>
        </div>
        
        <!-- Payment Summary (Sticky) -->
        <div class="payment-summary">
            <h3 style="color: var(--user-primary); margin-bottom: 20px; font-size: 20px;">Rincian Pembayaran</h3>
            
            <div class="summary-row">
                <span>Total Harga ({{ $pesanan->details->sum('qty') }} Barang)</span>
                <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
            
            <div class="summary-row">
                <span>Total Biaya Pengiriman</span>
                <span>Rp0</span>
            </div>
            
            <div class="summary-row" style="color: #27ae60;">
                <span>Diskon Belanja</span>
                <span>-Rp0</span>
            </div>
            
            <div class="summary-row" style="color: #e74c3c;">
                <span>Diskon Voucher</span>
                <span>-Rp0</span>
            </div>
            
            <div class="summary-row" style="border-top: 2px solid var(--user-primary); margin-top: 15px; padding-top: 15px;">
                <span style="font-weight: 700; font-size: 18px;">Total Pembayaran</span>
                <span style="font-weight: 700; font-size: 20px; color: var(--user-primary);">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
            
            <button type="submit" form="paymentForm" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px; margin-top: 25px;">
                <i class="fas fa-check-circle"></i> Bayar
            </button>
        </div>
    </div>
</div>

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
    background: var(--user-primary);
    color: white;
    border-color: var(--user-primary);
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
    color: var(--user-primary);
}

.step-line {
    width: 120px;
    height: 3px;
    background: #e5e7eb;
    margin: 0 20px;
    margin-bottom: 35px;
}

.step-line.active {
    background: var(--user-primary);
}

.payment-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.payment-method-group {
    margin-bottom: 15px;
}

.payment-method-item {
    display: block;
    cursor: pointer;
}

.payment-method-item input[type="radio"] {
    display: none;
}

.payment-method-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 20px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    transition: all 0.3s;
    background: white;
}

.payment-method-item:hover .payment-method-content {
    border-color: var(--user-primary);
    background: #f8fafc;
}

.payment-method-item input[type="radio"]:checked ~ .payment-method-content {
    border-color: var(--user-primary);
    background: #eff6ff;
}

.payment-method-header {
    display: flex;
    align-items: center;
    gap: 15px;
}

.payment-method-title {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
}

.payment-method-toggle {
    color: #9ca3af;
    transition: transform 0.3s;
}

.payment-method-item input[type="radio"]:checked ~ .payment-method-content .payment-method-toggle {
    transform: rotate(180deg);
    color: var(--user-primary);
}

.promo-input-group {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.promo-card {
    position: relative;
    background: linear-gradient(135deg, #fff9e6 0%, #fff5d9 100%);
    border: 2px dashed #f59e0b;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    gap: 15px;
}

.promo-icon {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #f59e0b;
    flex-shrink: 0;
}

.promo-details h4 {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin: 0 0 8px 0;
}

.promo-details p {
    font-size: 14px;
    color: #e74c3c;
    margin: 0 0 10px 0;
}

.promo-details ul {
    list-style: none;
    padding: 0;
    margin: 0 0 12px 0;
}

.promo-details ul li {
    font-size: 13px;
    color: #666;
    margin-bottom: 4px;
    padding-left: 15px;
    position: relative;
}

.promo-details ul li:before {
    content: "•";
    position: absolute;
    left: 0;
    color: #f59e0b;
}

.promo-code {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #374151;
}

.promo-code strong {
    color: var(--user-primary);
}

.copy-btn {
    background: none;
    border: none;
    color: var(--user-primary);
    cursor: pointer;
    padding: 4px;
    transition: all 0.3s;
}

.copy-btn:hover {
    color: var(--user-accent);
}

.promo-info-link {
    position: absolute;
    top: 15px;
    right: 15px;
    color: var(--user-primary);
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
}

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

.form-input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s;
}

.form-input:focus {
    outline: none;
    border-color: var(--user-primary);
    box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
}

.form-error {
    color: #e74c3c;
    font-size: 13px;
    margin-top: 5px;
    display: block;
}

@media (max-width: 992px) {
    .user-container > div {
        grid-template-columns: 1fr !important;
    }
    
    .payment-summary {
        position: static;
        margin-top: 30px;
    }
}
</style>
@endpush

@push('scripts')
<script>
function copyPromoCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Kode promo berhasil disalin: ' + code);
    });
}
</script>
@endpush
@endsection
