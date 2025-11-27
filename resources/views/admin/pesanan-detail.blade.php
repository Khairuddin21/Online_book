@extends('admin.layout')

@section('title', 'Detail Pesanan #'.$pesanan->id_pesanan)
@section('page-title', 'Detail Pesanan #'.$pesanan->id_pesanan)

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.dashboard') }}" style="color: #007bff; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <!-- Left Column: Order Details -->
    <div>
        <!-- Customer Info -->
        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 25px;">
            <h3 style="margin: 0 0 20px 0; color: #333; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-user-circle"></i> Informasi Pelanggan
            </h3>
            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 15px;">
                <div style="color: #666; font-weight: 600;">Nama:</div>
                <div>{{ $pesanan->user->nama }}</div>
                
                <div style="color: #666; font-weight: 600;">Email:</div>
                <div>{{ $pesanan->user->email }}</div>
                
                <div style="color: #666; font-weight: 600;">No. HP:</div>
                <div>{{ $pesanan->user->no_hp ?? '-' }}</div>
                
                <div style="color: #666; font-weight: 600;">Alamat:</div>
                <div>{{ $pesanan->user->alamat ?? '-' }}</div>
            </div>
        </div>

        <!-- Items -->
        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 20px 0; color: #333; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-shopping-bag"></i> Item Pesanan
            </h3>
            
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #e0e0e0;">
                        <th style="padding: 12px; text-align: left; color: #666; font-weight: 600;">Buku</th>
                        <th style="padding: 12px; text-align: center; color: #666; font-weight: 600;">Qty</th>
                        <th style="padding: 12px; text-align: right; color: #666; font-weight: 600;">Harga</th>
                        <th style="padding: 12px; text-align: right; color: #666; font-weight: 600;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanan->pesananDetails as $detail)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 15px;">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                @if($detail->buku->cover)
                                    <img src="{{ asset('storage/' . $detail->buku->cover) }}" 
                                         alt="{{ $detail->buku->judul }}"
                                         style="width: 50px; height: 70px; object-fit: cover; border-radius: 5px;">
                                @else
                                    <div style="width: 50px; height: 70px; background: #e0e0e0; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-book" style="color: #999;"></i>
                                    </div>
                                @endif
                                <div>
                                    <div style="font-weight: 600; color: #333;">{{ $detail->buku->judul }}</div>
                                    <div style="font-size: 13px; color: #999;">{{ $detail->buku->penulis }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 15px; text-align: center;">{{ $detail->jumlah }}</td>
                        <td style="padding: 15px; text-align: right;">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                        <td style="padding: 15px; text-align: right; font-weight: 600;">
                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                    
                    <tr style="background: #f8f9fa;">
                        <td colspan="3" style="padding: 15px; text-align: right; font-weight: 700; color: #333; font-size: 16px;">
                            TOTAL:
                        </td>
                        <td style="padding: 15px; text-align: right; font-weight: 700; color: #007bff; font-size: 18px;">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column: Order Summary -->
    <div>
        <!-- Order Info -->
        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 25px;">
            <h3 style="margin: 0 0 20px 0; color: #333; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-receipt"></i> Info Pesanan
            </h3>
            <div style="display: grid; gap: 15px;">
                <div>
                    <div style="color: #666; font-size: 13px; margin-bottom: 5px;">ID Pesanan</div>
                    <div style="font-weight: 600; font-size: 18px; color: #007bff;">#{{ $pesanan->id_pesanan }}</div>
                </div>
                
                <div>
                    <div style="color: #666; font-size: 13px; margin-bottom: 5px;">Tanggal Pesanan</div>
                    <div style="font-weight: 600;">{{ $pesanan->tanggal_pesanan->format('d F Y, H:i') }}</div>
                </div>
                
                <div>
                    <div style="color: #666; font-size: 13px; margin-bottom: 5px;">Status Pesanan</div>
                    <div>
                        @if($pesanan->status == 'menunggu')
                            <span style="background: #ffc107; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block;">
                                <i class="fas fa-clock"></i> Menunggu
                            </span>
                        @elseif($pesanan->status == 'diproses')
                            <span style="background: #17a2b8; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block;">
                                <i class="fas fa-spinner"></i> Diproses
                            </span>
                        @elseif($pesanan->status == 'dikirim')
                            <span style="background: #007bff; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block;">
                                <i class="fas fa-truck"></i> Dikirim
                            </span>
                        @elseif($pesanan->status == 'selesai')
                            <span style="background: #28a745; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block;">
                                <i class="fas fa-check-circle"></i> Selesai
                            </span>
                        @else
                            <span style="background: #dc3545; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block;">
                                <i class="fas fa-times-circle"></i> Dibatalkan
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        @if($pesanan->pembayaran)
        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 25px;">
            <h3 style="margin: 0 0 20px 0; color: #333; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-credit-card"></i> Info Pembayaran
            </h3>
            <div style="display: grid; gap: 15px;">
                <div>
                    <div style="color: #666; font-size: 13px; margin-bottom: 5px;">Metode Pembayaran</div>
                    <div style="font-weight: 600;">{{ ucfirst($pesanan->pembayaran->metode) }}</div>
                </div>
                
                <div>
                    <div style="color: #666; font-size: 13px; margin-bottom: 5px;">Jumlah Bayar</div>
                    <div style="font-weight: 600; color: #28a745; font-size: 18px;">
                        Rp {{ number_format($pesanan->pembayaran->jumlah, 0, ',', '.') }}
                    </div>
                </div>
                
                <div>
                    <div style="color: #666; font-size: 13px; margin-bottom: 5px;">Status Pembayaran</div>
                    <div>
                        @if($pesanan->pembayaran->status_verifikasi == 'valid')
                            <span style="background: #28a745; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block;">
                                <i class="fas fa-check"></i> Valid
                            </span>
                        @elseif($pesanan->pembayaran->status_verifikasi == 'invalid')
                            <span style="background: #dc3545; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block;">
                                <i class="fas fa-times"></i> Invalid
                            </span>
                        @else
                            <span style="background: #ffc107; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block;">
                                <i class="fas fa-clock"></i> Menunggu
                            </span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <div style="color: #666; font-size: 13px; margin-bottom: 5px;">Tanggal Pembayaran</div>
                    <div style="font-weight: 600;">{{ $pesanan->pembayaran->tanggal->format('d F Y, H:i') }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 20px 0; color: #333; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-tools"></i> Aksi
            </h3>
            
            <form action="{{ route('admin.pesanan.delete', $pesanan->id_pesanan) }}" 
                  method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus pesanan ini? Data tidak dapat dikembalikan!')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        style="width: 100%; background: #dc3545; color: white; padding: 12px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fas fa-trash-alt"></i> Hapus Pesanan
                </button>
            </form>
            
            <div style="margin-top: 15px; padding: 12px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; font-size: 13px; color: #856404;">
                <i class="fas fa-exclamation-triangle"></i> 
                Menghapus pesanan akan menghapus semua data terkait termasuk detail pesanan dan pembayaran.
            </div>
        </div>
    </div>
</div>
@endsection
