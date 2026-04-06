@extends('user.layout')

@section('title', 'Kategori Buku')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <div class="page-header">
        <h1 class="page-title">Kategori Buku</h1>
        <p class="page-subtitle">Jelajahi koleksi buku berdasarkan kategori</p>
    </div>

    <div class="categories-grid">
        @forelse($categories as $category)
        <div class="category-card" onclick="window.location.href='{{ route('user.books', ['kategori' => $category->id_kategori]) }}'">
            <i class="fas fa-book"></i>
            <h3>{{ $category->nama_kategori }}</h3>
            <p>{{ $category->buku_count }} Buku</p>
            @if($category->deskripsi)
            <p class="category-desc">{{ Str::limit($category->deskripsi, 60) }}</p>
            @endif
        </div>
        @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 80px 20px;">
            <i class="fas fa-folder-open" style="font-size: 56px; color: var(--green-pastel); margin-bottom: 16px; display: block;"></i>
            <h3 style="color: var(--text-dark); margin-bottom: 8px;">Belum ada kategori</h3>
        </div>
        @endforelse
    </div>
</div>
@endsection
