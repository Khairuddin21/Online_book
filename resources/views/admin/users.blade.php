@extends('admin.layout')

@section('title', 'Kelola Pengguna')
@section('page-title', 'Pengguna')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: var(--text-dark);">Daftar Pengguna</h2>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">{{ $users->total() }} pengguna terdaftar</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form action="{{ route('admin.users.index') }}" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." style="flex: 1; min-width: 200px;">
        <select name="role" style="min-width: 150px;" onchange="this.form.submit()">
            <option value="">Semua Role</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
        @if(request('search') || request('role'))
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm"><i class="fas fa-times"></i> Reset</a>
        @endif
    </form>
</div>

<!-- Users Table -->
<div class="data-table">
    @if($users->count() > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Pengguna</th>
                <th>Kontak</th>
                <th>Role</th>
                <th style="text-align: center;">Pesanan</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td><span style="font-weight: 600; color: var(--text-muted);">#{{ $user->id_user }}</span></td>
                <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="avatar" style="width: 38px; height: 38px; font-size: 14px;">{{ strtoupper(substr($user->nama, 0, 1)) }}</div>
                        <div>
                            <div style="font-weight: 600; color: var(--text-dark);">{{ $user->nama }}</div>
                            <div style="font-size: 12px; color: var(--text-muted);">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-size: 13px; color: var(--text-dark);">{{ $user->no_hp ?? '-' }}</div>
                    <div style="font-size: 12px; color: var(--text-muted);">{{ Str::limit($user->alamat ?? '-', 30) }}</div>
                </td>
                <td>
                    @if($user->role == 'admin')
                        <span class="badge badge-red"><i class="fas fa-shield-alt"></i> Admin</span>
                    @else
                        <span class="badge badge-blue"><i class="fas fa-user"></i> User</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <span class="badge badge-gray">{{ $user->pesanan_count }}</span>
                </td>
                <td style="text-align: center;">
                    <div style="display: flex; justify-content: center; gap: 6px;">
                        <button type="button" class="btn btn-primary btn-sm" onclick="showUserDetailModal({{ $user->id_user }})">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        @if($user->id_user != Auth::id())
                        <button type="button" class="btn btn-warning btn-sm" onclick="showRoleModal({{ $user->id_user }}, '{{ $user->nama }}', '{{ $user->role }}')">
                            <i class="fas fa-user-cog"></i> Role
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            <!-- User data for detail modal -->
            <script type="application/json" id="user-data-{{ $user->id_user }}">
                {!! json_encode([
                    'id_user' => $user->id_user,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'no_hp' => $user->no_hp,
                    'alamat' => $user->alamat,
                    'role' => $user->role,
                    'pesanan_count' => $user->pesanan_count
                ]) !!}
            </script>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination">
            {{ $users->appends(request()->query())->links('pagination::simple-bootstrap-4') }}
        </div>
    </div>
    @endif

    @else
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <p>Tidak ada pengguna ditemukan.</p>
    </div>
    @endif
</div>

<!-- Role Change Modal -->
<div class="modal-overlay" id="roleModal">
    <div class="modal-content">
        <h3><i class="fas fa-user-cog" style="color: var(--green-dark);"></i> Ubah Role Pengguna</h3>
        <form id="roleForm" method="POST">
            @csrf
            <p style="color: var(--text-muted); margin-bottom: 20px; font-size: 14px;">
                Mengubah role untuk: <strong id="roleUserName" style="color: var(--text-dark);"></strong>
            </p>
            <div class="form-group">
                <label>Role Baru</label>
                <select name="role" id="roleSelect" class="form-input">
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <div class="form-actions" style="border-top: none; padding-top: 12px;">
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeRoleModal()"><i class="fas fa-times"></i> Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- User Detail Modal -->
<div class="modal-overlay" id="userDetailModal">
    <div class="modal-content" style="max-width: 520px;">
        <h3><i class="fas fa-user" style="color: var(--green-dark);"></i> Detail Pengguna</h3>
        <div style="text-align: center; margin-bottom: 20px;">
            <div class="avatar avatar-lg" id="detailAvatar" style="margin: 0 auto 12px;"></div>
            <div style="font-size: 18px; font-weight: 700; color: var(--text-dark);" id="detailNama"></div>
            <div style="font-size: 14px; color: var(--text-muted);" id="detailEmail"></div>
        </div>
        <div class="detail-grid" id="detailGrid" style="background: var(--green-bg); padding: 18px; border-radius: var(--radius-sm); margin-bottom: 20px;">
            <div class="label">ID User</div><div class="value" id="detailId"></div>
            <div class="label">No. HP</div><div class="value" id="detailHp"></div>
            <div class="label">Alamat</div><div class="value" id="detailAlamat"></div>
            <div class="label">Role</div><div class="value" id="detailRole"></div>
            <div class="label">Total Pesanan</div><div class="value" id="detailPesanan"></div>
        </div>
        <div style="text-align: center;">
            <button type="button" class="btn btn-secondary" onclick="closeUserDetailModal()"><i class="fas fa-times"></i> Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showRoleModal(userId, userName, currentRole) {
    document.getElementById('roleForm').action = '{{ url("admin/users") }}/' + userId + '/update-role';
    document.getElementById('roleUserName').textContent = userName;
    document.getElementById('roleSelect').value = currentRole;
    document.getElementById('roleModal').classList.add('active');
}

function closeRoleModal() {
    document.getElementById('roleModal').classList.remove('active');
}

function showUserDetailModal(userId) {
    var dataEl = document.getElementById('user-data-' + userId);
    if (!dataEl) return;
    var user = JSON.parse(dataEl.textContent);

    document.getElementById('detailAvatar').textContent = user.nama.charAt(0).toUpperCase();
    document.getElementById('detailNama').textContent = user.nama;
    document.getElementById('detailEmail').textContent = user.email;
    document.getElementById('detailId').textContent = '#' + user.id_user;
    document.getElementById('detailHp').textContent = user.no_hp || '-';
    document.getElementById('detailAlamat').textContent = user.alamat || '-';
    document.getElementById('detailPesanan').textContent = user.pesanan_count + ' pesanan';

    var roleBadge = user.role === 'admin'
        ? '<span class="badge badge-red"><i class="fas fa-shield-alt"></i> Admin</span>'
        : '<span class="badge badge-blue"><i class="fas fa-user"></i> User</span>';
    document.getElementById('detailRole').innerHTML = roleBadge;

    document.getElementById('userDetailModal').classList.add('active');
}

function closeUserDetailModal() {
    document.getElementById('userDetailModal').classList.remove('active');
}

// Close modals on overlay click
document.getElementById('roleModal').addEventListener('click', function(e) {
    if (e.target === this) closeRoleModal();
});
document.getElementById('userDetailModal').addEventListener('click', function(e) {
    if (e.target === this) closeUserDetailModal();
});
</script>
@endpush
