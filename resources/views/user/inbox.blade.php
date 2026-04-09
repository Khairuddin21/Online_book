@extends('user.layout')

@section('title', 'Chat')

@section('content')
<div class="user-chat-page">
    <div class="user-chat-wrapper">
        {{-- Chat Header --}}
        <div class="user-chat-header">
            <div class="user-chat-header-center">
                <div class="user-chat-admin-avatar">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <div class="user-chat-admin-name">Admin 6BUCKS.litera</div>
                    <div class="user-chat-admin-status"><i class="fas fa-circle"></i> Customer Support</div>
                </div>
            </div>
        </div>

        {{-- 24h Timer Banner --}}
        @if($chatExpiresAt)
        <div class="user-chat-timer-banner" id="chatTimerBanner">
            <i class="fas fa-clock"></i>
            <span>Riwayat chat tersisa <strong id="chatCountdown"></strong></span>
        </div>
        @endif

        {{-- Chat Messages --}}
        <div class="user-chat-messages" id="chatMessages">
            @if($messages->count() > 0)
                @php $lastDate = null; @endphp
                @foreach($messages as $msg)
                    @php $msgDate = $msg->waktu->format('d M Y'); @endphp
                    @if($msgDate !== $lastDate)
                        <div class="user-chat-date-divider">
                            <span>{{ $msg->waktu->isToday() ? 'Hari Ini' : ($msg->waktu->isYesterday() ? 'Kemarin' : $msgDate) }}</span>
                        </div>
                        @php $lastDate = $msgDate; @endphp
                    @endif
                    <div class="user-chat-bubble {{ $msg->pengirim == 'user' ? 'sent' : 'received' }}" data-id="{{ $msg->id_chat }}">
                        @if($msg->pengirim == 'admin')
                            <div class="user-chat-bubble-sender">Admin</div>
                        @endif
                        <div class="user-chat-bubble-text">{!! nl2br(e($msg->pesan)) !!}</div>
                        <div class="user-chat-bubble-time">
                            {{ $msg->waktu->format('H:i') }}
                            @if($msg->pengirim == 'user')
                                <i class="fas fa-check-double {{ $msg->dibaca ? 'read' : '' }}"></i>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="user-chat-welcome">
                    <div class="user-chat-welcome-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Selamat Datang di Chat</h3>
                    <p>Kirim pesan kepada admin untuk mendapatkan bantuan terkait pesanan, produk, atau pertanyaan lainnya.</p>
                </div>
            @endif
        </div>

        {{-- Chat Input --}}
        <div class="user-chat-input-area">
            <form action="{{ route('user.inbox.send') }}" method="POST" id="chatForm" class="user-chat-input-form">
                @csrf
                <textarea name="pesan" id="chatInput" rows="1" placeholder="Ketik pesan Anda..." required maxlength="5000"></textarea>
                <button type="submit" class="user-chat-send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/chat.css') }}?v={{ time() }}">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    const chatInput = document.getElementById('chatInput');
    if (chatInput) {
        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
        chatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim()) {
                    document.getElementById('chatForm').submit();
                }
            }
        });
    }

    // 24h Countdown Timer
    @if($chatExpiresAt)
    var expiresAt = new Date('{{ $chatExpiresAt->toIso8601String() }}');
    var countdownEl = document.getElementById('chatCountdown');
    var bannerEl = document.getElementById('chatTimerBanner');

    function updateCountdown() {
        var now = new Date();
        var diff = expiresAt - now;
        if (diff <= 0) {
            // Chat expired — reload to clear
            window.location.reload();
            return;
        }
        var hours = Math.floor(diff / 3600000);
        var minutes = Math.floor((diff % 3600000) / 60000);
        var seconds = Math.floor((diff % 60000) / 1000);
        if (countdownEl) {
            countdownEl.textContent = hours + ' jam ' + minutes + ' menit ' + seconds + ' detik';
        }
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);
    @endif

    // Polling for new messages
    let lastId = {{ $messages->count() > 0 ? $messages->last()->id_chat : 0 }};

    setInterval(function() {
        fetch(`{{ route('user.inbox.newMessages') }}?last_id=${lastId}`)
            .then(r => r.json())
            .then(data => {
                // If expired, reload page
                if (data.expired) {
                    window.location.reload();
                    return;
                }

                if (data.messages && data.messages.length > 0) {
                    // Remove welcome message if present
                    const welcome = chatMessages.querySelector('.user-chat-welcome');
                    if (welcome) welcome.remove();

                    data.messages.forEach(function(msg) {
                        const bubble = document.createElement('div');
                        bubble.className = 'user-chat-bubble ' + (msg.pengirim === 'user' ? 'sent' : 'received');
                        bubble.dataset.id = msg.id_chat;

                        let html = '';
                        if (msg.pengirim === 'admin') {
                            html += '<div class="user-chat-bubble-sender">Admin</div>';
                        }
                        html += '<div class="user-chat-bubble-text">' + msg.pesan.replace(/\n/g, '<br>') + '</div>';
                        html += '<div class="user-chat-bubble-time">' + msg.waktu;
                        if (msg.pengirim === 'user') {
                            html += ' <i class="fas fa-check-double"></i>';
                        }
                        html += '</div>';

                        bubble.innerHTML = html;
                        chatMessages.appendChild(bubble);
                        lastId = msg.id_chat;
                    });
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            })
            .catch(function() {});
    }, 3000);
});
</script>
@endpush
