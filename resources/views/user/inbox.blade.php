@extends('user.layout')

@section('title', 'Pesan Masuk')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <div class="page-header">
        <h1 class="page-title">Pesan Masuk</h1>
        <p class="page-subtitle">Balasan dari admin untuk pertanyaan Anda</p>
    </div>

    <div class="inbox-container">
        <div class="inbox-content">
            @if($messages->count() > 0)
                @foreach($messages as $message)
                <div class="message-item">
                    <div class="message-header" onclick="toggleMessage({{ $message->id_pesan }})">
                        <div class="message-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        
                        <div class="message-info">
                            <div class="message-subject">{{ $message->subjek }}</div>
                            <div class="message-date">
                                <i class="fas fa-clock"></i>
                                {{ $message->tanggal_balas->format('d M Y, H:i') }}
                            </div>
                        </div>
                        
                        <div class="message-toggle" id="toggle-{{ $message->id_pesan }}">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    
                    <div class="message-body" id="body-{{ $message->id_pesan }}">
                        <div class="message-content">
                            <div class="message-section">
                                <div class="message-section-title">Pesan Anda</div>
                                <div class="message-text">{{ $message->isi_pesan }}</div>
                                <div class="message-date" style="margin-top: 10px;">
                                    <i class="fas fa-paper-plane"></i>
                                    Dikirim: {{ $message->tanggal->format('d M Y, H:i') }}
                                </div>
                            </div>
                            
                            <div class="message-section admin-reply">
                                <div class="admin-badge">
                                    <i class="fas fa-shield-alt"></i>
                                    Balasan Admin
                                </div>
                                <div class="message-text">{{ $message->balasan_admin }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

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
                    <a href="{{ route('user.contact') }}" class="btn btn-outline-green btn-sm" style="margin-top: 16px;">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleMessage(id) {
    const body = document.getElementById('body-' + id);
    const toggle = document.getElementById('toggle-' + id);
    const icon = toggle.querySelector('i');
    
    body.classList.toggle('active');
    
    if (body.classList.contains('active')) {
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}
</script>
@endpush
@endsection
