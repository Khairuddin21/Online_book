@extends('admin.layout')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: var(--text-dark);">Edit Buku</h2>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Perbarui informasi buku</p>
    </div>
    <a href="{{ route('admin.buku.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<form action="{{ route('admin.buku.update', $buku->id_buku) }}" method="POST">
    @csrf
    @method('PUT')
    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 24px; align-items: start;">
        <!-- Left Column -->
        <div class="form-card">
            <div class="form-section-title"><i class="fas fa-book"></i> Informasi Buku</div>

            <div class="form-group">
                <label>Judul Buku <span style="color: var(--danger);">*</span></label>
                <input type="text" name="judul" value="{{ old('judul', $buku->judul) }}" required class="form-input {{ $errors->has('judul') ? 'is-invalid' : '' }}" placeholder="Masukkan judul buku">
                @error('judul') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>ISBN <span style="color: var(--danger);">*</span></label>
                <input type="text" name="isbn" value="{{ old('isbn', $buku->isbn) }}" required class="form-input {{ $errors->has('isbn') ? 'is-invalid' : '' }}" placeholder="Contoh: 978-602-XXX-XXX-X">
                @error('isbn') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Penulis <span style="color: var(--danger);">*</span></label>
                <input type="text" name="penulis" value="{{ old('penulis', $buku->penulis) }}" required class="form-input {{ $errors->has('penulis') ? 'is-invalid' : '' }}" placeholder="Nama penulis buku">
                @error('penulis') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Penerbit <span style="color: var(--danger);">*</span></label>
                <input type="text" name="penerbit" value="{{ old('penerbit', $buku->penerbit) }}" required class="form-input {{ $errors->has('penerbit') ? 'is-invalid' : '' }}" placeholder="Nama penerbit buku">
                @error('penerbit') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Tahun Terbit <span style="color: var(--danger);">*</span></label>
                    <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" required class="form-input" min="1900" max="{{ date('Y') + 1 }}">
                    @error('tahun_terbit') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Kategori <span style="color: var(--danger);">*</span></label>
                    <select name="id_kategori" required class="form-input {{ $errors->has('id_kategori') ? 'is-invalid' : '' }}">
                        <option value="">Pilih Kategori</option>
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id_kategori }}" {{ old('id_kategori', $buku->id_kategori) == $kat->id_kategori ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                    @error('id_kategori') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <div class="form-card" style="margin-bottom: 20px;">
                <div class="form-section-title"><i class="fas fa-tag"></i> Harga & Stok</div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label>Harga (Rp) <span style="color: var(--danger);">*</span></label>
                        <input type="number" name="harga" value="{{ old('harga', $buku->harga) }}" required class="form-input" min="0" step="1000">
                        @error('harga') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Stok <span style="color: var(--danger);">*</span></label>
                        <input type="number" name="stok" value="{{ old('stok', $buku->stok) }}" required class="form-input" min="0">
                        @error('stok') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Cover URL</label>
                    <input type="url" name="cover" value="{{ old('cover', $buku->cover) }}" class="form-input {{ $errors->has('cover') ? 'is-invalid' : '' }}" placeholder="https://example.com/cover.jpg">
                    <div class="form-hint">Masukkan URL gambar cover buku (opsional)</div>
                    @error('cover') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                @if($buku->cover)
                <div class="form-group">
                    <label>Preview Cover</label>
                    <div style="padding: 12px; background: var(--green-bg); border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: inline-block;">
                        <img src="{{ $buku->cover }}" alt="{{ $buku->judul }}" style="max-width: 120px; max-height: 160px; border-radius: 6px; object-fit: cover;">
                    </div>
                </div>
                @endif

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-input" rows="5" placeholder="Sinopsis atau deskripsi buku...">{{ old('deskripsi', $buku->deskripsi) }}</textarea>
                    @error('deskripsi') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
            </div>

            <div class="info-card">
                <h3><i class="fas fa-info-circle"></i> Informasi</h3>
                <div style="display: grid; grid-template-columns: 100px 1fr; gap: 6px; font-size: 14px; line-height: 1.9; opacity: 0.9;">
                    <strong>ID Buku:</strong> <span>#{{ $buku->id_buku }}</span>
                    <strong>ISBN:</strong> <span>{{ $buku->isbn }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions" style="margin-top: 24px; border-top: none;">
        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Buku</button>
        <a href="{{ route('admin.buku.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
    </div>
</form>
@endsection

@push('styles')
<style>
    @media (max-width: 768px) {
        form > div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush
