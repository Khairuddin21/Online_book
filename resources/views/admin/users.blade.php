@extends('admin.layout')

@section('title', 'Kelola Pengguna')
@section('page-title', 'Kelola Pengguna')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; color: #2c3e50; font-size: 24px;">Kelola Pengguna</h2>
        <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 14px;">Manajemen pengguna dan riwayat pesanan</p>
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

<!-- Filter & Search Bar -->
<div class="filter-bar" style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <form action="{{ route('admin.users.index') }}" method="GET" style="display: grid; grid-template-columns: 1fr auto auto auto; gap: 10px;">
        <div style="position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #95a5a6;"></i>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Cari nama atau email pengguna..." 
                   style="width: 100%; padding: 12px 15px 12px 45px; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 15px; transition: all 0.3s;">
        </div>
        
        <select name="role" style="padding: 12px 15px; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 15px; cursor: pointer; min-width: 150px;">
            <option value="">Semua Role</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
        </select>
        
        <button type="submit" class="btn btn-primary" style="background: #3498db; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        @if(request('search') || request('role'))
        <a href="{{ route('admin.users.index') }}" class="btn" style="background: #95a5a6; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center;">
            <i class="fas fa-times"></i> Reset
        </a>
        @endif
    </form>
</div>

<!-- Users Table -->
<div class="data-table">
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Nama & Email</th>
                <th>Kontak</th>
                <th style="text-align: center;">Role</th>
                <th style="text-align: center;">Total Pesanan</th>
                <th style="text-align: center; width: 200px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr id="user-{{ $user->id_user }}">
                <td><strong>#{{ $user->id_user }}</strong></td>
                <td>
                    <div style="font-weight: 600; color: #2c3e50; margin-bottom: 4px;">{{ $user->nama }}</div>
                    <div style="font-size: 13px; color: #7f8c8d;">
                        <i class="fas fa-envelope"></i> {{ $user->email }}
                    </div>
                </td>
                <td>
                    <div style="font-size: 14px; color: #2c3e50; margin-bottom: 4px;">
                        <i class="fas fa-phone"></i> {{ $user->no_hp ?: '-' }}
                    </div>
                    <div style="font-size: 13px; color: #7f8c8d;">
                        <i class="fas fa-map-marker-alt"></i> {{ $user->alamat ? Str::limit($user->alamat, 30) : '-' }}
                    </div>
                </td>
                <td style="text-align: center;">
                    <span style="background: {{ $user->role == 'admin' ? '#e74c3c' : '#3498db' }}; color: white; padding: 5px 15px; border-radius: 20px; font-weight: 600; font-size: 13px; text-transform: uppercase;">
                        {{ $user->role }}
                    </span>
                </td>
                <td style="text-align: center;">
                    <span style="background: #ecf0f1; color: #2c3e50; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 14px;">
                        {{ $user->pesanan_count }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 6px; justify-content: center;">
                        <button onclick="showUserDetailModal({{ $user->id_user }})" 
                                class="btn btn-sm"
                                style="background: #3498db; color: white; padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        
                        @if($user->id_user != Auth::id())
                        <button onclick="showRoleModal({{ $user->id_user }}, '{{ $user->nama }}', '{{ $user->role }}')" 
                                class="btn btn-sm"
                                style="background: #f39c12; color: white; padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;">
                            <i class="fas fa-user-shield"></i> Role
                        </button>
                        @else
                        <button disabled
                                class="btn btn-sm"
                                style="background: #95a5a6; color: white; padding: 6px 12px; border: none; border-radius: 6px; cursor: not-allowed; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600; opacity: 0.5;">
                            <i class="fas fa-user-shield"></i> You
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            
            <!-- Hidden data for modal -->
            <script type="application/json" id="user-data-{{ $user->id_user }}">
                {
                    "id": {{ $user->id_user }},
                    "nama": "{{ $user->nama }}",
                    "email": "{{ $user->email }}",
                    "no_hp": "{{ $user->no_hp ?: '-' }}",
                    "alamat": "{{ $user->alamat ?: '-' }}",
                    "role": "{{ $user->role }}",
                    "pesanan_count": {{ $user->pesanan_count }},
                    "pesanan": [
                        @foreach($user->pesanan()->latest('tanggal_pesanan')->take(5)->get() as $pesanan)
                        {
                            "id": {{ $pesanan->id_pesanan }},
                            "tanggal": "{{ $pesanan->tanggal_pesanan->format('d M Y, H:i') }}",
                            "total": "{{ number_format($pesanan->total_harga, 0, ',', '.') }}",
                            "status": "{{ $pesanan->status }}",
                            "url": "{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}"
                        }{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ]
                }
            </script>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 60px; color: #999;">
                    <i class="fas fa-users" style="font-size: 64px; margin-bottom: 15px; opacity: 0.3; display: block;"></i>
                    <p style="font-size: 16px; margin: 0;">
                        @if(request('search') || request('role'))
                            Pengguna tidak ditemukan
                        @else
                            Belum ada pengguna terdaftar
                        @endif
                    </p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($users->hasPages())
<div class="pagination-wrapper" style="margin-top: 25px; display: flex; justify-content: center;">
    <div class="pagination">
        @if ($users->onFirstPage())
            <span class="disabled">&laquo;</span>
        @else
            <a href="{{ $users->appends(request()->query())->previousPageUrl() }}" rel="prev">&laquo;</a>
        @endif

        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
            @if ($page == $users->currentPage())
                <span class="active">{{ $page }}</span>
            @else
                <a href="{{ $users->appends(request()->query())->url($page) }}">{{ $page }}</a>
            @endif
        @endforeach

        @if ($users->hasMorePages())
            <a href="{{ $users->appends(request()->query())->nextPageUrl() }}" rel="next">&raquo;</a>
        @else
            <span class="disabled">&raquo;</span>
        @endif
    </div>
</div>
@endif

<!-- Role Change Modal -->
<div id="roleModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%;">
        <h3 style="margin: 0 0 20px 0; color: #2c3e50;">
            <i class="fas fa-user-shield"></i> Ubah Role Pengguna
        </h3>
        
        <form id="roleForm" method="POST">
            @csrf
            <p style="color: #7f8c8d; margin-bottom: 20px;">
                Ubah role untuk: <strong id="userName" style="color: #2c3e50;"></strong>
            </p>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 10px; color: #2c3e50; font-weight: 600;">
                    Pilih Role Baru:
                </label>
                <div style="display: flex; gap: 15px;">
                    <label style="flex: 1; cursor: pointer;">
                        <input type="radio" name="role" value="user" required style="margin-right: 8px;">
                        <span style="color: #2c3e50;">User</span>
                    </label>
                    <label style="flex: 1; cursor: pointer;">
                        <input type="radio" name="role" value="admin" required style="margin-right: 8px;">
                        <span style="color: #2c3e50;">Admin</span>
                    </label>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" 
                        style="flex: 1; background: #27ae60; color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-check"></i> Simpan
                </button>
                <button type="button" 
                        onclick="closeRoleModal()"
                        style="flex: 1; background: #95a5a6; color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </form>
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

.filter-bar input:focus,
.filter-bar select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.pagination {
    display: flex;
    gap: 5px;
}

.pagination a,
.pagination span {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    color: #2c3e50;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.pagination a:hover {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.pagination span.active {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.pagination span.disabled {
    background: #ecf0f1;
    color: #bdc3c7;
    cursor: not-allowed;
}

@media (max-width: 992px) {
    .filter-bar form {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
function showUserDetailModal(userId) {
    const userData = JSON.parse(document.getElementById('user-data-' + userId).textContent);
    
    // Create modal
    const modal = document.createElement('div');
    modal.id = 'user-detail-modal';
    modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;';
    
    const modalContent = document.createElement('div');
    modalContent.style.cssText = 'background: white; border-radius: 12px; width: 90%; max-width: 900px; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);';
    
    // Status badge colors
    const statusColors = {
        'selesai': '#27ae60',
        'menunggu': '#f39c12',
        'diproses': '#3498db',
        'dibatalkan': '#e74c3c'
    };
    
    // Build order history HTML
    let ordersHTML = '';
    if (userData.pesanan.length > 0) {
        userData.pesanan.forEach(pesanan => {
            ordersHTML += `
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid ${statusColors[pesanan.status] || '#95a5a6'};">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                        <div>
                            <strong style="color: #2c3e50; font-size: 14px;">Pesanan #${pesanan.id}</strong>
                            <div style="font-size: 12px; color: #7f8c8d; margin-top: 2px;">
                                <i class="fas fa-calendar"></i> ${pesanan.tanggal}
                            </div>
                        </div>
                        <span style="background: ${statusColors[pesanan.status] || '#95a5a6'}; color: white; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase;">
                            ${pesanan.status}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <strong style="color: #27ae60; font-size: 15px;">Rp ${pesanan.total}</strong>
                        <a href="${pesanan.url}" style="color: #3498db; font-size: 12px; text-decoration: none; font-weight: 600;">
                            Lihat Detail <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            `;
        });
        if (userData.pesanan_count > 5) {
            ordersHTML += `<p style="text-align: center; color: #7f8c8d; font-size: 13px; margin-top: 10px;">Menampilkan 5 dari ${userData.pesanan_count} pesanan</p>`;
        }
    } else {
        ordersHTML = `
            <div style="text-align: center; padding: 30px; color: #999;">
                <i class="fas fa-shopping-cart" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px; display: block;"></i>
                <p style="margin: 0;">Belum ada pesanan</p>
            </div>
        `;
    }
    
    modalContent.innerHTML = `
        <div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; padding: 20px 30px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; font-size: 20px; font-weight: 600;">
                <i class="fas fa-user-circle"></i> Detail Pengguna
            </h2>
            <button onclick="document.getElementById('user-detail-modal').remove()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding: 30px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <!-- User Info -->
                <div>
                    <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 16px; border-bottom: 2px solid #3498db; padding-bottom: 8px;">
                        <i class="fas fa-user"></i> Informasi Pengguna
                    </h3>
                    <table style="width: 100%; font-size: 14px;">
                        <tr>
                            <td style="padding: 8px 0; color: #7f8c8d; width: 120px;"><strong>ID User:</strong></td>
                            <td style="padding: 8px 0; color: #2c3e50;">#${userData.id}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #7f8c8d;"><strong>Nama:</strong></td>
                            <td style="padding: 8px 0; color: #2c3e50;">${userData.nama}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #7f8c8d;"><strong>Email:</strong></td>
                            <td style="padding: 8px 0; color: #2c3e50;">${userData.email}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #7f8c8d;"><strong>No. HP:</strong></td>
                            <td style="padding: 8px 0; color: #2c3e50;">${userData.no_hp}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #7f8c8d; vertical-align: top;"><strong>Alamat:</strong></td>
                            <td style="padding: 8px 0; color: #2c3e50;">${userData.alamat}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #7f8c8d;"><strong>Role:</strong></td>
                            <td style="padding: 8px 0;">
                                <span style="background: ${userData.role === 'admin' ? '#e74c3c' : '#3498db'}; color: white; padding: 4px 12px; border-radius: 15px; font-weight: 600; font-size: 12px; text-transform: uppercase;">
                                    ${userData.role}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Order History -->
                <div>
                    <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 16px; border-bottom: 2px solid #27ae60; padding-bottom: 8px;">
                        <i class="fas fa-shopping-cart"></i> Riwayat Pesanan (${userData.pesanan_count})
                    </h3>
                    <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                        ${ordersHTML}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    // Close on overlay click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function showRoleModal(userId, userName, currentRole) {
    const modal = document.getElementById('roleModal');
    const form = document.getElementById('roleForm');
    const userNameSpan = document.getElementById('userName');
    
    form.action = '/admin/users/' + userId + '/update-role';
    userNameSpan.textContent = userName;
    
    // Set current role as checked
    const radios = form.querySelectorAll('input[name="role"]');
    radios.forEach(radio => {
        if (radio.value === currentRole) {
            radio.checked = true;
        }
    });
    
    modal.style.display = 'flex';
}

function closeRoleModal() {
    const modal = document.getElementById('roleModal');
    modal.style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('roleModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRoleModal();
    }
});
</script>
@endpush
@endsection
