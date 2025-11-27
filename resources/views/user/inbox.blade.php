@extends('user.layout')

@section('title', 'Pesan Masuk')

@push('styles')
<style>
.inbox-container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0 20px;
}

.inbox-header {
    background: linear-gradient(135deg, var(--user-primary), var(--user-accent));
    color: white;
    padding: 30px;
    border-radius: 15px 15px 0 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.inbox-header h1 {
    margin: 0;
    font-size: 28px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.inbox-header h1 i {
    font-size: 32px;
}

.inbox-header p {
    margin: 8px 0 0 44px;
    opacity: 0.9;
    font-size: 15px;
}

.inbox-content {
    background: white;
    border-radius: 0 0 15px 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.inbox-empty {
    padding: 60px 30px;
    text-align: center;
    color: #999;
}

.inbox-empty i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.3;
}

.inbox-empty h3 {
    color: #666;
    margin-bottom: 10px;
}

.inbox-empty p {
    color: #999;
    font-size: 15px;
}

.message-item {
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.message-item:last-child {
    border-bottom: none;
}

.message-item:hover {
    background: #f8f9fa;
}

.message-header {
    padding: 20px 30px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.message-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--user-primary), var(--user-accent));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 22px;
    flex-shrink: 0;
}

.message-info {
    flex: 1;
}

.message-subject {
    font-size: 16px;
    font-weight: 600;
    color: var(--user-primary);
    margin-bottom: 6px;
}

.message-date {
    font-size: 13px;
    color: #999;
    display: flex;
    align-items: center;
    gap: 6px;
}

.message-toggle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.message-item:hover .message-toggle {
    background: var(--user-accent);
    color: white;
}

.message-body {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.message-body.active {
    max-height: 1000px;
}

.message-content {
    padding: 0 30px 30px 30px;
    margin-left: 70px;
}

.message-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.message-section:last-child {
    margin-bottom: 0;
}

.message-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #666;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.message-text {
    color: #333;
    line-height: 1.6;
    font-size: 15px;
    white-space: pre-wrap;
}

.admin-reply {
    background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
    border-left: 4px solid var(--user-accent);
}

.admin-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--user-accent);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 10px;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    padding: 30px 20px;
    background: white;
    border-radius: 0 0 15px 15px;
}

.pagination a,
.pagination span {
    padding: 8px 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    color: #666;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 14px;
}

.pagination a:hover {
    background: var(--user-primary);
    color: white;
    border-color: var(--user-primary);
}

.pagination .active span {
    background: var(--user-primary);
    color: white;
    border-color: var(--user-primary);
}

@media (max-width: 768px) {
    .inbox-header h1 {
        font-size: 22px;
    }
    
    .message-header {
        padding: 15px 20px;
    }
    
    .message-content {
        margin-left: 0;
        padding: 0 20px 20px 20px;
    }
    
    .message-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
}
</style>
@endpush

@section('content')
<div class="inbox-container">
    <div class="inbox-header">
        <h1>
            <i class="fas fa-inbox"></i>
            Pesan Masuk
        </h1>
        <p>Balasan dari admin untuk pertanyaan Anda</p>
    </div>
    
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
            <div class="pagination">
                @if($messages->onFirstPage())
                    <span>&laquo;</span>
                @else
                    <a href="{{ $messages->previousPageUrl() }}">&laquo;</a>
                @endif
                
                @foreach($messages->getUrlRange(1, $messages->lastPage()) as $page => $url)
                    @if($page == $messages->currentPage())
                        <span class="active"><span>{{ $page }}</span></span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($messages->hasMorePages())
                    <a href="{{ $messages->nextPageUrl() }}">&raquo;</a>
                @else
                    <span>&raquo;</span>
                @endif
            </div>
            @endif
        @else
            <div class="inbox-empty">
                <i class="fas fa-inbox"></i>
                <h3>Belum Ada Pesan</h3>
                <p>Anda belum memiliki pesan balasan dari admin</p>
                <a href="{{ route('user.contact') }}" class="btn btn-primary" style="margin-top: 15px; display: inline-block; text-decoration: none; padding: 8px 16px; font-size: 13px;">
                    Kirim Pesan
                </a>
            </div>
        @endif
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
