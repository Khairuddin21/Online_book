@extends('admin.layout')

@section('title', 'Pesan Kontak')
@section('page-title', 'Pesan Kontak')

@section('content')
<div class="content-header">
    <div>
        <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: var(--text-dark);">Inbox Pesan</h2>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">
            {{ $pesan->total() }} pesan
            @php $belumDibaca = $pesan->where('balasan_admin', null)->count(); @endphp
            @if($belumDibaca > 0)
                · <span style="color: var(--warning); font-weight: 600;">{{ $belumDibaca }} belum dibalas</span>
            @endif
        </p>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form action="{{ route('admin.pesan.index') }}" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari subjek, pengirim, isi pesan..." style="flex: 1; min-width: 200px;">
        <select name="status" style="min-width: 170px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="belum_dibaca" {{ request('status') == 'belum_dibaca' ? 'selected' : '' }}>Belum Dibalas</option>
            <option value="sudah_dibaca" {{ request('status') == 'sudah_dibaca' ? 'selected' : '' }}>Sudah Dibalas</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.pesan.index') }}" class="btn btn-ghost btn-sm"><i class="fas fa-times"></i> Reset</a>
        @endif
    </form>
</div>

<!-- Messages Table -->
<div class="data-table">
    @if($pesan->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 36px;"></th>
                <th>Pengirim</th>
                <th>Subjek & Pesan</th>
                <th>Tanggal</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesan as $p)
            <tr style="{{ !$p->balasan_admin ? 'background: rgba(245, 158, 11, 0.04);' : '' }}">
                <td style="text-align: center;">
                    @if(!$p->balasan_admin)
                        <i class="fas fa-envelope" style="color: var(--warning); font-size: 14px;" title="Belum dibalas"></i>
                    @else
                        <i class="fas fa-envelope-open" style="color: var(--text-light); font-size: 14px;" title="Sudah dibalas"></i>
                    @endif
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="avatar" style="width: 34px; height: 34px; font-size: 13px;">{{ strtoupper(substr($p->user->nama ?? 'U', 0, 1)) }}</div>
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: var(--text-dark);">{{ $p->user->nama ?? 'Unknown' }}</div>
                            <div style="font-size: 12px; color: var(--text-muted);">{{ $p->user->email ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-weight: {{ !$p->balasan_admin ? '700' : '500' }}; color: var(--text-dark); margin-bottom: 2px; font-size: 14px;">{{ Str::limit($p->subjek, 45) }}</div>
                    <div style="font-size: 12px; color: var(--text-muted);">{{ Str::limit($p->isi_pesan, 60) }}</div>
                </td>
                <td>
                    <span style="font-size: 13px; color: var(--text-muted);">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</span>
                    <div style="font-size: 11px; color: var(--text-light);">{{ \Carbon\Carbon::parse($p->tanggal)->format('H:i') }}</div>
                </td>
                <td style="text-align: center;">
                    <div style="display: flex; justify-content: center; gap: 6px;">
                        <a href="{{ route('admin.pesan.show', $p->id_pesan) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                        <form action="{{ route('admin.pesan.delete', $p->id_pesan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    @if($pesan->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination">
            {{ $pesan->appends(request()->query())->links('pagination::simple-bootstrap-4') }}
        </div>
    </div>
    @endif

    @else
    <div class="empty-state">
        <i class="fas fa-envelope"></i>
        <p>Belum ada pesan kontak masuk.</p>
    </div>
    @endif
</div>
@endsection
