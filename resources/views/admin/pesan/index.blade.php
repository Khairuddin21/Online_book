@extends('admin.layout')

@section('title', 'Pesan Kontak')
@section('page-title', 'Pesan Kontak')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; color: #2c3e50; font-size: 24px;">Pesan Kontak</h2>
        <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 14px;">Kelola pesan dari pengguna</p>
    </div>
    <div>
        @php
            $totalPesan = \App\Models\PesanKontak::count();
            $belumDibaca = \App\Models\PesanKontak::whereNull('balasan_admin')->count();
        @endphp
        <span style="background: #3498db; color: white; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 14px;">
            <i class="fas fa-envelope"></i> Total: {{ $totalPesan }}
        </span>
        <span style="background: #e74c3c; color: white; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 14px; margin-left: 10px;">
            <i class="fas fa-envelope-open"></i> Belum Dibaca: {{ $belumDibaca }}
        </span>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<!-- Filter & Search Bar -->
<div class="filter-bar" style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <form action="{{ route('admin.pesan.index') }}" method="GET" style="display: grid; grid-template-columns: 1fr auto auto auto; gap: 10px;">
        <div style="position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #95a5a6;"></i>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Cari subjek, pesan, atau nama pengguna..." 
                   style="width: 100%; padding: 12px 15px 12px 45px; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 15px; transition: all 0.3s;">
        </div>
        
        <select name="status" style="padding: 12px 15px; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 15px; cursor: pointer; min-width: 180px;">
            <option value="">Semua Status</option>
            <option value="belum_dibaca" {{ request('status') == 'belum_dibaca' ? 'selected' : '' }}>Belum Dibaca</option>
            <option value="sudah_dibaca" {{ request('status') == 'sudah_dibaca' ? 'selected' : '' }}>Sudah Dibaca</option>
        </select>
        
        <button type="submit" class="btn btn-primary" style="background: #3498db; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        @if(request('search') || request('status'))
        <a href="{{ route('admin.pesan.index') }}" class="btn" style="background: #95a5a6; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center;">
            <i class="fas fa-times"></i> Reset
        </a>
        @endif
    </form>
</div>

<!-- Messages Table -->
<div class="data-table">
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th style="width: 60px; text-align: center;">Status</th>
                <th>Pengirim</th>
                <th>Subjek</th>
                <th>Isi Pesan</th>
                <th style="width: 150px;">Tanggal</th>
                <th style="text-align: center; width: 180px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesan as $msg)
            <tr style="{{ is_null($msg->balasan_admin) ? 'background: #fff3cd;' : '' }}">
                <td><strong>#{{ $msg->id_pesan }}</strong></td>
                <td style="text-align: center;">
                    @if(is_null($msg->balasan_admin))
                        <i class="fas fa-envelope" style="color: #e74c3c; font-size: 18px;" title="Belum dibaca"></i>
                    @else
                        <i class="fas fa-envelope-open" style="color: #27ae60; font-size: 18px;" title="Sudah dibaca"></i>
                    @endif
                </td>
                <td>
                    <div style="font-weight: 600; color: #2c3e50; margin-bottom: 4px;">
                        {{ $msg->user->nama ?? 'User Deleted' }}
                    </div>
                    <div style="font-size: 13px; color: #7f8c8d;">
                        <i class="fas fa-envelope"></i> {{ $msg->user->email ?? '-' }}
                    </div>
                </td>
                <td>
                    <strong style="color: #2c3e50;">{{ $msg->subjek }}</strong>
                </td>
                <td>
                    <div style="color: #7f8c8d; font-size: 14px; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ Str::limit($msg->isi_pesan, 80) }}
                    </div>
                </td>
                <td>
                    <div style="font-size: 13px; color: #7f8c8d;">
                        <i class="fas fa-calendar"></i> {{ $msg->tanggal->format('d M Y') }}
                    </div>
                    <div style="font-size: 12px; color: #95a5a6;">
                        {{ $msg->tanggal->format('H:i') }}
                    </div>
                </td>
                <td>
                    <div style="display: flex; gap: 6px; justify-content: center;">
                        <a href="{{ route('admin.pesan.show', $msg->id_pesan) }}" 
                           class="btn btn-sm"
                           style="background: #3498db; color: white; padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600; text-decoration: none;">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                        
                        <form action="{{ route('admin.pesan.delete', $msg->id_pesan) }}" 
                              method="POST" 
                              style="display: inline;"
                              onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm"
                                    style="background: #e74c3c; color: white; padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 60px; color: #999;">
                    <i class="fas fa-inbox" style="font-size: 64px; margin-bottom: 15px; opacity: 0.3; display: block;"></i>
                    <p style="font-size: 16px; margin: 0;">
                        @if(request('search') || request('status'))
                            Pesan tidak ditemukan
                        @else
                            Belum ada pesan kontak
                        @endif
                    </p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($pesan->hasPages())
<div class="pagination-wrapper" style="margin-top: 25px; display: flex; justify-content: center;">
    <div class="pagination">
        @if ($pesan->onFirstPage())
            <span class="disabled">&laquo;</span>
        @else
            <a href="{{ $pesan->appends(request()->query())->previousPageUrl() }}" rel="prev">&laquo;</a>
        @endif

        @foreach ($pesan->getUrlRange(1, $pesan->lastPage()) as $page => $url)
            @if ($page == $pesan->currentPage())
                <span class="active">{{ $page }}</span>
            @else
                <a href="{{ $pesan->appends(request()->query())->url($page) }}">{{ $page }}</a>
            @endif
        @endforeach

        @if ($pesan->hasMorePages())
            <a href="{{ $pesan->appends(request()->query())->nextPageUrl() }}" rel="next">&raquo;</a>
        @else
            <span class="disabled">&raquo;</span>
        @endif
    </div>
</div>
@endif

@push('styles')
<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.filter-bar input:focus,
.filter-bar select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.pagination {
    display: flex;
    gap: 5px;
}

.pagination a,
.pagination span {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    color: #2c3e50;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.pagination a:hover {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.pagination span.active {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.pagination span.disabled {
    background: #ecf0f1;
    color: #bdc3c7;
    cursor: not-allowed;
}

@media (max-width: 992px) {
    .filter-bar form {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush
@endsection
