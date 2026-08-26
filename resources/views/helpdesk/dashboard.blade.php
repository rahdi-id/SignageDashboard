@extends('layouts.main')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Live Chat Dashboard</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Hotel Helpdesk</div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Open Chat</h4>
                    </div>
                    <div class="card-body" id="stat-open">
                        {{ $totalOpen }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>In Progress</h4>
                    </div>
                    <div class="card-body" id="stat-inprogress">
                        {{ $totalInProgress }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Closed Chat</h4>
                    </div>
                    <div class="card-body" id="stat-closed">
                        {{ $totalClosed }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Chat Today</h4>
                    </div>
                    <div class="card-body" id="stat-today">
                        {{ $totalToday }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Chat by Department --}}
        <div class="col-lg-5 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Chat by Department</h4>
                </div>
                <div class="card-body">
                    @forelse($chatByDepartment as $dept)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ $dept->name }}</span>
                            <span class="font-weight-bold">{{ $dept->conversations_count }}</span>
                        </div>
                        @php
                            $total = $totalOpen + $totalInProgress + $totalClosed;
                            $pct   = $total > 0 ? round(($dept->conversations_count / $total) * 100) : 0;
                        @endphp
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                                style="width: {{ $pct }}%"
                                aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">No departments found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Conversations --}}
        <div class="col-lg-7 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Recent Conversations</h4>
                    <div class="card-header-action">
                        <a href="{{ route('helpdesk.conversations.index') }}" class="btn btn-primary btn-sm">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="recent-table">
                            <thead>
                                <tr>
                                    <th>Guest</th>
                                    <th>Room</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody id="recent-tbody">
                                {{-- Diisi oleh DataTables via loadDashboard() --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
(function () {
    'use strict';

    // ── Konstanta warna/label ─────────────────────────────────────────────────
    var statusColors   = { open: 'primary', in_progress: 'warning', closed: 'success' };
    var statusLabels   = { open: 'Open',    in_progress: 'In Progress', closed: 'Closed' };
    var priorityColors = { low: 'secondary', medium: 'info', high: 'danger' };
    var priorityLabels = { low: 'Low',       medium: 'Medium', high: 'High' };

    // ── State tracking ───────────────────────────────────────────────────────
    var lastKnownTopId       = {{ $recentConversations->isNotEmpty() ? $recentConversations->first()->id : 0 }};
    var lastKnownTotalUnread = 0;
    var isFirstLoad          = true;

    // ── Notification sound ────────────────────────────────────────────────────
    var _audioCtx = null;

    function playDashboardNotification() {
        try {
            if (!_audioCtx) _audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (_audioCtx.state === 'suspended') _audioCtx.resume();
            var osc  = _audioCtx.createOscillator();
            var gain = _audioCtx.createGain();
            osc.connect(gain); gain.connect(_audioCtx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, _audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(660, _audioCtx.currentTime + 0.15);
            gain.gain.setValueAtTime(0.35, _audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, _audioCtx.currentTime + 0.35);
            osc.start(_audioCtx.currentTime);
            osc.stop(_audioCtx.currentTime + 0.35);
        } catch (e) {}
    }

    (function () {
        function tryUnlock() {
            if (!_audioCtx) _audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (_audioCtx.state === 'suspended') _audioCtx.resume();
            document.removeEventListener('click',   tryUnlock);
            document.removeEventListener('keydown', tryUnlock);
        }
        document.addEventListener('click',   tryUnlock);
        document.addEventListener('keydown', tryUnlock);
    }());

    // ── DataTables instance untuk Recent Conversations ────────────────────────
    var recentTable;

    function initRecentTable() {
        recentTable = $('#recent-table').DataTable({
            data:        [],  // diisi via loadDashboard()
            columns: [
                // Guest + unread badge
                {
                    data: null,
                    render: function (d) {
                        var unread  = d.unread_count || 0;
                        var badge   = unread > 0
                            ? '<span class="badge badge-danger ml-1" title="' + unread + ' unread">' + unread + '</span>'
                            : '';
                        var weight  = unread > 0 ? ' style="font-weight:600"' : '';
                        return '<a href="{{ url("/helpdesk/conversations") }}/' + d.id + '"' + weight + '>'
                             + $('<span>').text(d.guest_name).html() + '</a>' + badge;
                    }
                },
                { data: 'room_number' },
                { data: 'department' },
                {
                    data: 'status',
                    render: function (d) {
                        var sc = statusColors[d] || 'secondary';
                        return '<span class="badge badge-' + sc + '">' + (statusLabels[d] || d) + '</span>';
                    }
                },
                {
                    data: 'priority',
                    render: function (d) {
                        var pc = priorityColors[d] || 'secondary';
                        return '<span class="badge badge-' + pc + '">' + (priorityLabels[d] || d) + '</span>';
                    }
                },
                { data: 'created_at' }
            ],
            pageLength: 10,
            lengthMenu: [10, 25, 50],
            order:      [[5, 'desc']],
            responsive: true,
            searching:  false,   // dashboard tidak perlu search bar
            language: {
                processing:  'Loading...',
                emptyTable:  'No recent conversations.',
                info:        'Showing _START_ to _END_ of _TOTAL_ conversations',
                infoEmpty:   'No conversations',
                lengthMenu:  'Show _MENU_ entries',
                paginate: {
                    next:     'Next',
                    previous: 'Previous'
                }
            }
        });
    }

    // ── loadDashboard() ───────────────────────────────────────────────────────
    function loadDashboard() {
        $.get('{{ route("helpdesk.stats") }}', function (data) {

            // Update stat cards
            $('#stat-open').text(data.totalOpen);
            $('#stat-inprogress').text(data.totalInProgress);
            $('#stat-closed').text(data.totalClosed);
            $('#stat-today').text(data.totalToday);

            var conversations = data.recentConversations;

            // Deteksi notifikasi
            var newTopId       = conversations.length > 0 ? conversations[0].id : lastKnownTopId;
            var newTotalUnread = 0;
            $.each(conversations, function (i, c) { newTotalUnread += (c.unread_count || 0); });

            if (!isFirstLoad) {
                if (newTopId > lastKnownTopId || newTotalUnread > lastKnownTotalUnread) {
                    playDashboardNotification();
                }
            }
            lastKnownTopId       = newTopId;
            lastKnownTotalUnread = newTotalUnread;
            isFirstLoad          = false;

            // Update DataTables — draw(false) mempertahankan current page
            recentTable.clear().rows.add(conversations).draw(false);
        });
    }

    // ── Boot ─────────────────────────────────────────────────────────────────
    $(document).ready(function () {
        initRecentTable();

        // Load pertama
        loadDashboard();

        // Auto-refresh setiap 10 detik — current page dipertahankan oleh draw(false)
        setInterval(loadDashboard, 10000);
    });

}());
</script>
@endsection
