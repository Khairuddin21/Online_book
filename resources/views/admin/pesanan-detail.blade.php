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
                        @if($pesanan->metode_pembayaran === 'cod')
                            <span class="badge badge-yellow" style="font-weight:700;"><i class="fas fa-money-bill-wave"></i> COD</span>
                        @else
                            <span class="badge badge-blue" style="font-weight:700;"><i class="fas fa-credit-card"></i> Online (Midtrans)</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Status -->
        @if(!in_array($pesanan->status, ['selesai', 'dibatalkan']))
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <i class="fas fa-sync-alt"></i>
                <h3>Update Status</h3>
            </div>
            <div class="card-body">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;">Ubah status pesanan sesuai progres pengiriman.</p>
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
                    <div class="label">Jumlah</div>
                    <div class="value" style="font-weight: 700; color: var(--green-deeper);">Rp {{ number_format($pesanan->pembayaran->jumlah, 0, ',', '.') }}</div>
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

        <!-- COD Proof Section -->
        @if($pesanan->metode_pembayaran === 'cod')
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <i class="fas fa-camera"></i>
                <h3>Bukti COD</h3>
            </div>
            <div class="card-body">
                @if($pesanan->bukti_cod)
                    <div style="text-align: center; margin-bottom: 16px;">
                        <img src="{{ asset('storage/' . $pesanan->bukti_cod) }}" 
                             alt="Bukti COD" 
                             style="max-width: 100%; max-height: 300px; border-radius: 12px; border: 2px solid var(--border-color); cursor: pointer;"
                             onclick="window.open(this.src, '_blank')">
                    </div>
                    <p style="text-align: center; font-size: 12px; color: var(--text-muted); margin-bottom: 16px;">
                        <i class="fas fa-search-plus"></i> Klik gambar untuk memperbesar
                    </p>

                    @if($pesanan->status === 'dikirim' && $pesanan->pembayaran && $pesanan->pembayaran->status_verifikasi !== 'valid')
                    <div style="display: flex; gap: 8px;">
                        <form action="{{ route('admin.pesanan.verifyCod', $pesanan->id_pesanan) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Verifikasi bukti COD dan tandai pesanan selesai?')">
                            @csrf @method('PUT')
                            <input type="hidden" name="aksi" value="terima">
                            <button type="submit" class="btn btn-success" style="width: 100%; justify-content: center;">
                                <i class="fas fa-check-circle"></i> Verifikasi & Selesaikan
                            </button>
                        </form>
                        <form action="{{ route('admin.pesanan.verifyCod', $pesanan->id_pesanan) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Tolak bukti COD? Pembayaran ditandai invalid.')">
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
                            <i class="fas fa-check-circle"></i> COD Terverifikasi
                        </span>
                    </div>
                    @endif
                @else
                    <div style="text-align: center; padding: 30px 16px; color: var(--text-muted);">
                        <i class="fas fa-image" style="font-size: 36px; margin-bottom: 10px; display: block; opacity: 0.4;"></i>
                        <p style="margin: 0; font-size: 14px;">User belum mengunggah bukti COD</p>
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
</style>
@endpush
