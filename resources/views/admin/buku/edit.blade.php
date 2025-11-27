@extends('admin.layout')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; color: #2c3e50; font-size: 24px;">Edit Buku</h2>
        <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 14px;">Perbarui informasi buku</p>
    </div>
    <a href="{{ route('admin.buku.index') }}" class="btn" style="background: #95a5a6; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<div class="form-container" style="background: white; padding: 35px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <form action="{{ route('admin.buku.update', $buku->id_buku) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
            <!-- Left Column -->
            <div>
                <h3 style="color: #2c3e50; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #3498db; padding-bottom: 10px;">
                    <i class="fas fa-info-circle"></i> Informasi Dasar
                </h3>
                
                <!-- Judul -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; font-size: 15px;">
                        Judul Buku <span style="color: #e74c3c;">*</span>
                    </label>
                    <input type="text" 
                           name="judul" 
                           value="{{ old('judul', $buku->judul) }}" 
                           required
                           placeholder="Masukkan judul buku"
                           class="form-input"
                           style="width: 100%; padding: 12px 15px; border: 2px solid {{ $errors->has('judul') ? '#e74c3c' : '#ecf0f1' }}; border-radius: 8px; font-size: 15px;">
                    @error('judul')
                        <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
                
                <!-- ISBN -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; font-size: 15px;">
                        ISBN <span style="color: #e74c3c;">*</span>
                    </label>
                    <input type="text" 
                           name="isbn" 
                           value="{{ old('isbn', $buku->isbn) }}" 
                           required
                           placeholder="978-xxx-xxx-xxx-x"
                           class="form-input"
                           style="width: 100%; padding: 12px 15px; border: 2px solid {{ $errors->has('isbn') ? '#e74c3c' : '#ecf0f1' }}; border-radius: 8px; font-size: 15px;">
                    @error('isbn')
                        <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
                
                <!-- Penulis -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; font-size: 15px;">
                        Penulis <span style="color: #e74c3c;">*</span>
                    </label>
                    <input type="text" 
                           name="penulis" 
                           value="{{ old('penulis', $buku->penulis) }}" 
                           required
                           placeholder="Nama penulis"
                           class="form-input"
                           style="width: 100%; padding: 12px 15px; border: 2px solid {{ $errors->has('penulis') ? '#e74c3c' : '#ecf0f1' }}; border-radius: 8px; font-size: 15px;">
                    @error('penulis')
                        <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
                
                <!-- Penerbit -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; font-size: 15px;">
                        Penerbit <span style="color: #e74c3c;">*</span>
                    </label>
                    <input type="text" 
                           name="penerbit" 
                           value="{{ old('penerbit', $buku->penerbit) }}" 
                           required
                           placeholder="Nama penerbit"
                           class="form-input"
                           style="width: 100%; padding: 12px 15px; border: 2px solid {{ $errors->has('penerbit') ? '#e74c3c' : '#ecf0f1' }}; border-radius: 8px; font-size: 15px;">
                    @error('penerbit')
                        <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
                
                <!-- Tahun Terbit & Kategori -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; font-size: 15px;">
                            Tahun Terbit <span style="color: #e74c3c;">*</span>
                        </label>
                        <input type="number" 
                               name="tahun_terbit" 
                               value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" 
                               required
                               min="1900"
                               max="{{ date('Y') + 1 }}"
                               class="form-input"
                               style="width: 100%; padding: 12px 15px; border: 2px solid {{ $errors->has('tahun_terbit') ? '#e74c3c' : '#ecf0f1' }}; border-radius: 8px; font-size: 15px;">
                        @error('tahun_terbit')
                            <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; font-size: 15px;">
                            Kategori <span style="color: #e74c3c;">*</span>
                        </label>
                        <select name="id_kategori" 
                                required
                                class="form-input"
                                style="width: 100%; padding: 12px 15px; border: 2px solid {{ $errors->has('id_kategori') ? '#e74c3c' : '#ecf0f1' }}; border-radius: 8px; font-size: 15px; cursor: pointer;">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id_kategori }}" {{ old('id_kategori', $buku->id_kategori) == $kat->id_kategori ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kategori')
                            <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Right Column -->
            <div>
                <h3 style="color: #2c3e50; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #27ae60; padding-bottom: 10px;">
                    <i class="fas fa-dollar-sign"></i> Harga & Stok
                </h3>
                
                <!-- Harga & Stok -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; font-size: 15px;">
                            Harga (Rp) <span style="color: #e74c3c;">*</span>
                        </label>
                        <input type="number" 
                               name="harga" 
                               value="{{ old('harga', $buku->harga) }}" 
                               required
                               min="0"
                               step="0.01"
                               placeholder="0"
                               class="form-input"
                               style="width: 100%; padding: 12px 15px; border: 2px solid {{ $errors->has('harga') ? '#e74c3c' : '#ecf0f1' }}; border-radius: 8px; font-size: 15px;">
                        @error('harga')
                            <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; font-size: 15px;">
                            Stok <span style="color: #e74c3c;">*</span>
                        </label>
                        <input type="number" 
                               name="stok" 
                               value="{{ old('stok', $buku->stok) }}" 
                               required
                               min="0"
                               placeholder="0"
                               class="form-input"
                               style="width: 100%; padding: 12px 15px; border: 2px solid {{ $errors->has('stok') ? '#e74c3c' : '#ecf0f1' }}; border-radius: 8px; font-size: 15px;">
                        @error('stok')
                            <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
                
                <!-- Cover URL -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; font-size: 15px;">
                        URL Cover Buku
                    </label>
                    <input type="url" 
                           name="cover" 
                           value="{{ old('cover', $buku->cover) }}" 
                           placeholder="https://example.com/cover.jpg"
                           class="form-input"
                           style="width: 100%; padding: 12px 15px; border: 2px solid {{ $errors->has('cover') ? '#e74c3c' : '#ecf0f1' }}; border-radius: 8px; font-size: 15px;">
                    @error('cover')
                        <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                    <small style="color: #7f8c8d; font-size: 13px; margin-top: 5px; display: block;">
                        <i class="fas fa-info-circle"></i> Link gambar dari Google, Imgur, atau hosting lainnya
                    </small>
                    
                    @if($buku->cover)
                    <div style="margin-top: 10px;">
                        <img src="{{ $buku->cover }}" 
                             alt="Current Cover" 
                             style="width: 120px; height: 160px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);"
                             onerror="this.src='https://via.placeholder.com/120x160/95a5a6/ffffff?text=Error'">
                        <p style="font-size: 12px; color: #7f8c8d; margin-top: 5px;">Cover saat ini</p>
                    </div>
                    @endif
                </div>
                
                <!-- Sinopsis/Deskripsi -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; font-size: 15px;">
                        Sinopsis / Deskripsi
                    </label>
                    <textarea name="deskripsi" 
                              rows="5"
                              placeholder="Tuliskan sinopsis atau deskripsi buku..."
                              class="form-input"
                              style="width: 100%; padding: 12px 15px; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 15px; resize: vertical; font-family: inherit;">{{ old('deskripsi', $buku->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="form-actions" style="display: flex; gap: 10px; padding-top: 25px; margin-top: 25px; border-top: 2px solid #ecf0f1;">
            <button type="submit" 
                    class="btn btn-primary" 
                    style="background: #f39c12; color: white; padding: 14px 35px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;">
                <i class="fas fa-save"></i> Update Buku
            </button>
            <a href="{{ route('admin.buku.index') }}" 
               class="btn"
               style="background: #ecf0f1; color: #2c3e50; padding: 14px 30px; border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

<!-- Info Card -->
<div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); padding: 25px; border-radius: 12px; margin-top: 25px; color: white; box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);">
    <h3 style="margin: 0 0 15px 0; font-size: 18px;">
        <i class="fas fa-info-circle"></i> Informasi Buku
    </h3>
    <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px; line-height: 1.8;">
        <strong>ID Buku:</strong>
        <span>#{{ $buku->id_buku }}</span>
    </div>
</div>

@push('styles')
<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.form-input:focus {
    outline: none;
    border-color: #3498db !important;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

@media (max-width: 992px) {
    .content-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .form-container > form > div {
        grid-template-columns: 1fr !important;
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
