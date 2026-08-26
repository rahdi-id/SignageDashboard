@extends('layouts.main')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Conversation Detail</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('helpdesk.dashboard') }}">Hotel Helpdesk</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('helpdesk.conversations.index') }}">Conversations</a></div>
            <div class="breadcrumb-item">Detail</div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="section-body">
        <div class="row">

            {{-- ── Kolom Kiri: Chat Info ───────────────────────────────── --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Chat Info</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" width="40%">Chat #</td>
                                <td><strong>#{{ $conversation->id }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Guest Name</td>
                                <td>{{ $conversation->guest_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Room Number</td>
                                <td>{{ $conversation->room_number }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Department</td>
                                <td>{{ $conversation->department->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td>
                                    @php $sc = \App\Models\Conversation::STATUS_COLORS[$conversation->status] ?? 'secondary'; @endphp
                                    <span class="badge badge-{{ $sc }}" id="admin-status-badge">
                                        {{ \App\Models\Conversation::STATUS_LABELS[$conversation->status] ?? $conversation->status }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Priority</td>
                                <td>
                                    @php $pc = \App\Models\Conversation::PRIORITY_COLORS[$conversation->priority] ?? 'secondary'; @endphp
                                    <span class="badge badge-{{ $pc }}">
                                        {{ \App\Models\Conversation::PRIORITY_LABELS[$conversation->priority] ?? $conversation->priority }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Created At</td>
                                <td>{{ $conversation->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        @if($conversation->status !== 'closed')
                        <form action="{{ route('helpdesk.conversations.close', $conversation->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-block mb-2"
                                onclick="return confirm('Close this chat?')">
                                <i class="fas fa-check-circle"></i> Close Chat
                            </button>
                        </form>
                        @else
                        <form action="{{ route('helpdesk.conversations.reopen', $conversation->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning btn-block mb-2"
                                onclick="return confirm('Reopen this chat?')">
                                <i class="fas fa-redo"></i> Reopen Chat
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('helpdesk.conversations.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            {{-- ── Kolom Kanan: Chat Thread ────────────────────────────── --}}
            <div class="col-md-8">
                {{--
                    .admin-chat-card menggunakan display:flex + flex-direction:column
                    dengan height tetap agar:
                    - .admin-chat-messages bisa flex:1 dan overflow-y:auto
                    - reply form selalu menempel di bawah
                    - tidak ada nested scroll dengan window
                --}}
                <div class="card admin-chat-card">
                    <div class="card-header flex-shrink-0 d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Messages</h4>
                        <div class="presence-indicator" id="guest-presence">
                            <div class="presence-dot offline"></div>
                            <span id="guest-presence-text" class="text-muted">—</span>
                        </div>
                    </div>

                    {{-- Area messages — satu-satunya scrollable area --}}
                    <div class="admin-chat-messages" id="messages-container">
                        @forelse($conversation->messages as $msg)
                            @php $isGuest = $msg->sender_type === 'guest'; @endphp
                            <div class="admin-msg-row {{ $isGuest ? 'guest' : 'admin' }}"
                                 data-msg-id="{{ $msg->id }}">
                                <div class="admin-msg-avatar">
                                    <i class="fas {{ $isGuest ? 'fa-user' : 'fa-user-tie' }}"></i>
                                </div>
                                <div class="admin-msg-bubble-wrap">
                                    <div class="admin-msg-sender">
                                        {{ $isGuest ? $conversation->guest_name : 'Admin' }}
                                    </div>
                                    <div class="admin-msg-bubble">{{ $msg->message }}</div>
                                    @if($msg->attachment)
                                    @php
                                        $ext = strtolower(pathinfo($msg->attachment, PATHINFO_EXTENSION));
                                        $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                                    @endphp
                                    <div class="admin-msg-attachment">
                                        @if($isImg)
                                        <a href="{{ asset('images/helpdesk/' . $msg->attachment) }}"
                                           target="_blank" class="chat-img-link">
                                            <img src="{{ asset('images/helpdesk/' . $msg->attachment) }}"
                                                 alt="attachment"
                                                 class="chat-img-preview">
                                        </a>
                                        @else
                                        <a href="{{ asset('images/helpdesk/' . $msg->attachment) }}"
                                           target="_blank"
                                           class="btn btn-sm {{ $isGuest ? 'btn-secondary' : 'btn-light' }}">
                                            <i class="fas fa-paperclip"></i> Attachment
                                        </a>
                                        @endif
                                    </div>
                                    @endif
                                    <div class="admin-msg-time">
                                        {{ $msg->created_at->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="admin-empty-state" id="empty-state">
                                <i class="fas fa-comments"></i>
                                No messages yet.
                            </div>
                        @endforelse
                    </div>

                    {{-- Reply form — selalu di bawah --}}
                    @if($conversation->status !== 'closed')
                    <div class="admin-chat-footer flex-shrink-0" id="admin-reply-area">
                        <form action="{{ route('helpdesk.conversations.reply', $conversation->id) }}"
                              method="POST" enctype="multipart/form-data" id="admin-reply-form">
                            @csrf
                            {{-- File input tersembunyi — dipicu oleh tombol paperclip --}}
                            <input type="file"
                                   name="attachment"
                                   id="admin-file-input"
                                   accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx"
                                   style="display:none"
                                   class="@error('attachment') is-invalid @enderror">
                            <div class="admin-reply-input-row">
                                {{-- Tombol paperclip — kiri textarea --}}
                                <button type="button" class="admin-btn-attach" id="admin-btn-attach" title="Attach file">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <textarea name="message"
                                    class="admin-reply-textarea @error('message') is-invalid @enderror"
                                    id="admin-reply-textarea"
                                    rows="1"
                                    placeholder="Type your reply here...">{{ old('message') }}</textarea>
                                <button type="submit" class="admin-send-btn" id="admin-send-btn" title="Send Reply">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            @error('message')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                            {{-- Preview nama file terpilih --}}
                            <div class="admin-file-preview" id="admin-file-preview">
                                <i class="fas fa-paperclip"></i>
                                <span id="admin-file-name"></span>
                                <span class="remove-file" id="admin-btn-remove-file" title="Remove">
                                    <i class="fas fa-times"></i>
                                </span>
                            </div>
                            @error('attachment')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </form>
                    </div>
                    @else
                    <div class="admin-chat-footer admin-chat-closed flex-shrink-0" id="admin-reply-area">
                        <i class="fas fa-lock"></i> This conversation is closed. Reopen to reply.
                    </div>
                    @endif

                </div>{{-- /.admin-chat-card --}}
            </div>

        </div>
    </div>
</section>
@endsection

@section('js')
<style>
    /*
     * Layout flex pada card agar messages container bisa flex:1
     * dan reply form selalu menempel di bawah tanpa nested scroll.
     * Height card ditetapkan agar scrollbar ada di container,
     * bukan di window.
     */
    .admin-chat-card {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 220px); /* sesuaikan jika navbar/header berbeda tinggi */
        min-height: 480px;
        overflow: hidden;
    }

    .admin-chat-card .card-header {
        flex-shrink: 0;
    }

    /* ── Messages scrollable area ── */
    .admin-chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        -webkit-overflow-scrolling: touch;
    }

    /* ── Message rows ── */
    .admin-msg-row {
        display: flex;
        align-items: flex-end;
        gap: 10px;
    }

    .admin-msg-row.guest { flex-direction: row; }
    .admin-msg-row.admin { flex-direction: row-reverse; }

    .admin-msg-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 14px;
    }

    .admin-msg-row.guest .admin-msg-avatar { background: #e0e0e0; color: #555; }
    .admin-msg-row.admin .admin-msg-avatar { background: #6777ef; color: #fff; }

    .admin-msg-bubble-wrap {
        max-width: 72%;
        display: flex;
        flex-direction: column;
    }

    .admin-msg-row.guest .admin-msg-bubble-wrap { align-items: flex-start; }
    .admin-msg-row.admin .admin-msg-bubble-wrap { align-items: flex-end; }

    .admin-msg-sender {
        font-size: 0.72rem;
        font-weight: 600;
        color: #636e72;
        margin-bottom: 3px;
        padding: 0 4px;
    }

    .admin-msg-bubble {
        padding: 10px 14px;
        border-radius: 16px;
        font-size: 0.9rem;
        line-height: 1.55;
        word-break: break-word;
        white-space: pre-wrap;
    }

    .admin-msg-row.guest .admin-msg-bubble {
        background: #f5f5f5;
        color: #2d3436;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.07);
    }

    .admin-msg-row.admin .admin-msg-bubble {
        background: #6777ef;
        color: #fff;
        border-bottom-right-radius: 4px;
        box-shadow: 0 1px 4px rgba(103,119,239,0.25);
    }

    .admin-msg-attachment {
        margin-top: 6px;
    }

    /* ── Attachment image preview ── */
    .chat-img-preview {
        display: block;
        max-width: 250px;
        width: 100%;
        height: auto;
        border-radius: 10px;
        cursor: pointer;
        transition: opacity 0.15s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }

    .chat-img-preview:hover { opacity: 0.88; }

    .chat-img-link { display: inline-block; }

    .admin-msg-time {
        font-size: 0.7rem;
        color: #b2bec3;
        margin-top: 4px;
        padding: 0 4px;
    }

    .admin-msg-row.admin .admin-msg-time { text-align: right; }

    /* ── Empty state ── */
    .admin-empty-state {
        text-align: center;
        color: #b2bec3;
        padding: 40px 20px;
        font-size: 0.9rem;
        margin: auto;
    }

    .admin-empty-state i {
        font-size: 38px;
        display: block;
        margin-bottom: 10px;
        opacity: 0.35;
    }

    /* ── Reply footer ── */
    .admin-chat-footer {
        border-top: 1px solid #e8eaff;
        background: #fff;
        padding: 12px 16px;
    }

    .admin-reply-input-row {
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }

    .admin-reply-textarea {
        flex: 1;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 0.92rem;
        font-family: inherit;
        resize: none;
        outline: none;
        min-height: 44px;
        max-height: 120px;
        line-height: 1.4;
        transition: border-color 0.2s;
        color: #2d3436;
    }

    .admin-reply-textarea:focus { border-color: #6777ef; }

    .admin-send-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: none;
        background: #6777ef;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        cursor: pointer;
        font-size: 15px;
        transition: background 0.2s, transform 0.1s;
    }

    .admin-send-btn:hover  { background: #5a6fd6; }
    .admin-send-btn:active { transform: scale(0.93); }
    .admin-send-btn:disabled { background: #b2bec3; cursor: not-allowed; }

    .admin-attachment-label {
        font-size: 0.8rem;
        color: #636e72;
        font-weight: 600;
        display: block;
    }

    /* ── Tombol attach admin (icon klip) — sama dengan guest ── */
    .admin-btn-attach {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 2px solid #e0e0e0;
        background: #fff;
        color: #b2bec3;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        cursor: pointer;
        font-size: 16px;
        transition: border-color 0.2s, color 0.2s;
        padding: 0;
    }

    .admin-btn-attach:hover    { border-color: #6777ef; color: #6777ef; }
    .admin-btn-attach.has-file { border-color: #6777ef; color: #6777ef; background: #f0f2ff; }

    /* ── Preview nama file yang dipilih ── */
    .admin-file-preview {
        display: none;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        padding: 5px 10px;
        background: #f0f2ff;
        border-radius: 8px;
        font-size: 0.78rem;
        color: #6777ef;
    }

    .admin-file-preview.visible { display: flex; }

    .admin-file-preview .remove-file {
        margin-left: auto;
        cursor: pointer;
        color: #b2bec3;
        font-size: 13px;
        line-height: 1;
    }

    .admin-file-preview .remove-file:hover { color: #e74c3c; }

    .admin-chat-closed {
        text-align: center;
        color: #636e72;
        font-size: 0.85rem;
        padding: 14px 16px;
    }

    .admin-chat-closed i {
        margin-right: 6px;
        color: #b2bec3;
    }

    /* ── Online / Last Seen indicator ── */
    .presence-indicator {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.78rem;
        font-weight: 500;
    }

    .presence-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .presence-dot.online  { background: #47c363; }
    .presence-dot.offline { background: #b2bec3; }
</style>

<script>
(function () {
    'use strict';

    console.log('[ADMIN CHAT] JS LOADED');

    // ── Config ──────────────────────────────────────────────────────────────
    var POLL_INTERVAL = 4000;
    var GUEST_NAME    = {{ Js::from($conversation->guest_name) }};
    var POLL_URL      = "{{ route('helpdesk.conversations.messages', $conversation->id) }}";
    var isClosed      = {{ $conversation->status === 'closed' ? 'true' : 'false' }};
    var lastMessageId = 0;

    var container = document.getElementById('messages-container');

    // ── Scroll helpers ───────────────────────────────────────────────────────
    function isNearBottom() {
        if (!container) return true;
        // Admin dianggap "di bawah" jika dalam 150px dari bottom container
        return container.scrollHeight - container.scrollTop - container.clientHeight < 150;
    }

    function scrollToBottom(smooth) {
        if (!container) return;
        console.log('[ADMIN CHAT] scrollToBottom smooth=' + smooth);
        if (smooth) {
            container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
        } else {
            container.scrollTop = container.scrollHeight;
        }
    }

    // ── Helper: apakah attachment adalah gambar ──────────────────────────────
    function isImageAttachment(filename) {
        if (!filename) return false;
        var ext = filename.split('.').pop().toLowerCase();
        return ['jpg', 'jpeg', 'png', 'gif', 'webp'].indexOf(ext) !== -1;
    }

    // ── HTML builder — struktur sama dengan guest chat ───────────────────────
    function buildMessageHTML(msg) {
        var isGuest = msg.sender_type === 'guest';
        var rowClass = isGuest ? 'guest' : 'admin';
        var icon     = isGuest ? 'fa-user' : 'fa-user-tie';
        var sender   = isGuest ? escapeHtml(GUEST_NAME) : 'Admin';
        var text     = escapeHtml(msg.message);
        var time     = escapeHtml(msg.created_at);

        var attachmentHTML = '';
        if (msg.attachment) {
            var fileUrl = '/images/helpdesk/' + encodeURIComponent(msg.attachment);
            if (isImageAttachment(msg.attachment)) {
                attachmentHTML = '<div class="admin-msg-attachment">'
                    + '<a href="' + fileUrl + '" target="_blank" class="chat-img-link">'
                    + '<img src="' + fileUrl + '" alt="attachment" class="chat-img-preview">'
                    + '</a></div>';
            } else {
                var btnClass = isGuest ? 'btn-secondary' : 'btn-light';
                attachmentHTML = '<div class="admin-msg-attachment">'
                    + '<a href="' + fileUrl + '" target="_blank"'
                    + ' class="btn btn-sm ' + btnClass + '">'
                    + '<i class="fas fa-paperclip"></i> Attachment</a></div>';
            }
        }

        return '<div class="admin-msg-row ' + rowClass + '" data-msg-id="' + msg.id + '">'
             +   '<div class="admin-msg-avatar"><i class="fas ' + icon + '"></i></div>'
             +   '<div class="admin-msg-bubble-wrap">'
             +     '<div class="admin-msg-sender">' + sender + '</div>'
             +     '<div class="admin-msg-bubble">' + text + '</div>'
             +     attachmentHTML
             +     '<div class="admin-msg-time">' + time + '</div>'
             +   '</div>'
             + '</div>';
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function removeEmptyState() {
        var el = document.getElementById('empty-state');
        if (el) el.remove();
    }

    // ── Online / Last Seen — Guest ───────────────────────────────────────────
    function updateGuestPresence(isoTimestamp) {
        var dot  = document.querySelector('#guest-presence .presence-dot');
        var text = document.getElementById('guest-presence-text');
        if (!dot || !text) return;

        if (!isoTimestamp) {
            dot.className  = 'presence-dot offline';
            text.textContent = 'Not seen yet';
            text.className = 'text-muted';
            return;
        }

        var lastSeen = new Date(isoTimestamp);
        var diffSec  = Math.floor((Date.now() - lastSeen.getTime()) / 1000);

        if (diffSec <= 15) {
            dot.className    = 'presence-dot online';
            text.textContent = 'Online';
            text.className   = 'text-success';
        } else {
            dot.className = 'presence-dot offline';
            text.className = 'text-muted';
            var diffMin = Math.floor(diffSec / 60);
            if (diffMin < 1) {
                text.textContent = 'Last seen just now';
            } else if (diffMin === 1) {
                text.textContent = 'Last seen 1 minute ago';
            } else if (diffMin < 60) {
                text.textContent = 'Last seen ' + diffMin + ' minutes ago';
            } else {
                var diffHr = Math.floor(diffMin / 60);
                text.textContent = 'Last seen ' + diffHr + (diffHr === 1 ? ' hour' : ' hours') + ' ago';
            }
        }
    }

    // ── Notifikasi suara — generate "ding" via Web Audio API ────────────────
    // Tidak memerlukan file audio eksternal.
    // audioCtx dibuat lazy (saat pertama dipanggil) karena browser
    // melarang AudioContext sebelum ada interaksi user.
    var audioCtx = null;

    function playNotification() {
        try {
            // Buat AudioContext hanya sekali
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }

            // Resume jika suspended (autoplay policy)
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            // Oscillator — nada "ding" pendek
            var osc    = audioCtx.createOscillator();
            var gainNode = audioCtx.createGain();

            osc.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            osc.type      = 'sine';
            osc.frequency.setValueAtTime(880, audioCtx.currentTime);          // A5
            osc.frequency.exponentialRampToValueAtTime(660, audioCtx.currentTime + 0.15); // E5

            gainNode.gain.setValueAtTime(0.4, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.35);

            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.35);

            console.log('[ADMIN CHAT] NOTIFICATION SOUND played');
        } catch (e) {
            // Gagal diam-diam — tidak mengganggu fungsi chat
            console.warn('[ADMIN CHAT] NOTIFICATION SOUND failed:', e.message);
        }
    }

    // Unlock AudioContext saat user pertama berinteraksi dengan halaman
    // (klik, ketik, scroll) agar suara bisa diputar saat polling nanti.
    (function unlockAudio() {
        var unlocked = false;
        function tryUnlock() {
            if (unlocked) return;
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            unlocked = true;
            document.removeEventListener('click',   tryUnlock);
            document.removeEventListener('keydown', tryUnlock);
            document.removeEventListener('scroll',  tryUnlock);
            console.log('[ADMIN CHAT] AudioContext unlocked');
        }
        document.addEventListener('click',   tryUnlock);
        document.addEventListener('keydown', tryUnlock);
        document.addEventListener('scroll',  tryUnlock);
    }());
    function seedLastId() {
        if (!container) return;
        var rows = container.querySelectorAll('[data-msg-id]');
        rows.forEach(function (row) {
            var id = parseInt(row.getAttribute('data-msg-id'), 10);
            if (id > lastMessageId) lastMessageId = id;
        });
        console.log('[ADMIN CHAT] seedLastId — lastMessageId=' + lastMessageId);
    }

    // ── Polling — append-only, tidak replace innerHTML ───────────────────────
    function poll() {
        console.log('[ADMIN CHAT] POLL REQUEST');
        fetch(POLL_URL, {
            method:  'GET',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            cache:   'no-store'
        })
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function (data) {
            console.log('[ADMIN CHAT] POLL RESPONSE — status=' + data.status
                + ' messages=' + data.messages.length);

            // Update status online guest berdasarkan timestamp dari server
            updateGuestPresence(data.guest_last_seen_at || null);

            var atBottom = isNearBottom();
            var hasNew   = false;

            data.messages.forEach(function (msg) {
                if (msg.id > lastMessageId) {
                    console.log('[ADMIN CHAT] NEW MESSAGE — id=' + msg.id
                        + ' sender=' + msg.sender_type);
                    removeEmptyState();
                    container.insertAdjacentHTML('beforeend', buildMessageHTML(msg));
                    lastMessageId = msg.id;
                    hasNew = true;

                    // Notifikasi suara hanya untuk pesan dari guest
                    if (msg.sender_type === 'guest') {
                        playNotification();
                    }
                }
            });

            // Scroll hanya jika admin sudah berada di bawah
            if (hasNew && atBottom) {
                scrollToBottom(true);
            }

            // Conversation ditutup dari sisi lain — update UI tanpa reload
            if (data.status === 'closed' && !isClosed) {
                console.log('[ADMIN CHAT] CLOSED — updating UI');
                isClosed = true;
                var replyArea = document.getElementById('admin-reply-area');
                if (replyArea) {
                    replyArea.outerHTML =
                        '<div class="admin-chat-footer admin-chat-closed flex-shrink-0"'
                      +     ' id="admin-reply-area">'
                      +   '<i class="fas fa-lock"></i>'
                      +   ' This conversation is closed. Reopen to reply.'
                      + '</div>';
                }
                var badge = document.getElementById('admin-status-badge');
                if (badge) {
                    badge.className = 'badge badge-success';
                    badge.textContent = 'Closed';
                }
            }
        })
        .catch(function (err) {
            console.warn('[ADMIN CHAT] POLL ERROR — ' + err.message);
        });
    }

    // ── File attachment handler admin ────────────────────────────────────────
    function initAttachment() {
        var btnAttach    = document.getElementById('admin-btn-attach');
        var fileInput    = document.getElementById('admin-file-input');
        var filePreview  = document.getElementById('admin-file-preview');
        var fileNameSpan = document.getElementById('admin-file-name');
        var btnRemove    = document.getElementById('admin-btn-remove-file');

        if (!btnAttach || !fileInput) return;

        btnAttach.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                if (fileNameSpan) fileNameSpan.textContent = this.files[0].name;
                if (filePreview)  filePreview.classList.add('visible');
                btnAttach.classList.add('has-file');
            } else {
                clearAdminFile();
            }
        });

        if (btnRemove) {
            btnRemove.addEventListener('click', function () {
                clearAdminFile();
            });
        }

        function clearAdminFile() {
            fileInput.value = '';
            if (fileNameSpan) fileNameSpan.textContent = '';
            if (filePreview)  filePreview.classList.remove('visible');
            btnAttach.classList.remove('has-file');
        }
    }

    // ── Textarea auto-resize ─────────────────────────────────────────────────
    function initTextarea() {
        var ta = document.getElementById('admin-reply-textarea');
        if (!ta) return;

        ta.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Ctrl+Enter = submit, Enter = newline
        ta.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                e.preventDefault();

                var form = document.getElementById('admin-reply-form');

                if (form && this.value.trim() !== '') {
                    form.submit();
                }
            }
        });

        // Auto-focus textarea
        ta.focus();
    }

    // ── Boot ─────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        console.log('[ADMIN CHAT] DOMContentLoaded — boot');

        seedLastId();
        scrollToBottom(false);   // instant scroll saat pertama load
        initAttachment();
        initTextarea();

        if (!isClosed) {
            console.log('[ADMIN CHAT] POLLING STARTED — interval=' + POLL_INTERVAL + 'ms');
            setInterval(poll, POLL_INTERVAL);
        } else {
            console.log('[ADMIN CHAT] conversation closed — polling skipped');
        }
    });

}());
</script>
@endsection
