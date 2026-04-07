@extends('admin.layout')

@section('title', 'Tambah Kategori Buku')
@section('page-title', 'Tambah Kategori')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: var(--text-dark);">Tambah Kategori Baru</h2>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Buat kategori baru untuk mengorganisir buku</p>
    </div>
    <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="form-card" style="max-width: 700px;">
    <form action="{{ route('admin.kategori.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Nama Kategori <span style="color: var(--danger);">*</span></label>
            <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" required
                   placeholder="Contoh: Fiksi, Non-Fiksi, Teknologi"
                   class="form-input {{ $errors->has('nama_kategori') ? 'is-invalid' : '' }}">
            @error('nama_kategori')
                <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
            @enderror
            <div class="form-hint"><i class="fas fa-info-circle"></i> Nama kategori harus unik dan deskriptif</div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Kategori</button>
            <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
        </div>
    </form>
</div>

<div class="info-card" style="max-width: 700px; margin-top: 20px;">
    <h3><i class="fas fa-lightbulb"></i> Tips Membuat Kategori</h3>
    <ul>
        <li>Gunakan nama yang jelas dan mudah dipahami</li>
        <li>Hindari nama kategori yang terlalu umum atau terlalu spesifik</li>
        <li>Pastikan nama kategori unik dan tidak duplikat</li>
    </ul>
</div>
@endsection
