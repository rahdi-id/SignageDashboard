@extends('layouts.main')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Departments</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('helpdesk.dashboard') }}">Hotel Helpdesk</a></div>
            <div class="breadcrumb-item">Departments</div>
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
                <h4>Department List</h4>
                <div class="card-header-action">
                    <a href="{{ route('helpdesk.departments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Department
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="departments-table" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th>Tickets</th>
                                <th>Status</th>
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
    $(document).ready(function () {
        $('#departments-table').DataTable({
            processing: true,
            ajax: {
                url: '{{ route("helpdesk.departments.data") }}',
                dataSrc: 'data'
            },
            columns: [
                { data: null, render: (data, type, row, meta) => meta.row + 1 },
                { data: 'name' },
                { data: 'slug' },
                {
                    data: 'description',
                    render: d => d ?? '<span class="text-muted">-</span>'
                },
                {
                    data: 'conversations_count',
                    className: 'text-center',
                    render: d => `<span class="badge badge-info">${d}</span>`
                },
                {
                    data: 'is_active',
                    className: 'text-center',
                    render: d => d
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>'
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (id) {
                        return `
                            <a href="/helpdesk/departments/${id}/edit" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="${id}">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        `;
                    }
                }
            ],
            responsive: true,
            order: [[0, 'asc']]
        });

        // Delete using global #deleteModal from layouts/main.blade.php
        $(document).on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            $('#deleteForm').attr('action', `/helpdesk/departments/${id}`);
            $('#deleteModal').modal('show');
        });
    });
</script>
@endsection
