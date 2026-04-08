@extends('user.layout')

@section('title', 'Pesan Masuk')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <div class="page-header">
        <h1 class="page-title">Pesan Masuk</h1>
        <p class="page-subtitle">Balasan dari admin untuk pertanyaan Anda</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="inbox-container">
        <div class="inbox-content" style="border-radius: var(--radius-lg); border: 1px solid rgba(0,0,0,0.06);">
            @if($messages->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 16px; padding: 24px;">
                    @foreach($messages as $message)
                    <div class="inbox-card">
                        <!-- Header -->
                        <div class="inbox-card-header">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                <div class="message-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <div style="font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $message->subjek }}</div>
                                    <div style="font-size: 12px; color: var(--text-light); display: flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-clock"></i> Dibalas {{ $message->tanggal_balas->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('user.inbox.delete', $message->id_pesan) }}" method="POST" onsubmit="return confirm('Hapus pesan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inbox-delete-btn" title="Hapus pesan">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Your Message -->
                        <div class="inbox-card-section">
                            <div style="font-size: 11px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Pesan Anda</div>
                            <div style="font-size: 14px; color: var(--text-medium); line-height: 1.7; white-space: pre-wrap;">{{ $message->isi_pesan }}</div>
                            <div style="font-size: 12px; color: var(--text-light); margin-top: 10px; display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-paper-plane"></i> Dikirim: {{ $message->tanggal->format('d M Y, H:i') }}
                            </div>
                        </div>

                        <!-- Admin Reply -->
                        <div class="inbox-card-reply">
                            <div style="display: inline-flex; align-items: center; gap: 4px; background: var(--success); color: #fff; padding: 3px 10px; border-radius: 50px; font-size: 11px; font-weight: 700; margin-bottom: 10px;">
                                <i class="fas fa-shield-alt"></i> Balasan Admin
                            </div>
                            <div style="font-size: 14px; color: var(--text-dark); line-height: 1.7; white-space: pre-wrap;">{{ $message->balasan_admin }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($messages->hasPages())
                <div class="pagination-wrapper">
                    <div class="custom-pagination">
                        @if($messages->onFirstPage())
                            <span class="pagination-btn disabled">
                                <i class="fas fa-chevron-left"></i> Previous
                            </span>
                        @else
                            <a href="{{ $messages->previousPageUrl() }}" class="pagination-btn">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        @endif
                        <span class="pagination-info">
                            Halaman {{ $messages->currentPage() }} dari {{ $messages->lastPage() }}
                        </span>
                        @if($messages->hasMorePages())
                            <a href="{{ $messages->nextPageUrl() }}" class="pagination-btn">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <span class="pagination-btn disabled">
                                Next <i class="fas fa-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                </div>
                @endif
            @else
                <div class="inbox-empty">
                    <i class="fas fa-inbox"></i>
                    <h3>Belum Ada Pesan</h3>
                    <p>Anda belum memiliki pesan balasan dari admin</p>
                    <a href="{{ route('user.contact') }}" class="btn btn-green" style="padding: 10px 24px; font-size: 14px; margin-top: 16px;">
                        Kirim Pesan
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
