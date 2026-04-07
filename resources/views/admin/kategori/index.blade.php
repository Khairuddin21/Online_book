@extends('admin.layout')

@section('title', 'Kelola Kategori Buku')
@section('page-title', 'Kategori Buku')

@section('content')
<!-- Page Header -->
<div class="content-header">
    <div>
        <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: var(--text-dark);">Kelola Kategori</h2>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Atur kategori untuk mengorganisir buku</p>
    </div>
    <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Kategori
    </a>
</div>

<!-- Search Bar -->
<div class="filter-bar">
    <form action="{{ route('admin.kategori.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
        <div style="flex: 1; position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-light); font-size: 14px;"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..."
                   style="width: 100%; padding: 11px 16px 11px 40px; border: 2px solid var(--border-color); border-radius: var(--radius-sm); font-size: 14px; font-family: inherit; color: var(--text-dark); transition: all var(--transition);">
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
        @if(request('search'))
            <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Reset</a>
        @endif
    </form>
</div>

<!-- Table -->
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
                <td><strong style="color: var(--green-dark);">#{{ $item->id_kategori }}</strong></td>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="avatar" style="width: 36px; height: 36px; font-size: 14px; border-radius: 8px;">
                            {{ strtoupper(substr($item->nama_kategori, 0, 1)) }}
                        </div>
                        <span style="font-weight: 600;">{{ $item->nama_kategori }}</span>
                    </div>
                </td>
                <td style="text-align: center;">
                    <span class="badge badge-gray">{{ $item->buku_count }} Buku</span>
                </td>
                <td style="text-align: center;">
                    <div style="display: flex; gap: 6px; justify-content: center;">
                        <a href="{{ route('admin.kategori.edit', $item->id_kategori) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.kategori.destroy', $item->id_kategori) }}" method="POST" style="display: inline;"
                              onsubmit="return confirm('Yakin ingin menghapus kategori {{ $item->nama_kategori }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" {{ $item->buku_count > 0 ? 'disabled title=Tidak dapat menghapus kategori yang memiliki buku' : '' }}>
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">
                    <div class="empty-state">
                        <i class="fas fa-tags"></i>
                        <p>
                            @if(request('search'))
                                Kategori "{{ request('search') }}" tidak ditemukan
                            @else
                                Belum ada kategori. <a href="{{ route('admin.kategori.create') }}">Tambah kategori pertama</a>
                            @endif
                        </p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($kategori->hasPages())
<div class="pagination-wrapper">
    <div class="pagination">
        @if ($kategori->onFirstPage())
            <span class="disabled">&laquo;</span>
        @else
            <a href="{{ $kategori->previousPageUrl() }}">&laquo;</a>
        @endif
        @foreach ($kategori->getUrlRange(1, $kategori->lastPage()) as $page => $url)
            @if ($page == $kategori->currentPage())
                <span class="active">{{ $page }}</span>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach
        @if ($kategori->hasMorePages())
            <a href="{{ $kategori->nextPageUrl() }}">&raquo;</a>
        @else
            <span class="disabled">&raquo;</span>
        @endif
    </div>
</div>
@endif
@endsection
