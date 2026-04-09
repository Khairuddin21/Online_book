@extends('admin.layout')

@section('title', 'Chat')
@section('page-title', 'Chat')

@section('content')
<div class="chat-wrapper">
    {{-- Sidebar: Contact List --}}
    <div class="chat-sidebar" id="chatSidebar">
        <div class="chat-sidebar-header">
            <h3><i class="fas fa-comments"></i> Chat</h3>
            <div class="chat-search">
                <i class="fas fa-search"></i>
                <input type="text" id="searchContact" placeholder="Cari pengguna...">
            </div>
        </div>
        <div class="chat-contact-list" id="contactList">
            @forelse($users as $user)
                <a href="{{ route('admin.pesan.index', ['user_id' => $user->id_user]) }}"
                   class="chat-contact {{ $selectedUser && $selectedUser->id_user == $user->id_user ? 'active' : '' }}">
                    <div class="chat-contact-avatar">
                        {{ strtoupper(substr($user->nama, 0, 1)) }}
                        @if($user->unread_count > 0)
                            <span class="unread-dot"></span>
                        @endif
                    </div>
                    <div class="chat-contact-info">
                        <div class="chat-contact-name">
                            {{ $user->nama }}
                            @if($user->unread_count > 0)
                                <span class="unread-badge">{{ $user->unread_count }}</span>
                            @endif
                        </div>
                        <div class="chat-contact-preview">
                            @if($user->chatMessages->first())
                                @php $last = $user->chatMessages->first(); @endphp
                                <span class="preview-sender">{{ $last->pengirim == 'admin' ? 'Anda: ' : '' }}</span>{{ Str::limit($last->pesan, 35) }}
                            @else
                                <em>Belum ada pesan</em>
                            @endif
                        </div>
                    </div>
                    <div class="chat-contact-time">
                        @if($user->chatMessages->first())
                            {{ $user->chatMessages->first()->waktu->format('H:i') }}
                        @endif
                    </div>
                </a>
            @empty
                <div class="chat-empty-contacts">
                    <i class="fas fa-users"></i>
                    <p>Belum ada percakapan</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Main: Chat Area --}}
    <div class="chat-main">
        @if($selectedUser)
            {{-- Chat Header --}}
            <div class="chat-header">
                <button class="chat-back-btn" id="chatBackBtn"><i class="fas fa-arrow-left"></i></button>
                <div class="chat-header-avatar">{{ strtoupper(substr($selectedUser->nama, 0, 1)) }}</div>
                <div class="chat-header-info">
                    <div class="chat-header-name">{{ $selectedUser->nama }}</div>
                    <div class="chat-header-status">{{ $selectedUser->email }}</div>
                </div>
                <div class="chat-header-actions">
                    <form action="{{ route('admin.pesan.delete', $selectedUser->id_user) }}" method="POST"
                          onsubmit="return confirm('Hapus seluruh percakapan dengan {{ $selectedUser->nama }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="chat-action-btn" title="Hapus percakapan">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- 24h Timer Notice --}}
            @if($chatExpiresAt)
            <div class="chat-timer-notice" id="adminChatTimer">
                <i class="fas fa-clock"></i> Riwayat chat berakhir dalam <strong id="adminChatCountdown"></strong>
            </div>
            @endif

            {{-- Chat Messages --}}
            <div class="chat-messages" id="chatMessages">
                @php $lastDate = null; @endphp
                @foreach($messages as $msg)
                    @php $msgDate = $msg->waktu->format('d M Y'); @endphp
                    @if($msgDate !== $lastDate)
                        <div class="chat-date-divider">
                            <span>{{ $msg->waktu->isToday() ? 'Hari Ini' : ($msg->waktu->isYesterday() ? 'Kemarin' : $msgDate) }}</span>
                        </div>
                        @php $lastDate = $msgDate; @endphp
                    @endif
                    <div class="chat-bubble {{ $msg->pengirim == 'admin' ? 'sent' : 'received' }}" data-id="{{ $msg->id_chat }}">
                        <div class="chat-bubble-text">{!! nl2br(e($msg->pesan)) !!}</div>
                        <div class="chat-bubble-time">
                            {{ $msg->waktu->format('H:i') }}
                            @if($msg->pengirim == 'admin')
                                <i class="fas fa-check-double {{ $msg->dibaca ? 'read' : '' }}"></i>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Chat Input --}}
            <div class="chat-input-area">
                <form action="{{ route('admin.pesan.send') }}" method="POST" id="chatForm" class="chat-input-form">
                    @csrf
                    <input type="hidden" name="id_user" value="{{ $selectedUser->id_user }}">
                    <textarea name="pesan" id="chatInput" rows="1" placeholder="Ketik pesan..." required maxlength="5000"></textarea>
                    <button type="submit" class="chat-send-btn" id="chatSendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        @else
            {{-- Empty State --}}
            <div class="chat-empty-state">
                <div class="chat-empty-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Pilih Percakapan</h3>
                <p>Pilih pengguna dari daftar di samping untuk mulai membalas pesan</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/chat.css') }}?v={{ time() }}">
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

    @if($selectedUser)
    const userId = {{ $selectedUser->id_user }};
    let lastId = {{ $messages->count() > 0 ? $messages->last()->id_chat : 0 }};

    // 24h Countdown
    @if($chatExpiresAt)
    var adminExpiresAt = new Date('{{ $chatExpiresAt->toIso8601String() }}');
    var adminCountdownEl = document.getElementById('adminChatCountdown');

    function updateAdminCountdown() {
        var now = new Date();
        var diff = adminExpiresAt - now;
        if (diff <= 0) {
            window.location.href = '{{ route("admin.pesan.index") }}';
            return;
        }
        var hours = Math.floor(diff / 3600000);
        var minutes = Math.floor((diff % 3600000) / 60000);
        var seconds = Math.floor((diff % 60000) / 1000);
        if (adminCountdownEl) {
            adminCountdownEl.textContent = hours + 'j ' + minutes + 'm ' + seconds + 'd';
        }
    }
    updateAdminCountdown();
    setInterval(updateAdminCountdown, 1000);
    @endif

    setInterval(function() {
        fetch(`{{ route('admin.pesan.newMessages') }}?user_id=${userId}&last_id=${lastId}`)
            .then(r => r.json())
            .then(data => {
                if (data.expired) {
                    window.location.href = '{{ route("admin.pesan.index") }}';
                    return;
                }

                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(function(msg) {
                        const bubble = document.createElement('div');
                        bubble.className = 'chat-bubble ' + (msg.pengirim === 'admin' ? 'sent' : 'received');
                        bubble.dataset.id = msg.id_chat;
                        bubble.innerHTML = '<div class="chat-bubble-text">' + msg.pesan.replace(/\n/g, '<br>') + '</div>'
                            + '<div class="chat-bubble-time">' + msg.waktu
                            + (msg.pengirim === 'admin' ? ' <i class="fas fa-check-double"></i>' : '') + '</div>';
                        chatMessages.appendChild(bubble);
                        lastId = msg.id_chat;
                    });
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            })
            .catch(function() {});
    }, 3000);
    @endif

    const searchInput = document.getElementById('searchContact');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.chat-contact').forEach(function(el) {
                const name = el.querySelector('.chat-contact-name').textContent.toLowerCase();
                el.style.display = name.includes(val) ? '' : 'none';
            });
        });
    }

    const backBtn = document.getElementById('chatBackBtn');
    if (backBtn) {
        backBtn.addEventListener('click', function() {
            document.getElementById('chatSidebar').classList.remove('hidden-mobile');
            document.querySelector('.chat-main').classList.add('hidden-mobile');
        });
    }

    @if($selectedUser)
    if (window.innerWidth <= 768) {
        document.getElementById('chatSidebar').classList.add('hidden-mobile');
        document.querySelector('.chat-main').classList.remove('hidden-mobile');
    }
    @endif
});
</script>
@endpush
