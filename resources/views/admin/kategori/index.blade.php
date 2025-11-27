@extends('admin.layout')

@section('title', 'Kelola Kategori Buku')
@section('page-title', 'Kategori Buku')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; color: #2c3e50; font-size: 24px;">Kelola Kategori Buku</h2>
        <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 14px;">Atur kategori untuk mengorganisir buku Anda</p>
    </div>
    <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary" style="background: #3498db; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
        <i class="fas fa-plus"></i> Tambah Kategori
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

<!-- Search Bar -->
<div class="search-bar" style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <form action="{{ route('admin.kategori.index') }}" method="GET" style="display: flex; gap: 10px;">
        <div style="flex: 1; position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #95a5a6;"></i>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Cari kategori..." 
                   style="width: 100%; padding: 12px 15px 12px 45px; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 15px; transition: all 0.3s;">
        </div>
        <button type="submit" class="btn btn-primary" style="background: #3498db; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-search"></i> Cari
        </button>
        @if(request('search'))
        <a href="{{ route('admin.kategori.index') }}" class="btn" style="background: #95a5a6; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center;">
            <i class="fas fa-times"></i> Reset
        </a>
        @endif
    </form>
</div>

<!-- Kategori Table -->
<div class="data-table">
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Nama Kategori</th>
                <th style="width: 150px; text-align: center;">Jumlah Buku</th>
                <th style="width: 200px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kategori as $item)
            <tr>
                <td><strong>#{{ $item->id_kategori }}</strong></td>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                            {{ strtoupper(substr($item->nama_kategori, 0, 1)) }}
                        </div>
                        <span style="font-weight: 600; color: #2c3e50;">{{ $item->nama_kategori }}</span>
                    </div>
                </td>
                <td style="text-align: center;">
                    <span style="background: #ecf0f1; color: #2c3e50; padding: 5px 15px; border-radius: 20px; font-weight: 600; font-size: 14px;">
                        {{ $item->buku_count }} Buku
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 8px; justify-content: center;">
                        <a href="{{ route('admin.kategori.edit', $item->id_kategori) }}" 
                           class="btn btn-sm"
                           style="background: #f39c12; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 5px; font-weight: 600;">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.kategori.destroy', $item->id_kategori) }}" 
                              method="POST" 
                              style="display: inline;"
                              onsubmit="return confirm('Yakin ingin menghapus kategori {{ $item->nama_kategori }}?{{ $item->buku_count > 0 ? ' Kategori ini memiliki ' . $item->buku_count . ' buku!' : '' }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-sm"
                                    style="background: #e74c3c; color: white; padding: 8px 15px; border-radius: 6px; border: none; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 5px; font-weight: 600;"
                                    {{ $item->buku_count > 0 ? 'disabled title="Tidak dapat menghapus kategori yang memiliki buku"' : '' }}>
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 60px; color: #999;">
                    <i class="fas fa-tags" style="font-size: 64px; margin-bottom: 15px; opacity: 0.3; display: block;"></i>
                    <p style="font-size: 16px; margin: 0;">
                        @if(request('search'))
                            Kategori "{{ request('search') }}" tidak ditemukan
                        @else
                            Belum ada kategori. <a href="{{ route('admin.kategori.create') }}" style="color: #3498db; font-weight: 600;">Tambah kategori pertama</a>
                        @endif
                    </p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($kategori->hasPages())
<div class="pagination-wrapper" style="margin-top: 25px; display: flex; justify-content: center;">
    <div class="pagination">
        {{-- Previous Page Link --}}
        @if ($kategori->onFirstPage())
            <span class="disabled">&laquo;</span>
        @else
            <a href="{{ $kategori->previousPageUrl() }}" rel="prev">&laquo;</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($kategori->getUrlRange(1, $kategori->lastPage()) as $page => $url)
            @if ($page == $kategori->currentPage())
                <span class="active">{{ $page }}</span>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($kategori->hasMorePages())
            <a href="{{ $kategori->nextPageUrl() }}" rel="next">&raquo;</a>
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

.search-bar input:focus {
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

button:disabled {
    opacity: 0.5;
    cursor: not-allowed !important;
}

@media (max-width: 768px) {
    .content-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .search-bar form {
        flex-direction: column;
    }
}
</style>
@endpush
@endsection
