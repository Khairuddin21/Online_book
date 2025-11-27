@extends('admin.layout')

@section('title', 'Kelola Buku')
@section('page-title', 'Kelola Buku')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; color: #2c3e50; font-size: 24px;">Kelola Buku</h2>
        <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 14px;">Manajemen data buku di toko online</p>
    </div>
    <a href="{{ route('admin.buku.create') }}" class="btn btn-primary" style="background: #27ae60; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
        <i class="fas fa-plus"></i> Tambah Buku
    </a>
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
    <form action="{{ route('admin.buku.index') }}" method="GET" style="display: grid; grid-template-columns: 1fr auto auto auto; gap: 10px;">
        <div style="position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #95a5a6;"></i>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Cari judul, penulis, penerbit, ISBN..." 
                   style="width: 100%; padding: 12px 15px 12px 45px; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 15px; transition: all 0.3s;">
        </div>
        
        <select name="kategori" style="padding: 12px 15px; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 15px; cursor: pointer; min-width: 200px;">
            <option value="">Semua Kategori</option>
            @foreach($kategori as $kat)
                <option value="{{ $kat->id_kategori }}" {{ request('kategori') == $kat->id_kategori ? 'selected' : '' }}>
                    {{ $kat->nama_kategori }}
                </option>
            @endforeach
        </select>
        
        <button type="submit" class="btn btn-primary" style="background: #3498db; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        @if(request('search') || request('kategori'))
        <a href="{{ route('admin.buku.index') }}" class="btn" style="background: #95a5a6; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center;">
            <i class="fas fa-times"></i> Reset
        </a>
        @endif
    </form>
</div>

<!-- Buku Table -->
<div class="data-table">
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Cover</th>
                <th>Judul & ISBN</th>
                <th>Penulis</th>
                <th>Kategori</th>
                <th style="text-align: right;">Harga</th>
                <th style="text-align: center;">Stok</th>
                <th style="text-align: center; width: 200px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($buku as $item)
            <tr>
                <td>
                    <img src="{{ $item->cover ?: 'https://via.placeholder.com/80x120/3498db/ffffff?text=No+Cover' }}" 
                         alt="{{ $item->judul }}" 
                         style="width: 60px; height: 80px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);"
                         onerror="this.src='https://via.placeholder.com/80x120/95a5a6/ffffff?text=Error'">
                </td>
                <td>
                    <div style="font-weight: 600; color: #2c3e50; margin-bottom: 4px;">{{ $item->judul }}</div>
                    <div style="font-size: 13px; color: #7f8c8d;">
                        <i class="fas fa-barcode"></i> ISBN: {{ $item->isbn }}
                    </div>
                    <div style="font-size: 13px; color: #95a5a6; margin-top: 2px;">
                        {{ $item->penerbit }} ({{ $item->tahun_terbit }})
                    </div>
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px;">
                            {{ strtoupper(substr($item->penulis, 0, 1)) }}
                        </div>
                        <span style="color: #2c3e50;">{{ $item->penulis }}</span>
                    </div>
                </td>
                <td>
                    <span style="background: #ecf0f1; color: #2c3e50; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                        {{ $item->kategori->nama_kategori }}
                    </span>
                </td>
                <td style="text-align: right;">
                    <strong style="color: #27ae60; font-size: 15px;">Rp {{ number_format($item->harga, 0, ',', '.') }}</strong>
                </td>
                <td style="text-align: center;">
                    <span style="background: {{ $item->stok > 10 ? '#27ae60' : ($item->stok > 0 ? '#f39c12' : '#e74c3c') }}; color: white; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 13px;">
                        {{ $item->stok }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 6px; justify-content: center;">
                        <a href="{{ route('admin.buku.edit', $item->id_buku) }}" 
                           class="btn btn-sm"
                           style="background: #f39c12; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;"
                           title="Edit Buku">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.buku.destroy', $item->id_buku) }}" 
                              method="POST" 
                              style="display: inline;"
                              onsubmit="return confirm('Yakin ingin menghapus buku {{ addslashes($item->judul) }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-sm"
                                    style="background: #e74c3c; color: white; padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;"
                                    title="Hapus Buku">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 60px; color: #999;">
                    <i class="fas fa-book-open" style="font-size: 64px; margin-bottom: 15px; opacity: 0.3; display: block;"></i>
                    <p style="font-size: 16px; margin: 0;">
                        @if(request('search') || request('kategori'))
                            Buku tidak ditemukan
                        @else
                            Belum ada buku. <a href="{{ route('admin.buku.create') }}" style="color: #3498db; font-weight: 600;">Tambah buku pertama</a>
                        @endif
                    </p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($buku->hasPages())
<div class="pagination-wrapper" style="margin-top: 25px; display: flex; justify-content: center;">
    <div class="pagination">
        @if ($buku->onFirstPage())
            <span class="disabled">&laquo;</span>
        @else
            <a href="{{ $buku->appends(request()->query())->previousPageUrl() }}" rel="prev">&laquo;</a>
        @endif

        @foreach ($buku->getUrlRange(1, $buku->lastPage()) as $page => $url)
            @if ($page == $buku->currentPage())
                <span class="active">{{ $page }}</span>
            @else
                <a href="{{ $buku->appends(request()->query())->url($page) }}">{{ $page }}</a>
            @endif
        @endforeach

        @if ($buku->hasMorePages())
            <a href="{{ $buku->appends(request()->query())->nextPageUrl() }}" rel="next">&raquo;</a>
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
    .content-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .filter-bar form {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush
@endsection
