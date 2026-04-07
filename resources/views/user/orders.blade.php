@extends('user.layout')

@section('title', 'Pesanan Saya')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <div class="page-header">
        <h1 class="page-title">Pesanan Saya</h1>
        <p class="page-subtitle">Lacak dan kelola riwayat pesanan Anda</p>
    </div>

    @if($orders->count() > 0)
        <div class="orders-list">
            @foreach($orders as $order)
            <div class="order-card">
                <!-- Order Header -->
                <div class="order-card-header">
                    <div class="order-meta">
                        <span class="order-id">#{{ $order->id_pesanan }}</span>
                        @if($order->metode_pembayaran === 'cod')
                            <span class="order-method-badge method-cod"><i class="fas fa-money-bill-wave"></i> COD</span>
                        @else
                            <span class="order-method-badge method-online"><i class="fas fa-credit-card"></i> Online</span>
                        @endif
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
                            @if($order->metode_pembayaran === 'cod')
                                <span class="order-info-note">
                                    <i class="fas fa-clock"></i> Menunggu admin memproses pesanan COD
                                </span>
                            @else
                                <a href="{{ route('user.payment', $order->id_pesanan) }}" class="btn-order-action btn-pay">
                                    <i class="fas fa-credit-card"></i> Bayar Sekarang
                                </a>
                            @endif
                            <form action="{{ route('user.order.cancel', $order->id_pesanan) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin membatalkan pesanan #{{ $order->id_pesanan }}?')">
                                @csrf
                                <button type="submit" class="btn-order-action btn-cancel">
                                    <i class="fas fa-times"></i> Batalkan
                                </button>
                            </form>
                        @endif
                        @if($order->status === 'diproses')
                            @if($order->metode_pembayaran === 'cod')
                                <span class="order-info-note">
                                    <i class="fas fa-box"></i> Pesanan sedang dikemas (COD)
                                </span>
                            @else
                                <span class="order-info-note">
                                    <i class="fas fa-info-circle"></i> Pesanan sedang dikemas
                                </span>
                            @endif
                        @endif
                        @if($order->status === 'dikirim')
                            @if($order->metode_pembayaran === 'cod')
                                @if($order->bukti_cod)
                                    <span class="order-info-note cod-uploaded">
                                        <i class="fas fa-check-circle"></i> Bukti COD sudah diunggah — menunggu verifikasi admin
                                    </span>
                                @else
                                    <button type="button" class="btn-order-action btn-upload-cod" onclick="openCodModal({{ $order->id_pesanan }})">
                                        <i class="fas fa-camera"></i> Upload Bukti COD
                                    </button>
                                @endif
                            @else
                                <span class="order-info-note">
                                    <i class="fas fa-truck"></i> Dalam perjalanan
                                </span>
                            @endif
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

<!-- COD Upload Modal -->
<div id="codModal" class="cod-modal-overlay" style="display:none;">
    <div class="cod-modal">
        <div class="cod-modal-header">
            <h3><i class="fas fa-camera"></i> Upload Bukti COD</h3>
            <button type="button" class="cod-modal-close" onclick="closeCodModal()">&times;</button>
        </div>
        <form id="codUploadForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="cod-modal-body">
                <div class="cod-info-box">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <p><strong>Kirim foto sebagai bukti bahwa:</strong></p>
                        <ul>
                            <li>Anda telah menerima buku pesanan</li>
                            <li>Pembayaran COD telah dilakukan</li>
                        </ul>
                    </div>
                </div>
                <div class="cod-upload-area" id="codUploadArea">
                    <input type="file" name="bukti_cod" id="codFileInput" accept="image/jpeg,image/jpg,image/png,image/webp" required style="display:none;">
                    <div class="cod-upload-placeholder" id="codPlaceholder">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Klik atau seret foto ke sini</p>
                        <span>JPG, PNG, WebP (maks. 5MB)</span>
                    </div>
                    <img id="codPreview" class="cod-preview-img" style="display:none;" alt="Preview">
                </div>
            </div>
            <div class="cod-modal-footer">
                <button type="button" class="btn-cod-cancel" onclick="closeCodModal()">Batal</button>
                <button type="submit" class="btn-cod-submit">
                    <i class="fas fa-upload"></i> Kirim Bukti
                </button>
            </div>
        </form>
    </div>
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

/* Method Badge */
.order-method-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}
.method-cod { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.method-online { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }

/* Upload COD Button */
.btn-upload-cod {
    background: linear-gradient(135deg, #f59e0b, #d97706) !important;
    color: white !important;
    border: none !important;
    box-shadow: 0 3px 10px rgba(217, 119, 6, 0.3);
}
.btn-upload-cod:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(217, 119, 6, 0.4);
}

.cod-uploaded {
    color: #059669 !important;
    font-weight: 600;
}

/* COD Modal */
.cod-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.6);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    backdrop-filter: blur(4px);
}

.cod-modal {
    background: white;
    border-radius: 18px;
    max-width: 480px;
    width: 100%;
    box-shadow: 0 25px 60px rgba(0,0,0,0.3);
    animation: slideUpModal 0.3s ease;
}

@keyframes slideUpModal {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.cod-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
}

.cod-modal-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: #374151;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cod-modal-header h3 i { color: var(--green-dark, #6b9e65); }

.cod-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #9ca3af;
    cursor: pointer;
    padding: 4px;
    line-height: 1;
}

.cod-modal-close:hover { color: #374151; }

.cod-modal-body { padding: 20px 24px; }

.cod-info-box {
    display: flex;
    gap: 12px;
    padding: 14px;
    background: #fffbeb;
    border: 1px solid #fcd34d;
    border-radius: 12px;
    margin-bottom: 18px;
    font-size: 13px;
    color: #92400e;
}

.cod-info-box i { font-size: 18px; flex-shrink: 0; margin-top: 2px; }
.cod-info-box p { margin: 0 0 6px; }
.cod-info-box ul { margin: 0; padding-left: 18px; }
.cod-info-box li { margin-bottom: 2px; }

.cod-upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.2s;
    overflow: hidden;
}

.cod-upload-area:hover, .cod-upload-area.dragover {
    border-color: var(--green-dark, #6b9e65);
    background: #f0fdf4;
}

.cod-upload-placeholder {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}

.cod-upload-placeholder i { font-size: 40px; margin-bottom: 10px; display: block; }
.cod-upload-placeholder p { font-size: 14px; margin: 0 0 4px; font-weight: 600; color: #6b7280; }
.cod-upload-placeholder span { font-size: 12px; }

.cod-preview-img {
    width: 100%;
    max-height: 280px;
    object-fit: contain;
    display: block;
}

.cod-modal-footer {
    display: flex;
    gap: 10px;
    padding: 16px 24px;
    border-top: 1px solid #f0f0f0;
}

.btn-cod-cancel {
    flex: 1;
    padding: 12px;
    background: #f3f4f6;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    color: #6b7280;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-cod-cancel:hover { background: #e5e7eb; }

.btn-cod-submit {
    flex: 1;
    padding: 12px;
    background: linear-gradient(135deg, var(--green-dark, #6b9e65), var(--green-deeper, #4a7c44));
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s;
    box-shadow: 0 3px 10px rgba(74, 124, 68, 0.3);
}
.btn-cod-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(74, 124, 68, 0.4);
}
</style>
@endpush

@push('scripts')
<script>
function openCodModal(orderId) {
    const form = document.getElementById('codUploadForm');
    form.action = '/Online_book/public/orders/' + orderId + '/upload-cod';
    document.getElementById('codFileInput').value = '';
    document.getElementById('codPreview').style.display = 'none';
    document.getElementById('codPlaceholder').style.display = 'block';
    document.getElementById('codModal').style.display = 'flex';
}

function closeCodModal() {
    document.getElementById('codModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('codUploadArea');
    const fileInput = document.getElementById('codFileInput');
    const preview = document.getElementById('codPreview');
    const placeholder = document.getElementById('codPlaceholder');

    if (uploadArea) {
        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                showPreview(e.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length) showPreview(this.files[0]);
        });
    }

    function showPreview(file) {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    // Close modal on overlay click
    const modal = document.getElementById('codModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeCodModal();
        });
    }
});
</script>
@endpush

@endsection
