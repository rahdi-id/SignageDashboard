@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Add Schedule</h1>
        </div>
        <div class="section-body">
            <a href="{{ route('schedule.index') }}" class="btn btn-primary"><i class="fas fa-list"></i>
                Schedule</a>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert">
                                            <span>&times;</span>
                                        </button>
                                        {{ session('error') }}
                                    </div>
                                </div>
                            @endif
                            <form action="{{ route('schedule.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label>Meeting Room Display</label>
                                        <select name="display" id="display" class="form-control select2">
                                            <option value="">Select Meeting Room Display</option>
                                            @foreach ($displays as $display)
                                                <option {{ old('display') == $display->id ? 'selected' : '' }}
                                                    value="{{ $display->id }}">
                                                    {{ $display->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Event/Promotion Type</label>
                                        <select name="event_promotion_type" id="event_promotion_type" class="form-control">
                                            <option value="">Select Event/Promotion Type</option>
                                            <option value="Event">Event</option>
                                            <option value="Promotion">Promotion</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Event/Promotion</label>
                                        <select name="event_promotion" id="event_promotion" class="form-control">
                                            <option value="">Select Event/Promotion</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Start Date</label>
                                        <input type="date" id="start_date" class="form-control" name="start_date"
                                            value="{{ old('start_date') }}">
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>End Date</label>
                                        <input type="date" id="end_date" class="form-control" name="end_date"
                                            value="{{ old('end_date') }}">
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Start Time</label>
                                        <input type="time" id="start_time" class="form-control" name="start_time"
                                            value="{{ old('start_time') }}">
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>End Time</label>
                                        <input type="time" id="end_time" class="form-control" name="end_time"
                                            value="{{ old('end_time') }}">
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
@section('js')
    <script>
        $('#event_promotion_type').on('change', function() {
            $('#event_promotion')
                .find('option')
                .remove()
                .end()
                .append('<option value="">Select Event/Promotion</option>')
                .val('');
            if (this.value == 'Event') {
                var events = {!! json_encode($events) !!};
                events.forEach(event => {
                    $('#event_promotion')
                        .find('option')
                        .end()
                        .append('<option value="' + event['id'] + '">' + event['name'] + '</option>')
                        .val('');
                });
            } else if(this.value == 'Promotion') {
                var promotions = {!! json_encode($promotions) !!};
                promotions.forEach(promotion => {
                    $('#event_promotion')
                        .find('option')
                        .end()
                        .append('<option value="' + promotion['id'] + '">' + promotion['name'] + '</option>')
                        .val('');
                });
            }
        });
    </script>
@endsection
