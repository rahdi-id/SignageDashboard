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
    const statusColors = {
        'open': 'primary',
        'in_progress': 'warning',
        'closed': 'success'
    };
    const statusLabels = {
        'open': 'Open',
        'in_progress': 'In Progress',
        'closed': 'Closed'
    };
    const priorityColors = {
        'low': 'secondary',
        'medium': 'info',
        'high': 'danger'
    };
    const priorityLabels = {
        'low': 'Low',
        'medium': 'Medium',
        'high': 'High'
    };

    function loadTable() {
        const params = {
            guest_name:    $('#filter-guest').val(),
            room_number:   $('#filter-room').val(),
            department_id: $('#filter-department').val(),
            status:        $('#filter-status').val(),
        };

        $.get('{{ route("helpdesk.conversations.data") }}', params, function (response) {
            const tbody = $('#conversations-table tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.append('<tr><td colspan="8" class="text-center text-muted">No conversations found.</td></tr>');
                return;
            }

            $.each(response.data, function (i, conv) {
                const sc = statusColors[conv.status] || 'secondary';
                const sl = statusLabels[conv.status] || conv.status;
                const pc = priorityColors[conv.priority] || 'secondary';
                const pl = priorityLabels[conv.priority] || conv.priority;
                const dept = conv.department ? conv.department.name : '-';
                const createdAt = new Date(conv.created_at).toLocaleString('id-ID');

                tbody.append(`
                    <tr>
                        <td>${i + 1}</td>
                        <td>${conv.guest_name}</td>
                        <td>${conv.room_number}</td>
                        <td>${dept}</td>
                        <td><span class="badge badge-${sc}">${sl}</span></td>
                        <td><span class="badge badge-${pc}">${pl}</span></td>
                        <td>${createdAt}</td>
                        <td>
                            <a href="/helpdesk/conversations/${conv.id}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                `);
            });
        });
    }

    $(document).ready(function () {
        loadTable();

        $('#btn-filter').on('click', function () {
            loadTable();
        });

        $('#btn-reset').on('click', function () {
            $('#filter-guest').val('');
            $('#filter-room').val('');
            $('#filter-department').val('');
            $('#filter-status').val('');
            loadTable();
        });

        // Allow pressing Enter in search inputs to trigger filter
        $('#filter-guest, #filter-room').on('keypress', function (e) {
            if (e.which === 13) loadTable();
        });
    });
</script>
@endsection
