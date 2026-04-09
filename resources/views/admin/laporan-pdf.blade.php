<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan {{ $bulanNama[$bulan] }} {{ $tahun }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .container { padding: 30px 40px; }

        /* Bagian Header */
        .header { display: table; width: 100%; margin-bottom: 25px; border-bottom: 3px solid #2d6a4f; padding-bottom: 15px; }
        .header-left { display: table-cell; vertical-align: middle; }
        .header-left h1 { font-size: 22px; color: #2d6a4f; }
        .header-left p { font-size: 11px; color: #888; margin-top: 2px; }
        .header-right { display: table-cell; text-align: right; vertical-align: middle; }
        .header-right h2 { font-size: 16px; color: #2d6a4f; text-transform: uppercase; letter-spacing: 1px; }
        .header-right .period { font-size: 13px; color: #555; margin-top: 4px; font-weight: 600; }
        .header-right .generated { font-size: 10px; color: #999; margin-top: 2px; }

        /* Kartu Ringkasan */
        .section-title { font-size: 14px; font-weight: 700; color: #2d6a4f; margin: 20px 0 10px; padding-bottom: 5px; border-bottom: 1px solid #e0e0e0; }
        .summary-grid { display: table; width: 100%; margin-bottom: 20px; }
        .summary-row { display: table-row; }
        .summary-cell { display: table-cell; width: 33.33%; padding: 8px; }
        .summary-box { background: #f8faf8; border: 1px solid #e8f0e8; border-radius: 6px; padding: 12px 14px; }
        .summary-box .label { font-size: 10px; text-transform: uppercase; color: #888; letter-spacing: 0.5px; }
        .summary-box .value { font-size: 18px; font-weight: 700; color: #2d6a4f; margin-top: 2px; }
        .summary-box .sub { font-size: 10px; color: #999; margin-top: 2px; }
        .summary-box .trend-up { color: #27ae60; }
        .summary-box .trend-down { color: #e74c3c; }

        /* Styling Tabel */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table thead th { background: #2d6a4f; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; }
        table thead th:last-child, table thead th.right { text-align: right; }
        table tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
        table tbody td:last-child, table tbody td.right { text-align: right; }
        table tbody td.center { text-align: center; }
        table tbody tr:nth-child(even) { background: #fafafa; }

        /* Badge peringkat */
        .rank-1 { background: #FFD700; color: #333; padding: 2px 7px; border-radius: 10px; font-weight: 700; font-size: 10px; }
        .rank-2 { background: #C0C0C0; color: #333; padding: 2px 7px; border-radius: 10px; font-weight: 700; font-size: 10px; }
        .rank-3 { background: #CD7F32; color: #fff; padding: 2px 7px; border-radius: 10px; font-weight: 700; font-size: 10px; }

        /* Status pesanan */
        .status-grid { display: table; width: 100%; margin-bottom: 15px; }
        .status-cell { display: table-cell; padding: 4px 8px; }
        .status-item { background: #f5f5f5; border-radius: 6px; padding: 8px 12px; text-align: center; }
        .status-item .status-name { font-size: 10px; color: #888; text-transform: capitalize; }
        .status-item .status-count { font-size: 16px; font-weight: 700; margin-top: 2px; }
        .status-menunggu { color: #f39c12; }
        .status-diproses { color: #3498db; }
        .status-dikirim { color: #8e44ad; }
        .status-selesai { color: #27ae60; }
        .status-dibatalkan { color: #e74c3c; }

        /* Bagian Footer */
        .footer { margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 12px; }
        .footer p { font-size: 10px; color: #999; }

        /* Buat pindah halaman */
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>Book.com</h1>
                <p>Toko Buku Online Terpercaya</p>
            </div>
            <div class="header-right">
                <h2>Rekap Laporan</h2>
                <div class="period">{{ $bulanNama[$bulan] }} {{ $tahun }}</div>
                <div class="generated">Dicetak: {{ now()->format('d M Y, H:i') }}</div>
            </div>
        </div>

        <!-- Summary -->
        <div class="section-title">Ringkasan</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-box">
                        <div class="label">Total Pendapatan</div>
                        <div class="value">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div>
                        @php
                            $revDiff = $pendapatanBulanLalu > 0 ? (($pendapatanBulanIni - $pendapatanBulanLalu) / $pendapatanBulanLalu) * 100 : ($pendapatanBulanIni > 0 ? 100 : 0);
                        @endphp
                        <div class="sub {{ $revDiff >= 0 ? 'trend-up' : 'trend-down' }}">{{ $revDiff >= 0 ? '+' : '' }}{{ round($revDiff, 1) }}% dari bulan lalu</div>
                    </div>
                </div>
                <div class="summary-cell">
                    <div class="summary-box">
                        <div class="label">Total Pesanan</div>
                        <div class="value">{{ $totalPesanan }}</div>
                        @php
                            $ordDiff = $totalPesananBulanLalu > 0 ? (($totalPesanan - $totalPesananBulanLalu) / $totalPesananBulanLalu) * 100 : ($totalPesanan > 0 ? 100 : 0);
                        @endphp
                        <div class="sub {{ $ordDiff >= 0 ? 'trend-up' : 'trend-down' }}">{{ $ordDiff >= 0 ? '+' : '' }}{{ round($ordDiff, 1) }}% dari bulan lalu</div>
                    </div>
                </div>
                <div class="summary-cell">
                    <div class="summary-box">
                        <div class="label">Pesanan Selesai</div>
                        <div class="value">{{ $pesananSelesai }}</div>
                        <div class="sub">dari {{ $totalPesanan }} pesanan</div>
                    </div>
                </div>
            </div>
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-box">
                        <div class="label">Dibatalkan</div>
                        <div class="value">{{ $pesananDibatalkan }}</div>
                        <div class="sub">{{ $totalPesanan > 0 ? round(($pesananDibatalkan / $totalPesanan) * 100, 1) : 0 }}% cancel rate</div>
                    </div>
                </div>
                <div class="summary-cell">
                    <div class="summary-box">
                        <div class="label">Sedang Diproses</div>
                        <div class="value">{{ $pesananDiproses }}</div>
                        <div class="sub">menunggu / diproses / dikirim</div>
                    </div>
                </div>
                <div class="summary-cell">
                    <div class="summary-box">
                        <div class="label">User Baru</div>
                        <div class="value">{{ $userBaru }}</div>
                        <div class="sub">pendaftaran bulan ini</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="section-title">Distribusi Status Pesanan</div>
        <div class="status-grid">
            @php
                $allStatuses = ['menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
            @endphp
            @foreach($allStatuses as $st)
            <div class="status-cell">
                <div class="status-item">
                    <div class="status-name">{{ $st }}</div>
                    <div class="status-count status-{{ $st }}">{{ $statusCounts[$st] ?? 0 }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Payment Methods -->
        @if($metodePembayaran->count() > 0)
        <div class="section-title">Metode Pembayaran</div>
        <table>
            <thead>
                <tr>
                    <th>Metode</th>
                    <th class="right">Transaksi</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metodePembayaran as $mp)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $mp->metode)) }}</td>
                    <td class="right">{{ $mp->jumlah }}x</td>
                    <td class="right">Rp {{ number_format($mp->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Daily Revenue Table -->
        <div class="section-title">Pendapatan & Pesanan Harian</div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th class="right">Pesanan</th>
                    <th class="right">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @php $totalDailyRevenue = 0; $totalDailyOrders = 0; @endphp
                @for($d = 0; $d < count($chartDailyLabels); $d++)
                    @if($chartDailyRevenue[$d] > 0 || $chartDailyOrders[$d] > 0)
                    <tr>
                        <td>{{ $chartDailyLabels[$d] }} {{ $bulanNama[$bulan] }} {{ $tahun }}</td>
                        <td class="right">{{ $chartDailyOrders[$d] }}</td>
                        <td class="right">Rp {{ number_format($chartDailyRevenue[$d], 0, ',', '.') }}</td>
                    </tr>
                    @php $totalDailyRevenue += $chartDailyRevenue[$d]; $totalDailyOrders += $chartDailyOrders[$d]; @endphp
                    @endif
                @endfor
                <tr style="background: #f0faf4; font-weight: 700;">
                    <td>Total</td>
                    <td class="right">{{ $totalDailyOrders }}</td>
                    <td class="right">Rp {{ number_format($totalDailyRevenue, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Bestselling Books -->
        @if($bukuTerlaris->count() > 0)
        <div class="page-break"></div>
        <div class="section-title">Buku Terlaris</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th class="right">Harga</th>
                    <th class="right">Terjual</th>
                    <th class="right">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bukuTerlaris as $index => $buku)
                <tr>
                    <td class="center">
                        @if($index < 3)
                            <span class="rank-{{ $index + 1 }}">{{ $index + 1 }}</span>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </td>
                    <td><strong>{{ $buku->judul }}</strong></td>
                    <td>{{ $buku->penulis }}</td>
                    <td class="right">Rp {{ number_format($buku->harga, 0, ',', '.') }}</td>
                    <td class="right">{{ $buku->total_terjual }} pcs</td>
                    <td class="right">Rp {{ number_format($buku->total_pendapatan, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Top Customers -->
        @if($topCustomers->count() > 0)
        <div class="section-title">Pelanggan Teratas</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th class="right">Pesanan</th>
                    <th class="right">Total Belanja</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topCustomers as $index => $cust)
                <tr>
                    <td class="center">
                        @if($index < 3)
                            <span class="rank-{{ $index + 1 }}">{{ $index + 1 }}</span>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </td>
                    <td><strong>{{ $cust->nama }}</strong></td>
                    <td>{{ $cust->email }}</td>
                    <td class="right">{{ $cust->total_pesanan }}</td>
                    <td class="right">Rp {{ number_format($cust->total_belanja, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Book.com — Rekap Laporan {{ $bulanNama[$bulan] }} {{ $tahun }}</p>
            <p>Dokumen ini dibuat secara otomatis dan sah tanpa tanda tangan</p>
        </div>
    </div>
</body>
</html>
