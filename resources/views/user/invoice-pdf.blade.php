<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $pesanan->id_pesanan }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 13px; color: #333; line-height: 1.5; }

        .invoice-container { padding: 40px; }

        /* Header */
        .invoice-top { display: table; width: 100%; margin-bottom: 30px; }
        .invoice-brand { display: table-cell; vertical-align: top; }
        .invoice-brand h1 { font-size: 26px; color: #2d6a4f; margin-bottom: 2px; }
        .invoice-brand p { font-size: 12px; color: #888; }
        .invoice-title { display: table-cell; text-align: right; vertical-align: top; }
        .invoice-title h2 { font-size: 22px; color: #2d6a4f; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-title .inv-number { font-size: 13px; color: #666; margin-top: 4px; }

        /* Info grid */
        .invoice-info { display: table; width: 100%; margin-bottom: 25px; }
        .info-col { display: table-cell; width: 50%; vertical-align: top; }
        .info-col h4 { font-size: 11px; text-transform: uppercase; color: #999; letter-spacing: 1px; margin-bottom: 6px; }
        .info-col p { margin-bottom: 2px; font-size: 13px; }

        /* Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .items-table thead th {
            background: #2d6a4f;
            color: #fff;
            padding: 10px 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .items-table thead th:last-child,
        .items-table thead th:nth-child(3),
        .items-table thead th:nth-child(4) { text-align: right; }
        .items-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }
        .items-table tbody td:last-child,
        .items-table tbody td:nth-child(3),
        .items-table tbody td:nth-child(4) { text-align: right; }
        .items-table tbody tr:nth-child(even) { background: #f9f9f9; }

        /* Totals */
        .totals { width: 280px; margin-left: auto; }
        .totals-row { display: table; width: 100%; padding: 6px 0; }
        .totals-row span { display: table-cell; }
        .totals-row span:last-child { text-align: right; font-weight: 600; }
        .totals-row.grand { border-top: 2px solid #2d6a4f; padding-top: 10px; margin-top: 4px; font-size: 16px; color: #2d6a4f; }

        /* Payment info */
        .payment-info { background: #f0faf4; border: 1px solid #d4edda; border-radius: 6px; padding: 14px 18px; margin-top: 25px; }
        .payment-info h4 { font-size: 12px; text-transform: uppercase; color: #2d6a4f; margin-bottom: 8px; letter-spacing: 0.5px; }
        .payment-info .info-row { display: table; width: 100%; margin-bottom: 3px; font-size: 12px; }
        .payment-info .info-row span { display: table-cell; }
        .payment-info .info-row span:first-child { color: #666; width: 140px; }

        /* Footer */
        .invoice-footer { margin-top: 40px; text-align: center; border-top: 1px solid #eee; padding-top: 15px; }
        .invoice-footer p { font-size: 11px; color: #999; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Bagian Header -->
        <div class="invoice-top">
            <div class="invoice-brand">
                <h1>Book.com</h1>
                <p>Toko Buku Online Terpercaya</p>
            </div>
            <div class="invoice-title">
                <h2>Invoice</h2>
                <div class="inv-number">#INV-{{ str_pad($pesanan->id_pesanan, 5, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <!-- Info Pesanan -->
        <div class="invoice-info">
            <div class="info-col">
                <h4>Ditagihkan Kepada</h4>
                <p><strong>{{ $pesanan->user->nama }}</strong></p>
                <p>{{ $pesanan->user->email }}</p>
                @if($pesanan->user->no_hp)
                    <p>{{ $pesanan->user->no_hp }}</p>
                @endif
            </div>
            <div class="info-col" style="text-align: right;">
                <h4>Detail Invoice</h4>
                <p><strong>Tanggal:</strong> {{ $pesanan->tanggal_pesanan ? $pesanan->tanggal_pesanan->format('d M Y, H:i') : now()->format('d M Y, H:i') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($pesanan->status) }}</p>
                <p><strong>Metode:</strong> {{ $pesanan->metode_pembayaran === 'offline' ? 'Payment Offline' : 'Online Payment' }}</p>
            </div>
        </div>

        <!-- Tabel Daftar Item -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Buku</th>
                    <th style="width: 90px;">Harga</th>
                    <th style="width: 50px;">Qty</th>
                    <th style="width: 110px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pesanan->details as $i => $detail)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $detail->buku->judul ?? 'Buku' }}</strong>
                        @if($detail->buku && $detail->buku->penulis)
                            <br><span style="font-size: 11px; color: #888;">{{ $detail->buku->penulis }}</span>
                        @endif
                    </td>
                    <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $detail->qty }}</td>
                    <td>Rp {{ number_format($detail->harga_satuan * $detail->qty, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Harga -->
        <div class="totals">
            <div class="totals-row grand">
                <span>Total</span>
                <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Info Pembayaran -->
        @if($pesanan->pembayaran)
        <div class="payment-info">
            <h4>Informasi Pembayaran</h4>
            @if($pesanan->pembayaran->midtrans_transaction_id)
            <div class="info-row">
                <span>ID Transaksi</span>
                <span>{{ $pesanan->pembayaran->midtrans_transaction_id }}</span>
            </div>
            @endif
            <div class="info-row">
                <span>Metode Pembayaran</span>
                <span>{{ ucfirst(str_replace('_', ' ', $pesanan->pembayaran->metode)) }}</span>
            </div>
            @if($pesanan->pembayaran->jumlah_dibayar)
            <div class="info-row">
                <span>Jumlah Dibayar</span>
                <span>Rp {{ number_format($pesanan->pembayaran->jumlah_dibayar, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span>Kembalian</span>
                <span>
                    @php $kembalian = $pesanan->pembayaran->jumlah_dibayar - $pesanan->pembayaran->jumlah; @endphp
                    {{ $kembalian > 0 ? 'Rp ' . number_format($kembalian, 0, ',', '.') : 'Rp 0 (Uang Pas)' }}
                </span>
            </div>
            @endif
            <div class="info-row">
                <span>Status</span>
                <span>{{ $pesanan->pembayaran->status_verifikasi === 'valid' ? 'Terverifikasi' : ucfirst($pesanan->pembayaran->status_verifikasi) }}</span>
            </div>
            <div class="info-row">
                <span>Tanggal Bayar</span>
                <span>{{ $pesanan->pembayaran->tanggal ? $pesanan->pembayaran->tanggal->format('d M Y, H:i') : '-' }}</span>
            </div>
        </div>
        @endif

        <!-- Bagian Footer -->
        <div class="invoice-footer">
            <p>Terima kasih telah berbelanja di Book.com</p>
            <p>Invoice ini dibuat secara otomatis dan sah tanpa tanda tangan</p>
        </div>
    </div>
</body>
</html>
