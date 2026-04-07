@extends('admin.layout')

@section('title', 'Edit Kategori Buku')
@section('page-title', 'Edit Kategori')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: var(--text-dark);">Edit Kategori</h2>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Perbarui informasi kategori buku</p>
    </div>
    <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="form-card" style="max-width: 700px;">
    <form action="{{ route('admin.kategori.update', $kategori->id_kategori) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Nama Kategori <span style="color: var(--danger);">*</span></label>
            <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required
                   placeholder="Contoh: Fiksi, Non-Fiksi, Teknologi"
                   class="form-input {{ $errors->has('nama_kategori') ? 'is-invalid' : '' }}">
            @error('nama_kategori')
                <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
            @enderror
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Kategori</button>
            <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
        </div>
    </form>
</div>

<div class="info-card" style="max-width: 700px; margin-top: 20px;">
    <h3><i class="fas fa-info-circle"></i> Informasi Kategori</h3>
    <div style="display: grid; grid-template-columns: 140px 1fr; gap: 8px; font-size: 14px; line-height: 1.8; opacity: 0.9;">
        <strong>ID Kategori:</strong> <span>#{{ $kategori->id_kategori }}</span>
        <strong>Jumlah Buku:</strong> <span>{{ $kategori->buku_count ?? 0 }} buku</span>
    </div>
    @if(($kategori->buku_count ?? 0) > 0)
    <div style="background: rgba(255,255,255,0.15); padding: 14px; border-radius: 8px; margin-top: 14px; font-size: 14px;">
        <i class="fas fa-exclamation-triangle"></i> Mengubah nama kategori akan mempengaruhi {{ $kategori->buku_count }} buku.
    </div>
    @endif
</div>
@endsection
