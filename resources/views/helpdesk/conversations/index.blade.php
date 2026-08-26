@extends('layouts.main')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Conversations</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('helpdesk.dashboard') }}">Hotel Helpdesk</a></div>
            <div class="breadcrumb-item">Conversations</div>
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
        <div class="card">
            <div class="card-header">
                <h4>All Conversations</h4>
            </div>
            <div class="card-body">
                {{-- Filter Bar --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="text" id="filter-guest" class="form-control form-control-sm"
                            placeholder="Search guest name...">
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="filter-room" class="form-control form-control-sm"
                            placeholder="Search room number...">
                    </div>
                    <div class="col-md-2">
                        <select id="filter-department" class="form-control form-control-sm">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="filter-status" class="form-control form-control-sm">
                            <option value="">All Status</option>
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button id="btn-filter" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <button id="btn-reset" class="btn btn-secondary btn-sm">
                            <i class="fas fa-times"></i> Reset
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="conversations-table" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Guest Name</th>
                                <th>Room</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
    // ── Konstanta warna/label ─────────────────────────────────────────────────
    const statusColors  = { open: 'primary', in_progress: 'warning', closed: 'success' };
    const statusLabels  = { open: 'Open',    in_progress: 'In Progress', closed: 'Closed' };
    const priorityColors = { low: 'secondary', medium: 'info', high: 'danger' };
    const priorityLabels = { low: 'Low',       medium: 'Medium', high: 'High' };

    // ── State notifikasi ──────────────────────────────────────────────────────
    var lastKnownTopId       = 0;
    var lastKnownTotalUnread = 0;
    var isFirstLoad          = true;

    // ── Notification sound — Web Audio API ───────────────────────────────────
    var _audioCtx = null;

    function playListNotification() {
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

    // ── Bangun URL endpoint dengan filter aktif ───────────────────────────────
    function buildUrl() {
        var base   = '{{ route("helpdesk.conversations.data") }}';
        var params = new URLSearchParams();
        var gn = $('#filter-guest').val();
        var rn = $('#filter-room').val();
        var di = $('#filter-department').val();
        var st = $('#filter-status').val();
        if (gn) params.append('guest_name',    gn);
        if (rn) params.append('room_number',   rn);
        if (di) params.append('department_id', di);
        if (st) params.append('status',        st);
        var qs = params.toString();
        return qs ? base + '?' + qs : base;
    }

    // ── DataTables instance ───────────────────────────────────────────────────
    var convTable;

    $(document).ready(function () {

        convTable = $('#conversations-table').DataTable({
            processing: true,
            ajax: {
                url:     buildUrl(),
                dataSrc: function (json) {
                    // Deteksi notifikasi setelah data diterima
                    var rows = json.data || [];
                    if (rows.length > 0) {
                        var newTopId       = rows[0].id;
                        var newTotalUnread = 0;
                        $.each(rows, function (i, r) { newTotalUnread += (r.unread_count || 0); });

                        if (!isFirstLoad) {
                            if (newTopId > lastKnownTopId || newTotalUnread > lastKnownTotalUnread) {
                                playListNotification();
                            }
                        }
                        lastKnownTopId       = newTopId;
                        lastKnownTotalUnread = newTotalUnread;
                        isFirstLoad          = false;
                    } else {
                        isFirstLoad = false;
                    }
                    return rows;
                }
            },
            columns: [
                // #
                { data: null, render: (d, t, r, meta) => meta.row + 1, orderable: false, searchable: false },
                // Guest Name + unread badge
                {
                    data: null,
                    render: function (d) {
                        var unread = d.unread_count || 0;
                        var badge  = unread > 0
                            ? '<span class="badge badge-danger ml-1" title="' + unread + ' unread">' + unread + '</span>'
                            : '';
                        var style  = unread > 0 ? ' style="font-weight:600"' : '';
                        return '<span' + style + '>' + $('<span>').text(d.guest_name).html() + badge + '</span>';
                    }
                },
                // Room
                { data: 'room_number' },
                // Department
                {
                    data: 'department',
                    render: function (d) {
                        return d ? $('<span>').text(d.name).html() : '-';
                    }
                },
                // Status
                {
                    data: 'status',
                    render: function (d) {
                        var sc = statusColors[d]  || 'secondary';
                        var sl = statusLabels[d]  || d;
                        return '<span class="badge badge-' + sc + '">' + sl + '</span>';
                    }
                },
                // Priority
                {
                    data: 'priority',
                    render: function (d) {
                        var pc = priorityColors[d] || 'secondary';
                        var pl = priorityLabels[d] || d;
                        return '<span class="badge badge-' + pc + '">' + pl + '</span>';
                    }
                },
                // Created At
                {
                    data: 'created_at',
                    render: function (d) {
                        return new Date(d).toLocaleString('id-ID');
                    }
                },
                // Action
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (id) {
                        return '<a href="{{ url("/helpdesk/conversations") }}/' + id + '" class="btn btn-info btn-sm">'
                             + '<i class="fas fa-eye"></i> View</a>';
                    }
                }
            ],
            pageLength:  10,
            lengthMenu:  [10, 25, 50],
            order:       [[6, 'desc']], // urut Created At terbaru
            responsive:  true,
            language: {
                processing:     'Loading...',
                emptyTable:     'No conversations found.',
                zeroRecords:    'No conversations match the current filter.',
                info:           'Showing _START_ to _END_ of _TOTAL_ conversations',
                infoEmpty:      'Showing 0 to 0 of 0 conversations',
                infoFiltered:   '(filtered from _MAX_ total)',
                lengthMenu:     'Show _MENU_ entries',
                paginate: {
                    first:    'First',
                    last:     'Last',
                    next:     'Next',
                    previous: 'Previous'
                }
            }
        });

        // ── Filter bar ────────────────────────────────────────────────────────
        // Saat filter berubah: update URL endpoint, reset ke page 1 (data baru)
        function applyFilter() {
            convTable.ajax.url(buildUrl()).load(null, true); // true = reset paging
        }

        $('#btn-filter').on('click', applyFilter);

        $('#btn-reset').on('click', function () {
            $('#filter-guest').val('');
            $('#filter-room').val('');
            $('#filter-department').val('');
            $('#filter-status').val('');
            applyFilter();
        });

        $('#filter-guest, #filter-room').on('keypress', function (e) {
            if (e.which === 13) applyFilter();
        });

        // ── Auto-refresh setiap 8 detik — pertahankan current page ───────────
        setInterval(function () {
            convTable.ajax.url(buildUrl()).load(null, false); // false = jangan reset paging
        }, 8000);
    });
</script>
@endsection
