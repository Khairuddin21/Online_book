@extends('user.layout')

@section('title', 'Kategori Buku')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <section>
        <h1 class="section-title">Kategori Buku</h1>
        
        <div class="categories-grid" style="margin-top: 30px;">
            @forelse($categories as $category)
            <div class="category-card" onclick="window.location.href='{{ route('user.books', ['kategori' => $category->id_kategori]) }}'">
                <i class="fas fa-book"></i>
                <h3>{{ $category->nama_kategori }}</h3>
                <p>{{ $category->buku_count }} Buku</p>
                @if($category->deskripsi)
                <p style="font-size: 12px; color: #999; margin-top: 10px;">{{ Str::limit($category->deskripsi, 60) }}</p>
                @endif
            </div>
            @empty
            <div style="grid-column: 1/-1; text-align: center; color: #999; padding: 60px 20px;">
                <i class="fas fa-folder-open" style="font-size: 64px; display: block; margin-bottom: 20px; opacity: 0.3;"></i>
                <h3>Belum ada kategori</h3>
            </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
