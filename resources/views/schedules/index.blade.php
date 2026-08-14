@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Schedule</h1>
        </div>
        <div class="section-body">
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
                        <a href="{{ route('schedule.create') }}" id="btn-modal"
                            class="btn btn-block btn-icon icon-left btn-primary"><i class="fas fa-plus"></i>
                            Add Schedule</a>
                    </div>
                    <hr>
                    <div id='calendar'></div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Display</th>
                                    <th>Event / Promotion</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
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
    </section>
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: @json($data),
            });
            calendar.render();
        });

        var t = $('#table-1').DataTable({
            "ajax": {
                url: "{{ url('schedules/data') }}",
            },
            "responsive": true,
            "processing": true,
            "columnDefs": [{
                "targets": -1,
                "data": null,
                "defaultContent": "<button id='edit' class='btn btn-icon icon-left btn-primary'><i class='fas fa-edit'></i> Edit</button> <button id='delete' class='btn btn-icon icon-left btn-danger' data-toggle='modal' data-target='#deleteModal'><i class='fas fa-trash'></i> Delete</button>"
            }, {
                "targets": 0,
                "defaultContent": ""
            }],
            "columns": [{
                    data: ""
                },
                {
                    data: "display.name"
                },
                {
                    data: null,
                    render: function(schedule){
                        if(schedule.promotion == null){
                            return schedule.event.name;
                        } else{
                            return schedule.promotion.name;
                        }
                    }
                },
                {
                    data: "formatted_start_date_time"
                },
                {
                    data: "formatted_end_date_time"
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
            var url = "{{ route('schedule.destroy', ':id') }}";
            url = url.replace(':id', data['id'])
            $("#deleteForm").attr("action", url)
        });

        $('#table-1 tbody').on('click', '#edit', function() {
            var data = t.row($(this).parents('tr')).data();
            var url = "{{ route('schedule.edit', ':id') }}";
            url = url.replace(':id', data['id'])
            window.location.href = url;
        });
    </script>
@endsection
