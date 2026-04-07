@extends('admin.layout')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-info">
            <h3>{{ $totalBuku ?? 0 }}</h3>
            <p>Total Buku</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-book"></i>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-info">
            <h3>{{ $totalPesanan ?? 0 }}</h3>
            <p>Total Pesanan</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-info">
            <h3>{{ $pesananDibatalkan ?? 0 }}</h3>
            <p>Dibatalkan</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-times-circle"></i>
        </div>
    </div>

    <div class="stat-card danger">
        <div class="stat-info">
            <h3>Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h3>
            <p>Total Pendapatan</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-money-bill-wave"></i>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 28px;">
    <!-- Revenue Chart -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-chart-line"></i>
            <h3>Pendapatan 6 Bulan Terakhir</h3>
        </div>
        <div class="card-body" style="padding: 20px;">
            <canvas id="revenueChart" height="260"></canvas>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-chart-pie"></i>
            <h3>Distribusi Status</h3>
        </div>
        <div class="card-body" style="padding: 20px; display: flex; align-items: center; justify-content: center;">
            <canvas id="statusChart" height="260"></canvas>
        </div>
    </div>
</div>

<!-- Orders Chart -->
<div class="card" style="margin-bottom: 28px;">
    <div class="card-header">
        <i class="fas fa-chart-bar"></i>
        <h3>Jumlah Pesanan 6 Bulan Terakhir</h3>
    </div>
    <div class="card-body" style="padding: 20px;">
        <canvas id="ordersChart" height="160"></canvas>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="data-table">
    <h2><i class="fas fa-clock" style="color: var(--green-dark); margin-right: 8px;"></i>Pesanan Terbaru</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th style="text-align: right;">Total</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesananTerbaru ?? [] as $pesanan)
            <tr>
                <td><strong style="color: var(--green-dark);">#{{ $pesanan->id_pesanan }}</strong></td>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="avatar" style="width: 34px; height: 34px; font-size: 13px; border-radius: 8px;">
                            {{ strtoupper(substr($pesanan->user->nama, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 14px;">{{ $pesanan->user->nama }}</div>
                        </div>
                    </div>
                </td>
                <td style="color: var(--text-muted); font-size: 13px;">
                    {{ $pesanan->tanggal_pesanan->format('d M Y, H:i') }}
                </td>
                <td style="text-align: right; font-weight: 700; color: var(--green-deeper);">
                    Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                </td>
                <td style="text-align: center;">
                    <span class="badge status-{{ $pesanan->status }}">
                        @switch($pesanan->status)
                            @case('menunggu') <i class="fas fa-clock"></i> Menunggu @break
                            @case('diproses') <i class="fas fa-box"></i> Diproses @break
                            @case('dikirim') <i class="fas fa-truck"></i> Dikirim @break
                            @case('selesai') <i class="fas fa-check-circle"></i> Selesai @break
                            @case('dibatalkan') <i class="fas fa-times-circle"></i> Dibatalkan @break
                            @default {{ $pesanan->status }}
                        @endswitch
                    </span>
                </td>
                <td style="text-align: center;">
                    <div style="display: flex; gap: 6px; justify-content: center;">
                        <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                        <form action="{{ route('admin.pesanan.delete', $pesanan->id_pesanan) }}"
                              method="POST" style="display: inline;"
                              onsubmit="return confirm('Yakin ingin menghapus pesanan #{{ $pesanan->id_pesanan }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada pesanan</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    const chartLabels = {!! json_encode($chartLabels ?? []) !!};
    const chartRevenue = {!! json_encode($chartRevenue ?? []) !!};
    const chartOrders = {!! json_encode($chartOrders ?? []) !!};
    const statusCounts = {!! json_encode($statusCounts ?? (object)[]) !!};

    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: chartRevenue,
                borderColor: '#6b9e65',
                backgroundColor: 'rgba(107, 158, 101, 0.15)',
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#4a7c44',
                pointRadius: 5,
                pointHoverRadius: 7,
                borderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'Rp ' + (v / 1000).toLocaleString('id-ID') + 'K',
                        font: { size: 11 }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });

    // Orders Chart
    new Chart(document.getElementById('ordersChart'), {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Jumlah Pesanan',
                data: chartOrders,
                backgroundColor: 'rgba(107, 158, 101, 0.6)',
                borderColor: '#6b9e65',
                borderWidth: 1.5,
                borderRadius: 6,
                maxBarThickness: 50
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });

    // Status Distribution Chart
    const statusLabels = Object.keys(statusCounts).map(s => s.charAt(0).toUpperCase() + s.slice(1));
    const statusData = Object.values(statusCounts);
    const statusColors = {
        menunggu: '#f59e0b',
        diproses: '#3b82f6',
        dikirim: '#8b5cf6',
        selesai: '#22c55e',
        dibatalkan: '#ef4444'
    };
    const bgColors = Object.keys(statusCounts).map(s => statusColors[s] || '#94a3b8');

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: bgColors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 14, font: { size: 12 }, usePointStyle: true }
                }
            },
            cutout: '60%'
        }
    });
</script>
@endpush
