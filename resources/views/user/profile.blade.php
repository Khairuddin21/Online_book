@extends('user.layout')

@section('title', 'Profil Saya')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <div class="page-header">
        <h1 class="page-title">Profil Saya</h1>
        <p class="page-subtitle">Informasi akun dan data diri Anda</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- View Mode --}}
    <div class="profile-card" id="profileView">
        <div class="profile-header">
            <div class="profile-avatar">
                {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
            </div>
            <h2 class="profile-name">{{ Auth::user()->nama }}</h2>
            <p class="profile-email">{{ Auth::user()->email }}</p>
        </div>
        
        <div class="profile-body">
            <div class="profile-item">
                <i class="fas fa-user"></i>
                <div>
                    <div class="profile-item-label">Nama Lengkap</div>
                    <div class="profile-item-value">{{ Auth::user()->nama }}</div>
                </div>
            </div>
            <div class="profile-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <div class="profile-item-label">Email</div>
                    <div class="profile-item-value">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="profile-item">
                <i class="fas fa-phone"></i>
                <div>
                    <div class="profile-item-label">No. Telepon</div>
                    <div class="profile-item-value">{{ Auth::user()->no_hp ?? '-' }}</div>
                </div>
            </div>
            <div class="profile-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <div class="profile-item-label">Alamat</div>
                    <div class="profile-item-value">{{ Auth::user()->alamat ?? '-' }}</div>
                </div>
            </div>
            
            <div class="form-actions" style="margin-top: 24px; display: flex; gap: 12px;">
                <button class="btn btn-green" onclick="toggleEdit(true)">
                    <i class="fas fa-edit"></i> Edit Profil
                </button>
                <button class="btn btn-green" style="background: #6c757d; border-color: #6c757d;" onclick="togglePassword(true)">
                    <i class="fas fa-lock"></i> Ubah Password
                </button>
            </div>
        </div>
    </div>

    {{-- Edit Mode --}}
    <div class="profile-card" id="profileEdit" style="display: none;">
        <div class="profile-header">
            <div class="profile-avatar">
                {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
            </div>
            <h2 class="profile-name">Edit Profil</h2>
            <p class="profile-email">Perbarui informasi akun Anda</p>
        </div>
        
        <div class="profile-body">
            <form action="{{ route('user.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">
                        <i class="fas fa-user" style="color: var(--green-dark); margin-right: 6px;"></i> Nama Lengkap
                    </label>
                    <input type="text" name="nama" value="{{ old('nama', Auth::user()->nama) }}" 
                           class="form-control" style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">
                        <i class="fas fa-envelope" style="color: var(--green-dark); margin-right: 6px;"></i> Email
                    </label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" 
                           class="form-control" style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">
                        <i class="fas fa-phone" style="color: var(--green-dark); margin-right: 6px;"></i> No. Telepon
                    </label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', Auth::user()->no_hp) }}" 
                           class="form-control" style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" 
                           placeholder="Contoh: 08123456789">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">
                        <i class="fas fa-map-marker-alt" style="color: var(--green-dark); margin-right: 6px;"></i> Alamat
                    </label>
                    <textarea name="alamat" rows="3" class="form-control" 
                              style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; resize: vertical;"
                              placeholder="Masukkan alamat lengkap">{{ old('alamat', Auth::user()->alamat) }}</textarea>
                </div>

                <div class="form-actions" style="margin-top: 24px; display: flex; gap: 12px;">
                    <button type="submit" class="btn btn-green">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <button type="button" class="btn btn-green" style="background: #6c757d; border-color: #6c757d;" onclick="toggleEdit(false)">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Change Password Mode --}}
    <div class="profile-card" id="profilePassword" style="display: none;">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-lock" style="font-size: 36px;"></i>
            </div>
            <h2 class="profile-name">Ubah Password</h2>
            <p class="profile-email">Masukkan password lama dan password baru Anda</p>
        </div>
        
        <div class="profile-body">
            <form action="{{ route('user.profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">
                        <i class="fas fa-lock" style="color: var(--green-dark); margin-right: 6px;"></i> Password Lama
                    </label>
                    <input type="password" name="current_password" 
                           class="form-control" style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">
                        <i class="fas fa-key" style="color: var(--green-dark); margin-right: 6px;"></i> Password Baru
                    </label>
                    <input type="password" name="password" 
                           class="form-control" style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">
                        <i class="fas fa-key" style="color: var(--green-dark); margin-right: 6px;"></i> Konfirmasi Password Baru
                    </label>
                    <input type="password" name="password_confirmation" 
                           class="form-control" style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                </div>

                <div class="form-actions" style="margin-top: 24px; display: flex; gap: 12px;">
                    <button type="submit" class="btn btn-green">
                        <i class="fas fa-save"></i> Ubah Password
                    </button>
                    <button type="button" class="btn btn-green" style="background: #6c757d; border-color: #6c757d;" onclick="togglePassword(false)">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleEdit(show) {
    document.getElementById('profileView').style.display = show ? 'none' : 'block';
    document.getElementById('profileEdit').style.display = show ? 'block' : 'none';
    document.getElementById('profilePassword').style.display = 'none';
}

function togglePassword(show) {
    document.getElementById('profileView').style.display = show ? 'none' : 'block';
    document.getElementById('profilePassword').style.display = show ? 'block' : 'none';
    document.getElementById('profileEdit').style.display = 'none';
}
</script>
@endsection
