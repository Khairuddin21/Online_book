@extends('admin.layout')

@section('title', 'Detail Pesan')
@section('page-title', 'Detail Pesan')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: var(--text-dark);">Detail Pesan</h2>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Lihat dan balas pesan dari pengguna</p>
    </div>
    <a href="{{ route('admin.pesan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
    <!-- Left: Message Content -->
    <div>
        <!-- Message Card -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header" style="justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-envelope"></i>
                    <h3>{{ $pesan->subjek }}</h3>
                </div>
                @if($pesan->balasan_admin)
                    <span class="badge badge-green"><i class="fas fa-check"></i> Sudah Dibalas</span>
                @else
                    <span class="badge badge-yellow"><i class="fas fa-clock"></i> Belum Dibalas</span>
                @endif
            </div>
            <div class="card-body">
                <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;">
                    <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($pesan->tanggal)->format('d F Y, H:i') }} WIB
                </div>
                <div style="font-size: 15px; line-height: 1.8; color: var(--text-dark); padding: 18px; background: var(--green-bg); border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                    {!! nl2br(e($pesan->isi_pesan)) !!}
                </div>
            </div>
        </div>

        <!-- Reply Section -->
        @if($pesan->balasan_admin)
        <div class="card">
            <div class="card-header">
                <i class="fas fa-reply"></i>
                <h3>Balasan Admin</h3>
            </div>
            <div class="card-body">
                <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
                    <i class="fas fa-calendar-alt"></i>
                    @if($pesan->tanggal_balas)
                        {{ \Carbon\Carbon::parse($pesan->tanggal_balas)->format('d F Y, H:i') }} WIB
                    @else
                        -
                    @endif
                </div>
                <div style="font-size: 15px; line-height: 1.8; color: var(--text-dark); padding: 18px; background: var(--success-light); border-radius: var(--radius-sm); border: 1px solid #bbf7d0;">
                    {!! nl2br(e($pesan->balasan_admin)) !!}
                </div>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-header">
                <i class="fas fa-reply"></i>
                <h3>Kirim Balasan</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pesan.reply', $pesan->id_pesan) }}" method="POST">
                    @csrf
                    <div class="form-group" style="margin-bottom: 16px;">
                        <textarea name="balasan" class="form-input {{ $errors->has('balasan') ? 'is-invalid' : '' }}" rows="5" placeholder="Tulis balasan untuk pengguna..." required>{{ old('balasan') }}</textarea>
                        @error('balasan')
                            <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                        <div class="form-hint">Minimal 10 karakter. Balasan akan ditampilkan di inbox pengguna.</div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim Balasan</button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Right: Sender Info -->
    <div>
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <i class="fas fa-user"></i>
                <h3>Informasi Pengirim</h3>
            </div>
            <div class="card-body" style="text-align: center;">
                <div class="avatar avatar-lg" style="margin: 0 auto 14px;">{{ strtoupper(substr($pesan->user->nama ?? 'U', 0, 1)) }}</div>
                <div style="font-size: 17px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">{{ $pesan->user->nama ?? 'Unknown' }}</div>
                <div style="margin-bottom: 16px;">
                    @if(($pesan->user->role ?? 'user') == 'admin')
                        <span class="badge badge-red"><i class="fas fa-shield-alt"></i> Admin</span>
                    @else
                        <span class="badge badge-blue"><i class="fas fa-user"></i> User</span>
                    @endif
                </div>

                <div style="text-align: left; background: var(--green-bg); padding: 16px; border-radius: var(--radius-sm);">
                    <div class="detail-grid" style="grid-template-columns: 60px 1fr; gap: 10px;">
                        <div class="label">Email</div>
                        <div class="value" style="font-size: 13px;">{{ $pesan->user->email ?? '-' }}</div>
                        <div class="label">No. HP</div>
                        <div class="value" style="font-size: 13px;">{{ $pesan->user->no_hp ?? '-' }}</div>
                        <div class="label">Alamat</div>
                        <div class="value" style="font-size: 13px;">{{ $pesan->user->alamat ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Action -->
        <form action="{{ route('admin.pesan.delete', $pesan->id_pesan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" style="width: 100%; justify-content: center;">
                <i class="fas fa-trash"></i> Hapus Pesan
            </button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 2fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush
