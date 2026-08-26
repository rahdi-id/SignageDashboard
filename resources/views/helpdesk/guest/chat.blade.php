<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat — Hotel Helpdesk</title>

    {{-- FIX 3: jQuery dipindah ke <head> agar selalu tersedia sebelum DOMContentLoaded --}}
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
        integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <style>
        * { box-sizing: border-box; }

        /*
         * FIX 2: body menggunakan height: 100vh + overflow: hidden
         * Sebelumnya min-height: 100vh menyebabkan body bisa tumbuh melebihi
         * viewport sehingga .chat-messages tidak punya batas tinggi dan
         * overflow-y: auto tidak aktif — scroll terjadi di window, bukan
         * di container. Dengan height: 100vh + overflow: hidden, body
         * terkunci di viewport, .chat-messages flex:1 mendapat height limit
         * yang nyata, dan scrollbar muncul di container yang benar.
         */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2ff;
        }

        /* ── Top bar ── */
        .chat-topbar {
            background: #6777ef;
            color: #fff;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(103,119,239,0.3);
            z-index: 10;
        }

        .chat-topbar .topbar-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .chat-topbar .topbar-icon i {
            font-size: 18px;
        }

        .chat-topbar .topbar-info {
            flex: 1;
            min-width: 0;
        }

        .chat-topbar .topbar-info .name {
            font-weight: 700;
            font-size: 1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-topbar .topbar-info .sub {
            font-size: 0.78rem;
            opacity: 0.85;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-topbar .status-badge {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            flex-shrink: 0;
        }

        .status-open        { background: rgba(255,255,255,0.25); }
        .status-in_progress { background: rgba(255,193,7,0.35); }
        .status-closed      { background: rgba(0,0,0,0.25); }

        /* ── Error banner ── */
        .error-banner {
            background: #fdecea;
            border-bottom: 1px solid #f5c6cb;
            padding: 10px 14px;
            color: #721c24;
            font-size: 0.82rem;
            flex-shrink: 0;
        }

        /* ── Messages area ── */
        /*
         * flex: 1 + overflow-y: auto sekarang bekerja karena body
         * sudah dikunci di 100vh sehingga container ini mendapat
         * height limit yang nyata dari flexbox.
         */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            -webkit-overflow-scrolling: touch;
        }

        .msg-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }

        /* self = pesan dari guest sendiri → kanan */
        .msg-row.self  { flex-direction: row-reverse; }
        /* other = pesan dari admin/staff → kiri */
        .msg-row.other { flex-direction: row; }

        .msg-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 13px;
        }

        .msg-row.other .msg-avatar { background: #e0e0e0; color: #555; }
        .msg-row.self  .msg-avatar { background: #6777ef; color: #fff; }

        .msg-bubble-wrap {
            max-width: 75%;
            display: flex;
            flex-direction: column;
        }

        .msg-row.self  .msg-bubble-wrap { align-items: flex-end; }
        .msg-row.other .msg-bubble-wrap { align-items: flex-start; }

        .msg-sender {
            font-size: 0.72rem;
            font-weight: 600;
            color: #636e72;
            margin-bottom: 3px;
            padding: 0 4px;
        }

        .msg-bubble {
            padding: 10px 14px;
            border-radius: 16px;
            font-size: 0.92rem;
            line-height: 1.5;
            word-break: break-word;
            white-space: pre-wrap;
        }

        /* Pesan guest sendiri → kanan, ungu */
        .msg-row.self .msg-bubble {
            background: #6777ef;
            color: #fff;
            border-bottom-right-radius: 4px;
            box-shadow: 0 1px 4px rgba(103,119,239,0.25);
        }

        /* Pesan admin/staff → kiri, putih */
        .msg-row.other .msg-bubble {
            background: #fff;
            color: #2d3436;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        .msg-time {
            font-size: 0.7rem;
            color: #b2bec3;
            margin-top: 4px;
            padding: 0 4px;
        }

        .msg-row.self .msg-time { text-align: right; }

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
            margin-top: 6px;
        }

        .chat-img-preview:hover { opacity: 0.88; }
        .chat-img-link { display: inline-block; }

        .msg-attachment {
            margin-top: 6px;
        }

        .empty-state {
            text-align: center;
            color: #b2bec3;
            padding: 40px 20px;
            font-size: 0.9rem;
        }

        .empty-state i {
            font-size: 40px;
            display: block;
            margin-bottom: 10px;
            opacity: 0.4;
        }

        /* ── Input area ── */
        .chat-input-area {
            background: #fff;
            padding: 10px 12px;
            border-top: 1px solid #e8eaff;
            flex-shrink: 0;
        }

        /* Input row: attachment btn + textarea + send btn */
        .chat-input-row {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }

        .chat-textarea {
            flex: 1;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.95rem;
            resize: none;
            outline: none;
            font-family: inherit;
            min-height: 44px;
            max-height: 120px;
            line-height: 1.4;
            transition: border-color 0.2s;
            color: #2d3436;
        }

        .chat-textarea:focus {
            border-color: #6777ef;
        }

        /* Tombol attach (icon klip) */
        .btn-attach {
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
        }

        .btn-attach:hover        { border-color: #6777ef; color: #6777ef; }
        .btn-attach.has-file     { border-color: #6777ef; color: #6777ef; background: #f0f2ff; }

        .btn-send {
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
            font-size: 16px;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-send:hover  { background: #5a6fd6; }
        .btn-send:active { transform: scale(0.93); }
        .btn-send:disabled { background: #b2bec3; cursor: not-allowed; }

        /* Preview nama file terpilih */
        .chat-file-preview {
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

        .chat-file-preview.visible { display: flex; }

        .chat-file-preview .remove-file {
            margin-left: auto;
            cursor: pointer;
            color: #b2bec3;
            font-size: 13px;
            line-height: 1;
        }

        .chat-file-preview .remove-file:hover { color: #e74c3c; }

        .chat-closed-notice {
            text-align: center;
            padding: 14px 16px;
            background: #fff;
            border-top: 1px solid #e8eaff;
            color: #636e72;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .chat-closed-notice i {
            margin-right: 4px;
            color: #b2bec3;
        }

        /* ── Online / Last Seen indicator ── */
        .presence-indicator {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .presence-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .presence-dot.online  { background: #47c363; }
        .presence-dot.offline { background: rgba(255,255,255,0.45); }
    </style>
</head>
<body>

    {{-- Top bar --}}
    <div class="chat-topbar">
        <div class="topbar-icon">
            <i class="fas fa-headset"></i>
        </div>
        <div class="topbar-info">
            <div class="name">{{ $conversation->guest_name }}</div>
            <div class="sub">
                Room {{ $conversation->room_number }}
                &bull;
                {{ $conversation->department->name ?? 'Helpdesk' }}
            </div>
            <div class="presence-indicator mt-1" id="admin-presence" style="opacity:0.9">
                <div class="presence-dot offline"></div>
                <span id="admin-presence-text" style="color:rgba(255,255,255,0.8)">—</span>
            </div>
        </div>
        @php
            $statusLabels = ['open' => 'Open', 'in_progress' => 'In Progress', 'closed' => 'Closed'];
            $statusClass  = 'status-' . $conversation->status;
        @endphp
        <span class="status-badge {{ $statusClass }}" id="topbar-status-badge">
            {{ $statusLabels[$conversation->status] ?? $conversation->status }}
        </span>
    </div>

    {{-- Error banner --}}
    @if($errors->any())
    <div class="error-banner">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first() }}
    </div>
    @endif

    {{-- Messages --}}
    <div class="chat-messages" id="chat-messages">
        @forelse($conversation->messages as $msg)
            @php $isGuest = $msg->sender_type === 'guest'; @endphp
            {{-- self = pesan guest sendiri (kanan), other = pesan admin (kiri) --}}
            <div class="msg-row {{ $isGuest ? 'self' : 'other' }}" data-msg-id="{{ $msg->id }}">
                <div class="msg-avatar">
                    <i class="fas {{ $isGuest ? 'fa-user' : 'fa-user-tie' }}"></i>
                </div>
                <div class="msg-bubble-wrap">
                    <div class="msg-sender">
                        {{ $isGuest ? $conversation->guest_name : 'Hotel Staff' }}
                    </div>
                    <div class="msg-bubble">{{ $msg->message }}</div>
                    @if($msg->attachment)
                    @php
                        $ext   = strtolower(pathinfo($msg->attachment, PATHINFO_EXTENSION));
                        $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                    @endphp
                    <div class="msg-attachment">
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
                           class="btn btn-sm {{ $isGuest ? 'btn-light' : 'btn-secondary' }}">
                            <i class="fas fa-paperclip"></i> Attachment
                        </a>
                        @endif
                    </div>
                    @endif
                    <div class="msg-time">
                        {{ $msg->created_at->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-comments"></i>
                No messages yet. Start the conversation below.
            </div>
        @endforelse
    </div>

    {{-- Input area or closed notice --}}
    @if($conversation->status !== 'closed')
    <div class="chat-input-area" id="guest-input-area">
        <form action="{{ route('guest.helpdesk.send', $conversation->id) }}"
              method="POST"
              enctype="multipart/form-data"
              id="chat-form">
            @csrf
            {{-- Input file tersembunyi — dipicu oleh tombol icon attachment --}}
            <input type="file"
                   name="attachment"
                   id="chat-file-input"
                   accept=".jpg,.jpeg,.png,.gif,.webp"
                   style="display:none">
            <div class="chat-input-row">
                {{-- Tombol attachment --}}
                <button type="button" class="btn-attach" id="btn-attach" title="Attach image">
                    <i class="fas fa-paperclip"></i>
                </button>
                <textarea
                    name="message"
                    class="chat-textarea"
                    id="chat-textarea"
                    placeholder="Type a message or attach an image..."
                    rows="1"></textarea>
                <button type="submit" class="btn-send" id="btn-send" title="Send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            {{-- Preview nama file yang dipilih --}}
            <div class="chat-file-preview" id="chat-file-preview">
                <i class="fas fa-image"></i>
                <span id="chat-file-name"></span>
                <span class="remove-file" id="btn-remove-file" title="Remove">
                    <i class="fas fa-times"></i>
                </span>
            </div>
        </form>
    </div>
    @else
    <div class="chat-closed-notice" id="guest-input-area">
        <i class="fas fa-lock"></i> This conversation has been closed.
    </div>
    @endif

<script>
(function () {
    'use strict';

    console.log('[GUEST CHAT] JS LOADED');

    // ── Config ───────────────────────────────────────────────────────────────
    var POLL_INTERVAL = 4000;
    var GUEST_NAME = {{ Js::from($conversation->guest_name) }};
    var POLL_URL      = "{{ route('guest.helpdesk.messages', $conversation->id) }}";
    var SEND_URL      = "{{ route('guest.helpdesk.send', $conversation->id) }}";
    var CSRF_TOKEN    = '{{ csrf_token() }}';
    var isClosed      = {{ $conversation->status === 'closed' ? 'true' : 'false' }};

    var lastMessageId = 0;
    var container     = document.getElementById('chat-messages');

    // ── Scroll ───────────────────────────────────────────────────────────────
    function scrollToBottom(smooth) {
        if (!container) return;
        console.log('[GUEST CHAT] scroll to bottom (smooth=' + smooth + ')');
        if (smooth) {
            container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
        } else {
            container.scrollTop = container.scrollHeight;
        }
    }

    // ── HTML builder ─────────────────────────────────────────────────────────
    function isImageAttachment(filename) {
        if (!filename) return false;
        var ext = filename.split('.').pop().toLowerCase();
        return ['jpg', 'jpeg', 'png', 'gif', 'webp'].indexOf(ext) !== -1;
    }

    function buildMessageHTML(msg) {
        var isGuest = msg.sender_type === 'guest';
        // self = pesan guest sendiri → kanan, other = pesan admin → kiri
        var rowClass = isGuest ? 'self' : 'other';
        var icon     = isGuest ? 'fa-user' : 'fa-user-tie';
        var sender   = isGuest ? escapeHtml(GUEST_NAME) : 'Hotel Staff';
        var text     = escapeHtml(msg.message);
        var time     = escapeHtml(msg.created_at);

        var attachmentHTML = '';
        if (msg.attachment) {
            var fileUrl = '/images/helpdesk/' + encodeURIComponent(msg.attachment);
            if (isImageAttachment(msg.attachment)) {
                attachmentHTML = '<div class="msg-attachment">'
                    + '<a href="' + fileUrl + '" target="_blank" class="chat-img-link">'
                    + '<img src="' + fileUrl + '" alt="attachment" class="chat-img-preview">'
                    + '</a></div>';
            } else {
                // self (guest) → btn-light agar kontras di atas bubble ungu
                // other (admin) → btn-secondary
                var btnClass = isGuest ? 'btn-light' : 'btn-secondary';
                attachmentHTML = '<div class="msg-attachment">'
                    + '<a href="' + fileUrl + '" target="_blank"'
                    + ' class="btn btn-sm ' + btnClass + '">'
                    + '<i class="fas fa-paperclip"></i> Attachment</a></div>';
            }
        }

        return '<div class="msg-row ' + rowClass + '" data-msg-id="' + msg.id + '">'
             +   '<div class="msg-avatar"><i class="fas ' + icon + '"></i></div>'
             +   '<div class="msg-bubble-wrap">'
             +     '<div class="msg-sender">' + sender + '</div>'
             +     '<div class="msg-bubble">' + text + '</div>'
             +     attachmentHTML
             +     '<div class="msg-time">' + time + '</div>'
             +   '</div>'
             + '</div>';
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function removeEmptyState() {
        var empty = container ? container.querySelector('.empty-state') : null;
        if (empty) empty.remove();
    }

    // ── Notifikasi suara — bunyi saat pesan ADMIN masuk ke guest ────────────
    var audioCtx = null;

    function playNotification() {
        try {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            var osc      = audioCtx.createOscillator();
            var gainNode = audioCtx.createGain();
            osc.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(660, audioCtx.currentTime + 0.15);
            gainNode.gain.setValueAtTime(0.4, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.35);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.35);
            console.log('[GUEST CHAT] NOTIFICATION SOUND played');
        } catch (e) {
            console.warn('[GUEST CHAT] NOTIFICATION SOUND failed:', e.message);
        }
    }

    // Unlock AudioContext pada interaksi pertama user
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
            document.removeEventListener('click',      tryUnlock);
            document.removeEventListener('keydown',    tryUnlock);
            document.removeEventListener('touchstart', tryUnlock);
            console.log('[GUEST CHAT] AudioContext unlocked');
        }
        document.addEventListener('click',      tryUnlock);
        document.addEventListener('keydown',    tryUnlock);
        document.addEventListener('touchstart', tryUnlock);
    }());

    // ── Online / Last Seen — Admin ───────────────────────────────────────────
    function updateAdminPresence(isoTimestamp) {
        var dot  = document.querySelector('#admin-presence .presence-dot');
        var text = document.getElementById('admin-presence-text');
        if (!dot || !text) return;

        if (!isoTimestamp) {
            dot.className    = 'presence-dot offline';
            text.textContent = 'Staff not available';
            return;
        }

        var lastSeen = new Date(isoTimestamp);
        var diffSec  = Math.floor((Date.now() - lastSeen.getTime()) / 1000);

        if (diffSec <= 15) {
            dot.className    = 'presence-dot online';
            text.textContent = 'Staff Online';
        } else {
            dot.className = 'presence-dot offline';
            var diffMin = Math.floor(diffSec / 60);
            if (diffMin < 1) {
                text.textContent = 'Staff was here just now';
            } else if (diffMin === 1) {
                text.textContent = 'Staff last seen 1 minute ago';
            } else if (diffMin < 60) {
                text.textContent = 'Staff last seen ' + diffMin + ' minutes ago';
            } else {
                var diffHr = Math.floor(diffMin / 60);
                text.textContent = 'Staff last seen ' + diffHr + (diffHr === 1 ? ' hour' : ' hours') + ' ago';
            }
        }
    }

    // ── Seed lastMessageId dari DOM yang sudah dirender server ───────────────
    function seedLastId() {
        if (!container) return;
        var rows = container.querySelectorAll('[data-msg-id]');
        rows.forEach(function (row) {
            var id = parseInt(row.getAttribute('data-msg-id'), 10);
            if (id > lastMessageId) lastMessageId = id;
        });
        console.log('[GUEST CHAT] seedLastId — lastMessageId=' + lastMessageId);
    }

    // ── Polling ───────────────────────────────────────────────────────────────
    function poll() {
        console.log('[GUEST CHAT] POLL REQUEST — url=' + POLL_URL);
        $.ajax({
            url:      POLL_URL,
            method:   'GET',
            dataType: 'json',
            headers:  { 'Accept': 'application/json' },
            success: function (data) {
                console.log('[GUEST CHAT] POLL RESPONSE — status=' + data.status + ', messages=' + data.messages.length);

                // Update status online admin berdasarkan timestamp dari server
                updateAdminPresence(data.admin_last_seen_at || null);

                var hasNew = false;
                data.messages.forEach(function (msg) {
                    if (msg.id > lastMessageId) {
                        console.log('[GUEST CHAT] NEW MESSAGE FOUND — id=' + msg.id + ' sender=' + msg.sender_type);
                        removeEmptyState();
                        container.insertAdjacentHTML('beforeend', buildMessageHTML(msg));
                        lastMessageId = msg.id;
                        hasNew = true;

                        // Notifikasi suara hanya untuk pesan dari admin
                        if (msg.sender_type === 'admin') {
                            playNotification();
                        }
                    }
                });

                if (hasNew) {
                    scrollToBottom(true);
                }

                // Status berubah jadi closed — update UI tanpa reload
                if (data.status === 'closed' && !isClosed) {
                    console.log('[GUEST CHAT] CONVERSATION CLOSED — updating UI');
                    isClosed = true;
                    var inputArea = document.getElementById('guest-input-area');
                    if (inputArea) {
                        inputArea.outerHTML =
                            '<div class="chat-closed-notice" id="guest-input-area">'
                          +   '<i class="fas fa-lock"></i> This conversation has been closed.'
                          + '</div>';
                    }
                    // Update badge di topbar
                    var badge = document.getElementById('topbar-status-badge');
                    if (badge) {
                        badge.className = 'status-badge status-closed';
                        badge.textContent = 'Closed';
                    }
                }
            },
            error: function (xhr, status, err) {
                console.warn('[GUEST CHAT] POLL ERROR — status=' + xhr.status + ' ' + err);
            }
        });
    }

    // ── AJAX send (FormData — mendukung file upload) ─────────────────────────
    function initSendForm() {
        var form      = document.getElementById('chat-form');
        var textarea  = document.getElementById('chat-textarea');
        var btnSend   = document.getElementById('btn-send');
        var btnAttach = document.getElementById('btn-attach');
        var fileInput = document.getElementById('chat-file-input');
        var filePreview  = document.getElementById('chat-file-preview');
        var fileNameSpan = document.getElementById('chat-file-name');
        var btnRemove    = document.getElementById('btn-remove-file');

        if (!form || !textarea) {
            console.log('[GUEST CHAT] initSendForm — no form found (conversation closed?)');
            return;
        }

        console.log('[GUEST CHAT] initSendForm — ready');

        // ── File input handling ──────────────────────────────────────────────
        // Klik tombol klip → buka file picker
        if (btnAttach && fileInput) {
            btnAttach.addEventListener('click', function () {
                fileInput.click();
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    var name = this.files[0].name;
                    if (fileNameSpan) fileNameSpan.textContent = name;
                    if (filePreview)  filePreview.classList.add('visible');
                    if (btnAttach)    btnAttach.classList.add('has-file');
                } else {
                    clearFile();
                }
            });
        }

        if (btnRemove) {
            btnRemove.addEventListener('click', function () {
                clearFile();
            });
        }

        function clearFile() {
            if (fileInput)    fileInput.value = '';
            if (fileNameSpan) fileNameSpan.textContent = '';
            if (filePreview)  filePreview.classList.remove('visible');
            if (btnAttach)    btnAttach.classList.remove('has-file');
        }

        // ── Textarea auto-resize ─────────────────────────────────────────────
        textarea.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Enter = send, Shift+Enter = newline
        textarea.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                var hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
                if (this.value.trim() !== '' || hasFile) {
                    submitMessage();
                }
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
            if (textarea.value.trim() !== '' || hasFile) {
                submitMessage();
            }
        });

        // ── Submit via FormData (mendukung file) ─────────────────────────────
        function submitMessage() {
            var text    = textarea.value.trim();
            var hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

            if (!text && !hasFile) return;

            console.log('[GUEST CHAT] SEND MESSAGE — text=' + text.substring(0, 40)
                + ' hasFile=' + hasFile);

            if (btnSend)  btnSend.disabled  = true;
            if (btnAttach) btnAttach.disabled = true;
            textarea.disabled = true;

            var formData = new FormData();
            formData.append('_token', CSRF_TOKEN);
            if (text)    formData.append('message', text);
            if (hasFile) formData.append('attachment', fileInput.files[0]);

            fetch(SEND_URL, {
                method:  'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body:    formData,
                cache:   'no-store'
            })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { status: res.status, data: data };
                });
            })
            .then(function (result) {
                if (result.status === 201) {
                    var msg = result.data;
                    console.log('[GUEST CHAT] SEND SUCCESS — id=' + msg.id);
                    removeEmptyState();
                    if (msg.id > lastMessageId) {
                        container.insertAdjacentHTML('beforeend', buildMessageHTML(msg));
                        lastMessageId = msg.id;
                    }
                    textarea.value        = '';
                    textarea.style.height = 'auto';
                    clearFile();
                    scrollToBottom(true);
                } else {
                    var errMsg = 'Failed to send. Please try again.';
                    if (result.data && result.data.error) {
                        errMsg = result.data.error;
                    } else if (result.data && result.data.errors) {
                        var errs = result.data.errors;
                        var first = errs[Object.keys(errs)[0]];
                        if (first && first[0]) errMsg = first[0];
                    }
                    console.error('[GUEST CHAT] SEND ERROR — ' + errMsg);
                    alert(errMsg);
                }
            })
            .catch(function (err) {
                console.error('[GUEST CHAT] SEND FETCH ERROR — ' + err.message);
                alert('Network error. Please check your connection.');
            })
            .finally(function () {
                if (btnSend)   btnSend.disabled   = false;
                if (btnAttach) btnAttach.disabled  = false;
                textarea.disabled = false;
                textarea.focus();
            });
        }
    }

    // ── Boot ──────────────────────────────────────────────────────────────────
    $(document).ready(function () {
        console.log('[GUEST CHAT] DOMContentLoaded — boot');

        seedLastId();
        scrollToBottom(false);  // instant scroll on first load
        initSendForm();

        if (!isClosed) {
            console.log('[GUEST CHAT] POLLING STARTED — interval=' + POLL_INTERVAL + 'ms');
            setInterval(poll, POLL_INTERVAL);
        } else {
            console.log('[GUEST CHAT] conversation already closed — polling skipped');
        }
    });

}());
</script>
</body>
</html>
