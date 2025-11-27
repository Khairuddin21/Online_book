@extends('admin.layout')

@section('title', 'Detail Pesan')
@section('page-title', 'Detail Pesan Kontak')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; color: #2c3e50; font-size: 24px;">
            <i class="fas fa-envelope"></i> Detail Pesan
        </h2>
        <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 14px;">Pesan #{{ $pesan->id_pesan }} dari {{ $pesan->user->nama ?? 'User' }}</p>
    </div>
    <div>
        <a href="{{ route('admin.pesan.index') }}" 
           class="btn"
           style="background: #95a5a6; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
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

<!-- Message Container -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
    <!-- Main Message Content -->
    <div>
        <!-- Message Header -->
        <div style="background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #ecf0f1;">
                <div style="flex: 1;">
                    <h3 style="margin: 0 0 10px 0; color: #2c3e50; font-size: 20px;">
                        {{ $pesan->subjek }}
                    </h3>
                    <div style="color: #7f8c8d; font-size: 14px;">
                        <i class="fas fa-calendar"></i> {{ $pesan->tanggal->format('d F Y, H:i') }} WIB
                    </div>
                </div>
                <div>
                    @if(is_null($pesan->balasan_admin))
                        <span style="background: #e74c3c; color: white; padding: 6px 16px; border-radius: 20px; font-weight: 600; font-size: 13px;">
                            <i class="fas fa-envelope"></i> Belum Dibaca
                        </span>
                    @else
                        <span style="background: #27ae60; color: white; padding: 6px 16px; border-radius: 20px; font-weight: 600; font-size: 13px;">
                            <i class="fas fa-check-circle"></i> Sudah Dibalas
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Message Content -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #3498db;">
                <h4 style="margin: 0 0 15px 0; color: #7f8c8d; font-size: 13px; text-transform: uppercase; font-weight: 600;">
                    Isi Pesan:
                </h4>
                <div style="color: #2c3e50; font-size: 15px; line-height: 1.8; white-space: pre-wrap;">{{ $pesan->isi_pesan }}</div>
            </div>
        </div>
        
        <!-- Admin Reply Section -->
        @if($pesan->balasan_admin)
        <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 2px solid #ecf0f1;">
                <i class="fas fa-reply" style="color: #27ae60; font-size: 20px;"></i>
                <h3 style="margin: 0; color: #2c3e50; font-size: 18px;">Balasan Admin</h3>
                <span style="color: #7f8c8d; font-size: 13px; margin-left: auto;">
                    <i class="fas fa-clock"></i> {{ $pesan->tanggal_balas->format('d F Y, H:i') }} WIB
                </span>
            </div>
            <div style="background: #e8f5e9; padding: 20px; border-radius: 8px; border-left: 4px solid #27ae60;">
                <div style="color: #2c3e50; font-size: 15px; line-height: 1.8; white-space: pre-wrap;">{{ $pesan->balasan_admin }}</div>
            </div>
        </div>
        @else
        <!-- Reply Form -->
        <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #ecf0f1;">
                <i class="fas fa-reply" style="color: #3498db; font-size: 20px;"></i>
                <h3 style="margin: 0; color: #2c3e50; font-size: 18px;">Balas Pesan</h3>
            </div>
            
            <form action="{{ route('admin.pesan.reply', $pesan->id_pesan) }}" method="POST">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-comment-dots"></i> Balasan Anda:
                    </label>
                    <textarea name="balasan" 
                              rows="8" 
                              placeholder="Ketik balasan untuk user di sini..."
                              style="width: 100%; padding: 15px; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 15px; font-family: inherit; resize: vertical; transition: all 0.3s;"
                              onfocus="this.style.borderColor='#3498db'; this.style.boxShadow='0 0 0 3px rgba(52, 152, 219, 0.1)'"
                              onblur="this.style.borderColor='#ecf0f1'; this.style.boxShadow='none'"
                              required>{{ old('balasan') }}</textarea>
                    @error('balasan')
                        <span style="color: #e74c3c; font-size: 13px; margin-top: 5px; display: block;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" 
                            style="flex: 1; background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white; padding: 14px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(39, 174, 96, 0.3)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <i class="fas fa-paper-plane"></i> Kirim Balasan
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
    
    <!-- Sidebar - User Info -->
    <div>
        <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h3 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 16px; border-bottom: 2px solid #3498db; padding-bottom: 10px;">
                <i class="fas fa-user"></i> Informasi Pengirim
            </h3>
            
            @if($pesan->user)
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; margin: 0 auto 15px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: 600; box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);">
                    {{ strtoupper(substr($pesan->user->nama, 0, 1)) }}
                </div>
                <h4 style="margin: 0; color: #2c3e50; font-size: 18px;">{{ $pesan->user->nama }}</h4>
                <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 13px;">
                    <span style="background: {{ $pesan->user->role == 'admin' ? '#e74c3c' : '#3498db' }}; color: white; padding: 3px 10px; border-radius: 12px; font-weight: 600; text-transform: uppercase; font-size: 11px;">
                        {{ $pesan->user->role }}
                    </span>
                </p>
            </div>
            
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="padding: 10px 0; color: #7f8c8d; border-top: 1px solid #ecf0f1;"><i class="fas fa-id-card" style="width: 20px;"></i> ID User:</td>
                    <td style="padding: 10px 0; color: #2c3e50; text-align: right; border-top: 1px solid #ecf0f1;"><strong>#{{ $pesan->user->id_user }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; color: #7f8c8d; border-top: 1px solid #ecf0f1;"><i class="fas fa-envelope" style="width: 20px;"></i> Email:</td>
                    <td style="padding: 10px 0; color: #2c3e50; text-align: right; border-top: 1px solid #ecf0f1; word-break: break-word;">{{ $pesan->user->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; color: #7f8c8d; border-top: 1px solid #ecf0f1;"><i class="fas fa-phone" style="width: 20px;"></i> No. HP:</td>
                    <td style="padding: 10px 0; color: #2c3e50; text-align: right; border-top: 1px solid #ecf0f1;">{{ $pesan->user->no_hp ?: '-' }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 10px 0; color: #7f8c8d; border-top: 1px solid #ecf0f1;">
                        <i class="fas fa-map-marker-alt" style="width: 20px;"></i> Alamat:
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 0 0 10px 0; color: #2c3e50; font-size: 13px;">
                        {{ $pesan->user->alamat ?: 'Alamat belum diisi' }}
                    </td>
                </tr>
            </table>
            @else
            <div style="text-align: center; padding: 20px; color: #999;">
                <i class="fas fa-user-slash" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 10px;"></i>
                <p style="margin: 0;">User tidak ditemukan</p>
            </div>
            @endif
        </div>
        
        <!-- Actions -->
        <div style="background: white; border-radius: 12px; padding: 20px; margin-top: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h3 style="margin: 0 0 15px 0; color: #2c3e50; font-size: 16px; border-bottom: 2px solid #3498db; padding-bottom: 10px;">
                <i class="fas fa-cog"></i> Aksi
            </h3>
            
            <form action="{{ route('admin.pesan.delete', $pesan->id_pesan) }}" 
                  method="POST" 
                  onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        style="width: 100%; background: #e74c3c; color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s;"
                        onmouseover="this.style.background='#c0392b'"
                        onmouseout="this.style.background='#e74c3c'">
                    <i class="fas fa-trash"></i> Hapus Pesan
                </button>
            </form>
        </div>
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

@media (max-width: 992px) {
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush
@endsection
