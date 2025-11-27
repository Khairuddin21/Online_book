@extends('admin.layout')

@section('title', 'Edit Kategori Buku')
@section('page-title', 'Edit Kategori')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; color: #2c3e50; font-size: 24px;">Edit Kategori</h2>
        <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 14px;">Perbarui informasi kategori buku</p>
    </div>
    <a href="{{ route('admin.kategori.index') }}" class="btn" style="background: #95a5a6; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<div class="form-container" style="background: white; padding: 35px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); max-width: 700px;">
    <form action="{{ route('admin.kategori.update', $kategori->id_kategori) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group" style="margin-bottom: 25px;">
            <label for="nama_kategori" style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; font-size: 15px;">
                Nama Kategori <span style="color: #e74c3c;">*</span>
            </label>
            <input type="text" 
                   id="nama_kategori" 
                   name="nama_kategori" 
                   value="{{ old('nama_kategori', $kategori->nama_kategori) }}" 
                   required
                   placeholder="Contoh: Fiksi, Non-Fiksi, Teknologi, dll"
                   style="width: 100%; padding: 12px 15px; border: 2px solid {{ $errors->has('nama_kategori') ? '#e74c3c' : '#ecf0f1' }}; border-radius: 8px; font-size: 15px; transition: all 0.3s;"
                   onfocus="this.style.borderColor='#3498db'; this.style.boxShadow='0 0 0 3px rgba(52, 152, 219, 0.1)'"
                   onblur="this.style.borderColor='#ecf0f1'; this.style.boxShadow='none'">
            @error('nama_kategori')
                <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </span>
            @enderror
            <small style="color: #7f8c8d; font-size: 13px; margin-top: 5px; display: block;">
                <i class="fas fa-info-circle"></i> Masukkan nama kategori yang unik dan deskriptif
            </small>
        </div>

        <div class="form-actions" style="display: flex; gap: 10px; padding-top: 20px; border-top: 2px solid #ecf0f1;">
            <button type="submit" 
                    class="btn btn-primary" 
                    style="background: #f39c12; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;"
                    onmouseover="this.style.background='#e67e22'"
                    onmouseout="this.style.background='#f39c12'">
                <i class="fas fa-save"></i> Update Kategori
            </button>
            <a href="{{ route('admin.kategori.index') }}" 
               class="btn"
               style="background: #ecf0f1; color: #2c3e50; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;"
               onmouseover="this.style.background='#bdc3c7'"
               onmouseout="this.style.background='#ecf0f1'">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

<!-- Info Card -->
<div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); padding: 25px; border-radius: 12px; margin-top: 25px; color: white; max-width: 700px; box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);">
    <h3 style="margin: 0 0 15px 0; font-size: 18px;">
        <i class="fas fa-info-circle"></i> Informasi Kategori
    </h3>
    <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px; line-height: 1.8;">
        <strong>ID Kategori:</strong>
        <span>#{{ $kategori->id_kategori }}</span>
        
        <strong>Jumlah Buku:</strong>
        <span>{{ $kategori->buku_count ?? 0 }} buku menggunakan kategori ini</span>
    </div>
    
    @if(($kategori->buku_count ?? 0) > 0)
    <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin-top: 15px;">
        <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian:</strong> Mengubah nama kategori akan mempengaruhi {{ $kategori->buku_count }} buku yang menggunakan kategori ini.
    </div>
    @endif
</div>

@push('styles')
<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

@media (max-width: 768px) {
    .content-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions button,
    .form-actions a {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush
@endsection
