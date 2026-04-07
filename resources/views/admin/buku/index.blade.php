@extends('admin.layout')

@section('title', 'Kelola Buku')
@section('page-title', 'Kelola Buku')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: var(--text-dark);">Daftar Buku</h2>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">{{ $buku->total() }} buku tersedia</p>
    </div>
    <a href="{{ route('admin.buku.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Buku</a>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form action="{{ route('admin.buku.index') }}" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, ISBN..." style="flex: 1; min-width: 200px;">
        <select name="kategori" style="min-width: 170px;" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            @foreach($kategori as $kat)
                <option value="{{ $kat->id_kategori }}" {{ request('kategori') == $kat->id_kategori ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
        @if(request('search') || request('kategori'))
            <a href="{{ route('admin.buku.index') }}" class="btn btn-ghost btn-sm"><i class="fas fa-times"></i> Reset</a>
        @endif
    </form>
</div>

<!-- Books Table -->
<div class="data-table">
    @if($buku->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Cover</th>
                <th>Judul & Detail</th>
                <th>Penulis</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($buku as $b)
            <tr>
                <td>
                    @if($b->cover)
                        <img src="{{ $b->cover }}" alt="{{ $b->judul }}"
                             style="width: 50px; height: 68px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                    @else
                        <div style="width: 50px; height: 68px; background: var(--green-bg); border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color);">
                            <i class="fas fa-book" style="color: var(--text-light); font-size: 18px;"></i>
                        </div>
                    @endif
                </td>
                <td>
                    <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 2px;">{{ Str::limit($b->judul, 40) }}</div>
                    <div style="font-size: 12px; color: var(--text-muted);">ISBN: {{ $b->isbn }}</div>
                    <div style="font-size: 12px; color: var(--text-light);">{{ $b->penerbit }} · {{ $b->tahun_terbit }}</div>
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="avatar" style="width: 32px; height: 32px; font-size: 12px; border-radius: 8px;">{{ strtoupper(substr($b->penulis, 0, 1)) }}</div>
                        <span style="font-size: 13px;">{{ Str::limit($b->penulis, 25) }}</span>
                    </div>
                </td>
                <td>
                    <span class="badge badge-green">{{ $b->kategori->nama_kategori ?? '-' }}</span>
                </td>
                <td>
                    <span style="font-weight: 700; color: var(--green-deeper);">Rp {{ number_format($b->harga, 0, ',', '.') }}</span>
                </td>
                <td>
                    @if($b->stok > 10)
                        <span class="badge badge-green">{{ $b->stok }}</span>
                    @elseif($b->stok > 0)
                        <span class="badge badge-yellow">{{ $b->stok }}</span>
                    @else
                        <span class="badge badge-red">Habis</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <div style="display: flex; justify-content: center; gap: 6px;">
                        <a href="{{ route('admin.buku.edit', $b->id_buku) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.buku.destroy', $b->id_buku) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    @if($buku->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination">
            {{ $buku->appends(request()->query())->links('pagination::simple-bootstrap-4') }}
        </div>
    </div>
    @endif

    @else
    <div class="empty-state">
        <i class="fas fa-book"></i>
        <p>Belum ada buku tersedia. <a href="{{ route('admin.buku.create') }}">Tambah buku pertama</a></p>
    </div>
    @endif
</div>
@endsection
