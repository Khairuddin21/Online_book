@extends('admin.layout')

@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: var(--text-dark);">Detail Pesanan #{{ $pesanan->id_pesanan }}</h2>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Informasi lengkap pesanan</p>
    </div>
    <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
    <!-- Left Column -->
    <div>
        <!-- Customer Info -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <i class="fas fa-user"></i>
                <h3>Informasi Pelanggan</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Nama</div>
                        <div style="font-weight: 600; color: var(--text-dark);">{{ $pesanan->user->nama ?? '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Email</div>
                        <div style="color: var(--text-dark);">{{ $pesanan->user->email ?? '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">No. HP</div>
                        <div style="color: var(--text-dark);">{{ $pesanan->user->no_hp ?? '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Alamat</div>
                        <div style="color: var(--text-dark);">{{ $pesanan->user->alamat ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="data-table">
            <h2><i class="fas fa-box" style="color: var(--green-dark); margin-right: 8px;"></i>Item Pesanan</h2>
            <table>
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Buku</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Harga</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanan->pesananDetails as $detail)
                    <tr>
                        <td>
                            @if($detail->buku && $detail->buku->cover)
                                @if(Str::startsWith($detail->buku->cover, ['http://', 'https://']))
                                    <img src="{{ $detail->buku->cover }}" alt="{{ $detail->buku->judul }}" style="width: 44px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);">
                                @else
                                    <img src="{{ asset('storage/' . $detail->buku->cover) }}" alt="{{ $detail->buku->judul }}" style="width: 44px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);">
                                @endif
                            @else
                                <div style="width: 44px; height: 60px; background: var(--green-bg); border-radius: 6px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color);">
                                    <i class="fas fa-book" style="color: var(--text-light);"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-dark);">{{ $detail->buku->judul ?? 'Buku dihapus' }}</div>
                            @if($detail->buku)
                            <div style="font-size: 12px; color: var(--text-muted);">{{ $detail->buku->penulis }}</div>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-gray">{{ $detail->qty }}</span>
                        </td>
                        <td style="text-align: right; font-size: 13px;">
                            Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right; font-weight: 700; color: var(--green-deeper);">
                            Rp {{ number_format($detail->qty * $detail->harga_satuan, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                    <tr style="background: var(--green-bg);">
                        <td colspan="4" style="text-align: right; font-weight: 700; font-size: 15px;">Total</td>
                        <td style="text-align: right; font-weight: 800; font-size: 16px; color: var(--green-deeper);">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column -->
    <div>
        <!-- Order Info -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <i class="fas fa-receipt"></i>
                <h3>Info Pesanan</h3>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="label">ID Pesanan</div>
                    <div class="value" style="font-weight: 700;">#{{ $pesanan->id_pesanan }}</div>
                    <div class="label">Tanggal</div>
                    <div class="value">{{ $pesanan->tanggal_pesanan ? $pesanan->tanggal_pesanan->format('d M Y, H:i') : '-' }}</div>
                    <div class="label">Status</div>
                    <div class="value">
                        <span class="badge status-{{ $pesanan->status }}">
                            @switch($pesanan->status)
                                @case('menunggu') <i class="fas fa-clock"></i> @break
                                @case('diproses') <i class="fas fa-spinner"></i> @break
                                @case('dikirim') <i class="fas fa-truck"></i> @break
                                @case('selesai') <i class="fas fa-check-circle"></i> @break
                                @case('dibatalkan') <i class="fas fa-times-circle"></i> @break
                            @endswitch
                            {{ ucfirst($pesanan->status) }}
                        </span>
                    </div>
                    <div class="label">Metode Bayar</div>
                    <div class="value">
                        @if($pesanan->metode_pembayaran === 'offline')
                            <span class="badge badge-yellow" style="font-weight:700;"><i class="fas fa-store"></i> Payment Offline</span>
                        @else
                            <span class="badge badge-blue" style="font-weight:700;"><i class="fas fa-credit-card"></i> Online (Midtrans)</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Status / Aksi Pembayaran Offline -->
        @if(!in_array($pesanan->status, ['selesai', 'dibatalkan']))

        {{-- Kalo offline dan belum dibayar, tampilin aksi pembayaran --}}
        @if($pesanan->metode_pembayaran === 'offline' && (!$pesanan->pembayaran || $pesanan->pembayaran->status_verifikasi !== 'valid'))
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <i class="fas fa-cash-register"></i>
                <h3>Pembayaran Offline</h3>
            </div>
            <div class="card-body">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
                    Pilih metode pembayaran untuk pesanan offline ini.
                </p>

                <!-- Tab Switch -->
                <div class="offline-pay-tabs">
                    <button type="button" class="offline-pay-tab active" data-tab="cash" onclick="switchPayTab('cash')">
                        <i class="fas fa-money-bill-wave"></i> Bayar Cash
                    </button>
                    <button type="button" class="offline-pay-tab" data-tab="midtrans" onclick="switchPayTab('midtrans')">
                        <i class="fas fa-credit-card"></i> Bayar Midtrans
                    </button>
                </div>

                <!-- Cash Payment Form -->
                <div id="cashPayPanel" class="offline-pay-panel">
                    <form action="{{ route('admin.pesanan.offlinePayment', $pesanan->id_pesanan) }}" method="POST" id="cashPayForm">
                        @csrf
                        <input type="hidden" name="metode_bayar" value="cash">

                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 14px; margin-bottom: 14px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 13px; color: #166534; font-weight: 600;">Total Tagihan</span>
                                <span style="font-size: 20px; font-weight: 800; color: #166534;" id="totalTagihan">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div style="margin-bottom: 14px;">
                            <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); display: block; margin-bottom: 6px;">
                                Jumlah Uang Diterima <span style="color: #ef4444;">*</span>
                            </label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #6b7280; font-size: 14px;">Rp</span>
                                <input type="number" name="jumlah_cash" id="jumlahCashInput"
                                       class="form-input" 
                                       style="padding-left: 36px; font-size: 16px; font-weight: 700; height: 48px;"
                                       placeholder="0" min="0" step="1000"
                                       value="{{ old('jumlah_cash') }}"
                                       oninput="hitungKembalian()" required>
                            </div>
                        </div>

                        <div id="kembalianBox" style="display: none; background: #fffbeb; border: 1px solid #fcd34d; border-radius: 10px; padding: 12px 14px; margin-bottom: 14px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 13px; color: #92400e; font-weight: 600;">Kembalian</span>
                                <span style="font-size: 18px; font-weight: 800; color: #92400e;" id="kembalianAmount">Rp 0</span>
                            </div>
                        </div>

                        <div id="kurangBox" style="display: none; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 14px; margin-bottom: 14px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 13px; color: #991b1b; font-weight: 600;"><i class="fas fa-exclamation-triangle"></i> Kurang</span>
                                <span style="font-size: 18px; font-weight: 800; color: #991b1b;" id="kurangAmount">Rp 0</span>
                            </div>
                        </div>

                        <!-- Quick Amount Buttons -->
                        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 16px;">
                            @php
                                $total = $pesanan->total_harga;
                                $quickAmounts = [
                                    ceil($total / 1000) * 1000,
                                    ceil($total / 5000) * 5000,
                                    ceil($total / 10000) * 10000,
                                    ceil($total / 50000) * 50000,
                                ];
                                $quickAmounts = collect($quickAmounts)->unique()->filter(fn($v) => $v >= $total)->take(4)->values();
                            @endphp
                            @foreach($quickAmounts as $amt)
                            <button type="button" class="quick-amount-btn" onclick="setQuickAmount({{ $amt }})">
                                Rp {{ number_format($amt, 0, ',', '.') }}
                            </button>
                            @endforeach
                            <button type="button" class="quick-amount-btn" onclick="setQuickAmount({{ $total }})" style="background: #dcfce7; color: #166534; border-color: #86efac;">
                                Uang Pas
                            </button>
                        </div>

                        <button type="submit" class="btn btn-success" id="cashPayBtn" style="width: 100%; justify-content: center; padding: 12px;" disabled>
                            <i class="fas fa-check-circle"></i> <span id="cashPayBtnText">Konfirmasi Pembayaran Cash</span>
                        </button>
                    </form>
                </div>

                <!-- Midtrans Payment Panel -->
                <div id="midtransPayPanel" class="offline-pay-panel" style="display: none;">
                    <form action="{{ route('admin.pesanan.offlinePayment', $pesanan->id_pesanan) }}" method="POST" id="midtransPayForm">
                        @csrf
                        <input type="hidden" name="metode_bayar" value="midtrans">

                        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 14px; margin-bottom: 14px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 13px; color: #1e40af; font-weight: 600;">Total Tagihan</span>
                                <span style="font-size: 20px; font-weight: 800; color: #1e40af;">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px; line-height: 1.6;">
                            <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
                            Pembayaran akan diproses lewat Midtrans (QRIS, Transfer Bank, E-Wallet, dll). Setelah klik tombol di bawah, popup pembayaran akan muncul.
                        </p>

                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px;">
                            <i class="fas fa-credit-card"></i> Bayar dengan Midtrans
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Status update biasa (berlaku semua metode) --}}
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <i class="fas fa-sync-alt"></i>
                <h3>Update Status</h3>
            </div>
            <div class="card-body">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;">Ubah status pesanan secara manual.</p>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @if($pesanan->status === 'menunggu')
                    <form action="{{ route('admin.pesanan.updateStatus', $pesanan->id_pesanan) }}" method="POST" onsubmit="return confirm('Proses pesanan ini?')">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="diproses">
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                            <i class="fas fa-box"></i> Proses Pesanan
                        </button>
                    </form>
                    @endif

                    @if($pesanan->status === 'diproses')
                    <form action="{{ route('admin.pesanan.updateStatus', $pesanan->id_pesanan) }}" method="POST" onsubmit="return confirm('Tandai pesanan telah dikirim?')">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="dikirim">
                        <button type="submit" class="btn btn-success" style="width: 100%; justify-content: center;">
                            <i class="fas fa-truck"></i> Tandai Dikirim
                        </button>
                    </form>
                    @endif

                    @if($pesanan->status === 'dikirim')
                    <form action="{{ route('admin.pesanan.updateStatus', $pesanan->id_pesanan) }}" method="POST" onsubmit="return confirm('Tandai pesanan selesai? Pendapatan akan tercatat.')">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="selesai">
                        <button type="submit" class="btn btn-success" style="width: 100%; justify-content: center;">
                            <i class="fas fa-check-circle"></i> Tandai Selesai
                        </button>
                    </form>
                    @endif

                    @if(in_array($pesanan->status, ['menunggu', 'diproses']))
                    <form action="{{ route('admin.pesanan.updateStatus', $pesanan->id_pesanan) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?')">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="dibatalkan">
                        <button type="submit" class="btn btn-danger" style="width: 100%; justify-content: center;">
                            <i class="fas fa-times-circle"></i> Batalkan Pesanan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Payment Info -->
        @if($pesanan->pembayaran)
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <i class="fas fa-credit-card"></i>
                <h3>Info Pembayaran</h3>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="label">Metode</div>
                    <div class="value">{{ ucfirst($pesanan->pembayaran->metode ?? '-') }}</div>
                    <div class="label">Total Tagihan</div>
                    <div class="value" style="font-weight: 700; color: var(--green-deeper);">Rp {{ number_format($pesanan->pembayaran->jumlah, 0, ',', '.') }}</div>
                    @if($pesanan->pembayaran->jumlah_dibayar)
                    <div class="label">Jumlah Dibayar</div>
                    <div class="value" style="font-weight: 700; color: var(--text-dark);">Rp {{ number_format($pesanan->pembayaran->jumlah_dibayar, 0, ',', '.') }}</div>
                    <div class="label">Kembalian</div>
                    <div class="value">
                        @php $kembalian = $pesanan->pembayaran->jumlah_dibayar - $pesanan->pembayaran->jumlah; @endphp
                        @if($kembalian > 0)
                            <span style="font-weight: 700; color: #d97706;">Rp {{ number_format($kembalian, 0, ',', '.') }}</span>
                        @else
                            <span style="color: var(--text-muted);">Rp 0 (Uang Pas)</span>
                        @endif
                    </div>
                    @endif
                    <div class="label">Status</div>
                    <div class="value">
                        @if($pesanan->pembayaran->status_verifikasi == 'valid')
                            <span class="badge badge-green"><i class="fas fa-check"></i> Valid</span>
                        @elseif($pesanan->pembayaran->status_verifikasi == 'pending')
                            <span class="badge badge-yellow"><i class="fas fa-clock"></i> Pending</span>
                        @else
                            <span class="badge badge-red"><i class="fas fa-times"></i> {{ ucfirst($pesanan->pembayaran->status_verifikasi) }}</span>
                        @endif
                    </div>
                    <div class="label">Tanggal</div>
                    <div class="value">
                        @if($pesanan->pembayaran->tanggal_bayar)
                            {{ \Carbon\Carbon::parse($pesanan->pembayaran->tanggal_bayar)->format('d M Y, H:i') }}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Bukti Pembayaran Offline -->
        @if($pesanan->metode_pembayaran === 'offline')
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <i class="fas fa-camera"></i>
                <h3>Bukti Pembayaran Offline</h3>
            </div>
            <div class="card-body">
                @if($pesanan->bukti_offline)
                    <div style="text-align: center; margin-bottom: 16px;">
                        <img src="{{ asset('storage/' . $pesanan->bukti_offline) }}" 
                             alt="Bukti Pembayaran Offline" 
                             style="max-width: 100%; max-height: 300px; border-radius: 12px; border: 2px solid var(--border-color); cursor: pointer;"
                             onclick="window.open(this.src, '_blank')">
                    </div>
                    <p style="text-align: center; font-size: 12px; color: var(--text-muted); margin-bottom: 16px;">
                        <i class="fas fa-search-plus"></i> Klik gambar untuk memperbesar
                    </p>

                    @if($pesanan->status === 'dikirim' && $pesanan->pembayaran && $pesanan->pembayaran->status_verifikasi !== 'valid')
                    <div style="display: flex; gap: 8px;">
                        <form action="{{ route('admin.pesanan.verifyOffline', $pesanan->id_pesanan) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Verifikasi pembayaran offline dan tandai pesanan selesai?')">
                            @csrf @method('PUT')
                            <input type="hidden" name="aksi" value="terima">
                            <button type="submit" class="btn btn-success" style="width: 100%; justify-content: center;">
                                <i class="fas fa-check-circle"></i> Verifikasi & Selesaikan
                            </button>
                        </form>
                        <form action="{{ route('admin.pesanan.verifyOffline', $pesanan->id_pesanan) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Tolak bukti pembayaran offline? Pembayaran ditandai invalid.')">
                            @csrf @method('PUT')
                            <input type="hidden" name="aksi" value="tolak">
                            <button type="submit" class="btn btn-danger" style="width: 100%; justify-content: center;">
                                <i class="fas fa-times-circle"></i> Tolak Bukti
                            </button>
                        </form>
                    </div>
                    @elseif($pesanan->pembayaran && $pesanan->pembayaran->status_verifikasi === 'valid')
                    <div style="text-align: center; padding: 10px; background: #e8f8ef; border-radius: 10px;">
                        <span style="color: #27ae60; font-weight: 700; font-size: 14px;">
                            <i class="fas fa-check-circle"></i> Pembayaran Offline Terverifikasi
                        </span>
                    </div>
                    @endif
                @else
                    <div style="text-align: center; padding: 30px 16px; color: var(--text-muted);">
                        <i class="fas fa-image" style="font-size: 36px; margin-bottom: 10px; display: block; opacity: 0.4;"></i>
                        <p style="margin: 0; font-size: 14px;">User belum mengunggah bukti pembayaran offline</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Delete Action -->
        <form action="{{ route('admin.pesanan.delete', $pesanan->id_pesanan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan ini? Semua data terkait (detail & pembayaran) juga akan dihapus.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" style="width: 100%; justify-content: center;">
                <i class="fas fa-trash"></i> Hapus Pesanan
            </button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 2fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }

    /* Offline Payment Tabs */
    .offline-pay-tabs {
        display: flex;
        gap: 0;
        margin-bottom: 16px;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid var(--border-color);
    }
    .offline-pay-tab {
        flex: 1;
        padding: 10px 12px;
        border: none;
        background: #f9fafb;
        font-size: 13px;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .offline-pay-tab:first-child { border-right: 1px solid var(--border-color); }
    .offline-pay-tab.active {
        background: var(--green-dark, #6b9e65);
        color: white;
    }
    .offline-pay-tab:hover:not(.active) {
        background: #f3f4f6;
    }
    .offline-pay-panel { animation: fadeInPanel 0.2s ease; }
    @keyframes fadeInPanel { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

    .quick-amount-btn {
        padding: 6px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #f9fafb;
        font-size: 12px;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        color: #374151;
        cursor: pointer;
        transition: all 0.15s;
    }
    .quick-amount-btn:hover {
        border-color: var(--green-dark, #6b9e65);
        background: #f0fdf4;
        color: #166534;
    }
</style>
@endpush

@push('scripts')
{{-- Midtrans Snap JS --}}
@if($pesanan->metode_pembayaran === 'offline')
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com' }}/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif

<script>
    const totalHarga = {{ $pesanan->total_harga }};

    function switchPayTab(tab) {
        document.querySelectorAll('.offline-pay-tab').forEach(t => t.classList.remove('active'));
        document.querySelector(`.offline-pay-tab[data-tab="${tab}"]`).classList.add('active');

        document.getElementById('cashPayPanel').style.display = tab === 'cash' ? 'block' : 'none';
        document.getElementById('midtransPayPanel').style.display = tab === 'midtrans' ? 'block' : 'none';
    }

    function hitungKembalian() {
        const input = document.getElementById('jumlahCashInput');
        const bayar = parseFloat(input.value) || 0;
        const selisih = bayar - totalHarga;
        const kembalianBox = document.getElementById('kembalianBox');
        const kurangBox = document.getElementById('kurangBox');
        const btn = document.getElementById('cashPayBtn');

        if (bayar <= 0) {
            kembalianBox.style.display = 'none';
            kurangBox.style.display = 'none';
            btn.disabled = true;
            return;
        }

        if (selisih >= 0) {
            kembalianBox.style.display = 'block';
            kurangBox.style.display = 'none';
            document.getElementById('kembalianAmount').textContent = 'Rp ' + numberFormat(selisih);
            btn.disabled = false;
            document.getElementById('cashPayBtnText').textContent = selisih > 0 
                ? 'Konfirmasi — Kembalian Rp ' + numberFormat(selisih) 
                : 'Konfirmasi Pembayaran Cash (Uang Pas)';
        } else {
            kembalianBox.style.display = 'none';
            kurangBox.style.display = 'block';
            document.getElementById('kurangAmount').textContent = 'Rp ' + numberFormat(Math.abs(selisih));
            btn.disabled = true;
        }
    }

    function setQuickAmount(amount) {
        document.getElementById('jumlahCashInput').value = amount;
        hitungKembalian();
    }

    function numberFormat(num) {
        return new Intl.NumberFormat('id-ID').format(Math.round(num));
    }

    // Midtrans form handler — intercept dan buka snap popup
    document.addEventListener('DOMContentLoaded', function () {
        const midtransForm = document.getElementById('midtransPayForm');
        if (midtransForm) {
            midtransForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Submit form buat dapetin snap token
                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    redirect: 'follow',
                })
                .then(response => response.text())
                .then(html => {
                    // Parse redirect URL buat ambil snap token dari session
                    // Lebih simple: submit biasa aja, nanti redirect balik pake snap_token di session
                    midtransForm.submit();
                })
                .catch(() => midtransForm.submit());
            });
        }

        // Kalo ada snap token dari session (habis redirect), langsung buka popup
        @if(session('snap_token'))
        window.snap.pay('{{ session('snap_token') }}', {
            onSuccess: function(result) {
                // Kirim callback ke server
                fetch('{{ route("admin.pesanan.offlineMidtransCallback", $pesanan->id_pesanan) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        transaction_id: result.transaction_id,
                        order_id: result.order_id,
                        payment_type: result.payment_type,
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal memproses pembayaran.');
                        window.location.reload();
                    }
                })
                .catch(() => window.location.reload());
            },
            onPending: function(result) {
                alert('Pembayaran masih pending. Cek status nanti.');
            },
            onError: function(result) {
                alert('Pembayaran gagal.');
            },
            onClose: function() {
                // User nutup popup tanpa bayar
            },
        });
        @endif

        // Cash form confirmation
        const cashForm = document.getElementById('cashPayForm');
        if (cashForm) {
            cashForm.addEventListener('submit', function(e) {
                const bayar = parseFloat(document.getElementById('jumlahCashInput').value) || 0;
                const kembalian = bayar - totalHarga;
                let msg = 'Konfirmasi pembayaran cash?\n\n';
                msg += 'Total: Rp ' + numberFormat(totalHarga) + '\n';
                msg += 'Dibayar: Rp ' + numberFormat(bayar) + '\n';
                if (kembalian > 0) msg += 'Kembalian: Rp ' + numberFormat(kembalian);
                else msg += '(Uang Pas)';

                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush
