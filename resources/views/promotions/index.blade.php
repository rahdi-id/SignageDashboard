@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Promotion</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert">
                                            <span>&times;</span>
                                        </button>
                                        {{ session('success') }}
                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-12 col-md-6 col-lg-2 px-0 py-3">
                                <a href="{{ route('promotion.create') }}" id="btn-modal"
                                    class="btn btn-block btn-icon icon-left btn-primary"><i class="fas fa-plus"></i>
                                    Add Promotion</a>
                            </div>
                            <hr>
                            <div class="row  px-0 my-4">
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <label>Filter by Status</label>
                                    <select id="status_filter" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-1">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Promotion Name</th>
                                            <th>Promotion Date</th>
                                            <th>View Type</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script>
        let deleteId;

        var t = $('#table-1').DataTable({
            "ajax": {
                url: "{{ url('promotions/data') }}",
            },
            "responsive": true,
            "processing": true,
            "columnDefs": [{
                "targets": -1,
                "data": null,
                "defaultContent": "<button id='edit' class='btn btn-icon icon-left btn-primary'><i class='fas fa-edit'></i> Edit</button> <button id='add_media' class='btn btn-icon icon-left btn-info'><i class='fas fa-images'></i> Add Media</button> <button id='delete' class='btn btn-icon icon-left btn-danger' data-toggle='modal' data-target='#deleteModal'><i class='fas fa-trash'></i> Delete</button>"
            }, {
                "targets": 0,
                "defaultContent": ""
            }],
            "columns": [{
                    data: ""
                },
                {
                    data: "name"
                },
                {
                    data: "formatted_date"
                },
                {
                    data: "screen_type"
                },
                {
                    data: "status",
                    render: function(status) {
                        if (status == 0)
                            return "<div class='badge badge-danger'>Inactive</div>";
                        else
                            return "<div class='badge badge-success'>Active</div>";
                    }
                },
                {
                    data: null
                },
            ]
        })

        t.on('order.dt search.dt', function() {
            let i = 1;

            t.cells(null, 0, {
                search: 'applied',
                order: 'applied'
            }).every(function(cell) {
                this.data("<p class ='text-primary'>" + i++ + "</p>");
            });
        }).draw();

        $('#table-1 tbody').on('click', '#delete', function() {
            var data = t.row($(this).parents('tr')).data();
            var url = "{{ route('promotion.destroy', ':id') }}";
            url = url.replace(':id', data['id'])
            $("#deleteForm").attr("action", url)
        });

        $('#table-1 tbody').on('click', '#edit', function() {
            var data = t.row($(this).parents('tr')).data();
            var url = "{{ route('promotion.edit', ':id') }}";
            url = url.replace(':id', data['id'])
            window.location.href = url;
        });

        $('#table-1 tbody').on('click', '#add_media', function() {
            var data = t.row($(this).parents('tr')).data();
            var url = "{{ route('promotion-media.index', ':id') }}";
            url = url.replace(':id', data['id'])
            window.location.href = url;
        });

        $('#status_filter').on('change', function() {
            let status;
            if ($(this).val() == "1") {
                status = "Active";
            } else if ($(this).val() == "0") {
                status = "Inactive";
            } else {
                status = "";
            }
            if (status != "")
                t.columns(4).search("^" + status + "$", true, false, true).draw();
            else
                t.columns(4).search(status).draw();
        });
    </script>
@endsection
