@extends('admin.layout')

@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('content')

<!-- Filter Bar -->
<div class="laporan-filter">
    <div class="filter-info">
        <i class="fas fa-calendar-alt"></i>
        <span>Periode: <strong>{{ $bulanNama[$bulan] }} {{ $tahun }}</strong></span>
    </div>
    <form method="GET" action="{{ route('admin.laporan') }}" class="filter-form">
        <select name="bulan" class="filter-select">
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ $bulanNama[$i] }}</option>
            @endfor
        </select>
        <select name="tahun" class="filter-select">
            @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-filter">
            <i class="fas fa-search"></i> Tampilkan
        </button>
    </form>
</div>

<!-- Summary Cards -->
<div class="laporan-stats">
    <div class="lap-card lap-card-revenue">
        <div class="lap-card-icon">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="lap-card-info">
            <p class="lap-card-label">Total Pendapatan</p>
            <h3>Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h3>
            @php
                $revDiff = $pendapatanBulanLalu > 0 ? (($pendapatanBulanIni - $pendapatanBulanLalu) / $pendapatanBulanLalu) * 100 : ($pendapatanBulanIni > 0 ? 100 : 0);
            @endphp
            <span class="lap-trend {{ $revDiff >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="fas fa-arrow-{{ $revDiff >= 0 ? 'up' : 'down' }}"></i>
                {{ abs(round($revDiff, 1)) }}% dari bulan lalu
            </span>
        </div>
    </div>

    <div class="lap-card lap-card-orders">
        <div class="lap-card-icon">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="lap-card-info">
            <p class="lap-card-label">Total Pesanan</p>
            <h3>{{ $totalPesanan }}</h3>
            @php
                $ordDiff = $totalPesananBulanLalu > 0 ? (($totalPesanan - $totalPesananBulanLalu) / $totalPesananBulanLalu) * 100 : ($totalPesanan > 0 ? 100 : 0);
            @endphp
            <span class="lap-trend {{ $ordDiff >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="fas fa-arrow-{{ $ordDiff >= 0 ? 'up' : 'down' }}"></i>
                {{ abs(round($ordDiff, 1)) }}% dari bulan lalu
            </span>
        </div>
    </div>

    <div class="lap-card lap-card-done">
        <div class="lap-card-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="lap-card-info">
            <p class="lap-card-label">Pesanan Selesai</p>
            <h3>{{ $pesananSelesai }}</h3>
            <span class="lap-subtext">dari {{ $totalPesanan }} pesanan</span>
        </div>
    </div>

    <div class="lap-card lap-card-cancel">
        <div class="lap-card-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="lap-card-info">
            <p class="lap-card-label">Dibatalkan</p>
            <h3>{{ $pesananDibatalkan }}</h3>
            <span class="lap-subtext">{{ $totalPesanan > 0 ? round(($pesananDibatalkan / $totalPesanan) * 100, 1) : 0 }}% cancel rate</span>
        </div>
    </div>

    <div class="lap-card lap-card-process">
        <div class="lap-card-icon">
            <i class="fas fa-spinner"></i>
        </div>
        <div class="lap-card-info">
            <p class="lap-card-label">Sedang Diproses</p>
            <h3>{{ $pesananDiproses }}</h3>
            <span class="lap-subtext">menunggu / diproses / dikirim</span>
        </div>
    </div>

    <div class="lap-card lap-card-users">
        <div class="lap-card-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="lap-card-info">
            <p class="lap-card-label">User Baru</p>
            <h3>{{ $userBaru }}</h3>
            <span class="lap-subtext">pendaftaran bulan ini</span>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="laporan-charts">
    <div class="lap-chart-card">
        <h4><i class="fas fa-chart-area"></i> Pendapatan Harian</h4>
        <canvas id="dailyRevenueChart"></canvas>
    </div>
    <div class="lap-chart-card">
        <h4><i class="fas fa-chart-bar"></i> Pesanan Harian</h4>
        <canvas id="dailyOrdersChart"></canvas>
    </div>
</div>

<!-- Status + Metode Row -->
<div class="laporan-charts">
    <div class="lap-chart-card lap-chart-small">
        <h4><i class="fas fa-chart-pie"></i> Distribusi Status Pesanan</h4>
        <div class="chart-center-wrapper">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
    <div class="lap-chart-card lap-chart-small">
        <h4><i class="fas fa-credit-card"></i> Metode Pembayaran</h4>
        @if($metodePembayaran->count() > 0)
        <div class="metode-list">
            @foreach($metodePembayaran as $mp)
            <div class="metode-row">
                <div class="metode-info">
                    <span class="metode-name">{{ ucwords(str_replace('_', ' ', $mp->metode)) }}</span>
                    <span class="metode-count">{{ $mp->jumlah }}x transaksi</span>
                </div>
                <span class="metode-amount">Rp {{ number_format($mp->total, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state-mini">
            <i class="fas fa-receipt"></i>
            <p>Belum ada pembayaran di bulan ini</p>
        </div>
        @endif
    </div>
</div>

<!-- Buku Terlaris -->
<div class="lap-table-card">
    <h4><i class="fas fa-fire"></i> Buku Terlaris Bulan Ini</h4>
    @if($bukuTerlaris->count() > 0)
    <div class="table-responsive">
        <table class="lap-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Buku</th>
                    <th>Penulis</th>
                    <th>Harga</th>
                    <th>Terjual</th>
                    <th>Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bukuTerlaris as $index => $buku)
                <tr>
                    <td>
                        @if($index < 3)
                            <span class="rank-badge rank-{{ $index + 1 }}">{{ $index + 1 }}</span>
                        @else
                            <span class="rank-num">{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="buku-cell">
                            @if($buku->cover)
                                <img src="{{ Str::startsWith($buku->cover, 'http') ? $buku->cover : asset('storage/' . $buku->cover) }}" 
                                     alt="{{ $buku->judul }}" class="buku-thumb"
                                     onerror="this.src='https://via.placeholder.com/40x55?text=Buku'">
                            @else
                                <div class="buku-thumb-placeholder"><i class="fas fa-book"></i></div>
                            @endif
                            <span class="buku-title">{{ $buku->judul }}</span>
                        </div>
                    </td>
                    <td>{{ $buku->penulis }}</td>
                    <td>Rp {{ number_format($buku->harga, 0, ',', '.') }}</td>
                    <td><span class="sold-badge">{{ $buku->total_terjual }} pcs</span></td>
                    <td class="amount-cell">Rp {{ number_format($buku->total_pendapatan, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state-mini">
        <i class="fas fa-book-open"></i>
        <p>Belum ada buku terjual di bulan ini</p>
    </div>
    @endif
</div>

<!-- Top Customers -->
<div class="lap-table-card">
    <h4><i class="fas fa-crown"></i> Pelanggan Teratas</h4>
    @if($topCustomers->count() > 0)
    <div class="customer-grid">
        @foreach($topCustomers as $index => $cust)
        <div class="customer-card">
            <div class="customer-rank">{{ $index + 1 }}</div>
            <img src="https://ui-avatars.com/api/?name={{ urlencode($cust->nama) }}&background=6b9e65&color=fff&size=48&rounded=true" alt="{{ $cust->nama }}" class="customer-avatar">
            <div class="customer-info">
                <h5>{{ $cust->nama }}</h5>
                <p>{{ $cust->email }}</p>
            </div>
            <div class="customer-stats">
                <span class="cust-orders">{{ $cust->total_pesanan }} pesanan</span>
                <span class="cust-spent">Rp {{ number_format($cust->total_belanja, 0, ',', '.') }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state-mini">
        <i class="fas fa-users"></i>
        <p>Belum ada pelanggan di bulan ini</p>
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
/* Filter Bar */
.laporan-filter {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
    border-radius: 14px;
    padding: 18px 24px;
    margin-bottom: 28px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    flex-wrap: wrap;
    gap: 16px;
}

.filter-info {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
    color: #374151;
}

.filter-info i {
    color: var(--green-dark, #6b9e65);
    font-size: 18px;
}

.filter-form {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-select {
    padding: 10px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    color: #374151;
    background: #f9fafb;
    cursor: pointer;
    transition: border-color 0.2s;
}

.filter-select:focus {
    outline: none;
    border-color: var(--green-dark, #6b9e65);
}

.btn-filter {
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--green-dark, #6b9e65), var(--green-deeper, #4a7c44));
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.btn-filter:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(74, 124, 68, 0.3);
}

/* Summary Cards */
.laporan-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 28px;
}

.lap-card {
    background: white;
    border-radius: 14px;
    padding: 22px;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    transition: all 0.3s;
    border-left: 4px solid transparent;
}

.lap-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.lap-card-revenue { border-left-color: #27ae60; }
.lap-card-orders { border-left-color: #3498db; }
.lap-card-done { border-left-color: #2ecc71; }
.lap-card-cancel { border-left-color: #e74c3c; }
.lap-card-process { border-left-color: #f39c12; }
.lap-card-users { border-left-color: #9b59b6; }

.lap-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.lap-card-revenue .lap-card-icon { background: #e8f8ef; color: #27ae60; }
.lap-card-orders .lap-card-icon { background: #e8f4fd; color: #3498db; }
.lap-card-done .lap-card-icon { background: #eafaf1; color: #2ecc71; }
.lap-card-cancel .lap-card-icon { background: #fdedec; color: #e74c3c; }
.lap-card-process .lap-card-icon { background: #fef9e7; color: #f39c12; }
.lap-card-users .lap-card-icon { background: #f4ecf7; color: #9b59b6; }

.lap-card-info {
    flex: 1;
    min-width: 0;
}

.lap-card-label {
    font-size: 13px;
    color: #9ca3af;
    margin: 0 0 4px;
    font-weight: 500;
}

.lap-card-info h3 {
    font-size: 24px;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0 0 6px;
    word-break: break-word;
}

.lap-trend {
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 20px;
}

.trend-up { color: #27ae60; background: #e8f8ef; }
.trend-down { color: #e74c3c; background: #fdedec; }

.lap-subtext {
    font-size: 12px;
    color: #9ca3af;
}

/* Charts Row */
.laporan-charts {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 28px;
}

.lap-chart-card {
    background: white;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.lap-chart-card h4 {
    font-size: 16px;
    font-weight: 700;
    color: #374151;
    margin: 0 0 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.lap-chart-card h4 i {
    color: var(--green-dark, #6b9e65);
}

.chart-center-wrapper {
    max-width: 280px;
    height: 280px;
    margin: 0 auto;
    position: relative;
}

/* Metode Pembayaran */
.metode-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.metode-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #f9fafb;
    border-radius: 10px;
    transition: background 0.2s;
}

.metode-row:hover {
    background: #f0fdf4;
}

.metode-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.metode-name {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
}

.metode-count {
    font-size: 12px;
    color: #9ca3af;
}

.metode-amount {
    font-size: 14px;
    font-weight: 700;
    color: var(--green-dark, #6b9e65);
}

/* Tables */
.lap-table-card {
    background: white;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    margin-bottom: 28px;
}

.lap-table-card h4 {
    font-size: 16px;
    font-weight: 700;
    color: #374151;
    margin: 0 0 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.lap-table-card h4 i {
    color: var(--green-dark, #6b9e65);
}

.table-responsive {
    overflow-x: auto;
}

.lap-table {
    width: 100%;
    border-collapse: collapse;
}

.lap-table th {
    text-align: left;
    padding: 12px 16px;
    font-size: 12px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #f0f0f0;
    white-space: nowrap;
}

.lap-table td {
    padding: 14px 16px;
    font-size: 14px;
    color: #374151;
    border-bottom: 1px solid #f5f5f5;
    vertical-align: middle;
}

.lap-table tbody tr:hover {
    background: #f9fafb;
}

.buku-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.buku-thumb {
    width: 40px;
    height: 55px;
    border-radius: 6px;
    object-fit: cover;
    flex-shrink: 0;
}

.buku-thumb-placeholder {
    width: 40px;
    height: 55px;
    border-radius: 6px;
    background: #f0fdf4;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--green-dark, #6b9e65);
    flex-shrink: 0;
}

.buku-title {
    font-weight: 600;
    line-height: 1.3;
}

.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    font-size: 13px;
    font-weight: 800;
    color: white;
}

.rank-1 { background: linear-gradient(135deg, #f39c12, #e67e22); }
.rank-2 { background: linear-gradient(135deg, #95a5a6, #7f8c8d); }
.rank-3 { background: linear-gradient(135deg, #d68910, #b9770e); }

.rank-num {
    font-weight: 600;
    color: #9ca3af;
}

.sold-badge {
    display: inline-block;
    padding: 4px 10px;
    background: #e8f8ef;
    color: #27ae60;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.amount-cell {
    font-weight: 700;
    color: var(--green-dark, #6b9e65);
    white-space: nowrap;
}

/* Customer Grid */
.customer-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.customer-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 20px;
    background: #f9fafb;
    border-radius: 12px;
    transition: all 0.2s;
}

.customer-card:hover {
    background: #f0fdf4;
    transform: translateX(4px);
}

.customer-rank {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: var(--green-dark, #6b9e65);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
}

.customer-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    flex-shrink: 0;
}

.customer-info {
    flex: 1;
    min-width: 0;
}

.customer-info h5 {
    font-size: 14px;
    font-weight: 700;
    color: #374151;
    margin: 0 0 2px;
}

.customer-info p {
    font-size: 12px;
    color: #9ca3af;
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.customer-stats {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
    flex-shrink: 0;
}

.cust-orders {
    font-size: 12px;
    color: #6b7280;
}

.cust-spent {
    font-size: 14px;
    font-weight: 700;
    color: var(--green-dark, #6b9e65);
}

/* Empty State */
.empty-state-mini {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}

.empty-state-mini i {
    font-size: 36px;
    margin-bottom: 12px;
    opacity: 0.5;
}

.empty-state-mini p {
    font-size: 14px;
    margin: 0;
}

/* Responsive */
@media (max-width: 1200px) {
    .laporan-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .laporan-stats {
        grid-template-columns: 1fr;
    }
    .laporan-charts {
        grid-template-columns: 1fr;
    }
    .laporan-filter {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-form {
        flex-wrap: wrap;
    }
    .filter-select {
        flex: 1;
    }
    .btn-filter {
        flex: 1;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const green = '#6b9e65';
    const greenLight = 'rgba(107, 158, 101, 0.15)';
    const blue = '#3498db';
    const blueLight = 'rgba(52, 152, 219, 0.15)';

    // Daily Revenue Chart
    new Chart(document.getElementById('dailyRevenueChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartDailyLabels) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($chartDailyRevenue) !!},
                borderColor: green,
                backgroundColor: greenLight,
                fill: true,
                tension: 0.4,
                pointRadius: 2,
                pointHoverRadius: 6,
                pointBackgroundColor: green,
                borderWidth: 2.5,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return 'Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                        },
                        title: function(ctx) {
                            return 'Tanggal ' + ctx[0].label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(val) {
                            if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'jt';
                            if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'rb';
                            return 'Rp ' + val;
                        }
                    },
                    grid: { color: '#f0f0f0' }
                },
                x: {
                    grid: { display: false },
                    ticks: { maxTicksLimit: 15 }
                }
            }
        }
    });

    // Daily Orders Chart
    new Chart(document.getElementById('dailyOrdersChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartDailyLabels) !!},
            datasets: [{
                label: 'Pesanan',
                data: {!! json_encode($chartDailyOrders) !!},
                backgroundColor: blue,
                borderRadius: 4,
                barThickness: 'flex',
                maxBarThickness: 18,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: function(ctx) {
                            return 'Tanggal ' + ctx[0].label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: '#f0f0f0' }
                },
                x: {
                    grid: { display: false },
                    ticks: { maxTicksLimit: 15 }
                }
            }
        }
    });

    // Status Doughnut Chart
    const statusData = {!! json_encode($statusCounts) !!};
    const statusLabels = [];
    const statusValues = [];
    const statusColors = [];
    const colorMap = {
        'menunggu': '#f39c12',
        'diproses': '#3498db',
        'dikirim': '#8e44ad',
        'selesai': '#27ae60',
        'dibatalkan': '#e74c3c'
    };
    const labelMap = {
        'menunggu': 'Menunggu',
        'diproses': 'Diproses',
        'dikirim': 'Dikirim',
        'selesai': 'Selesai',
        'dibatalkan': 'Dibatalkan'
    };

    for (const [key, val] of Object.entries(statusData)) {
        statusLabels.push(labelMap[key] || key);
        statusValues.push(val);
        statusColors.push(colorMap[key] || '#95a5a6');
    }

    if (statusValues.length > 0) {
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: statusColors,
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, usePointStyle: true, pointStyle: 'circle', font: { size: 12 } }
                    }
                }
            }
        });
    } else {
        document.getElementById('statusChart').parentElement.innerHTML = '<div class="empty-state-mini"><i class="fas fa-chart-pie"></i><p>Belum ada data pesanan</p></div>';
    }
});
</script>
@endpush
