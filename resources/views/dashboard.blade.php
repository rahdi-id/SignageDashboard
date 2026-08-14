@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-map-pin"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Location</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalLocation }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Promotion</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalPromotion }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Event</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalEvent }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Display</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalDisplay }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Schedule</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalSchedule }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div id='calendar'></div>
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
    </script>
@endsection
