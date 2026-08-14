@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Add Promotion</h1>
        </div>
        <div class="section-body">
            <a href="{{ route('promotion.index') }}" class="btn btn-primary"><i class="fas fa-list"></i>
                Promotion</a>
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
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <button id="btn_assign" class="btn btn-info btn-icon icon-left float-right"><i
                                            class="fas fa-calendar"></i>
                                        Auto
                                        Assign</button>
                                </div>
                            </div>
                            <form action="{{ route('promotion.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Promotion Name</label>
                                        <input type="text" id="name" class="form-control" name="name"
                                            placeholder="ex: Promotion Room Weekend" value="{{ old('name') }}" required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Promotion Date</label>
                                        <input type="date" id="date" class="form-control" name="date" value="{{ old('date') }}"  required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>View Screen Type</label>
                                        <select name="screen_type" id="screen_type" class="form-control" required>
                                            <option  {{ old('screen_type') == "Landscape" ? 'selected' : '' }}  value="Landscape">Landscape</option>
                                            <option {{ old('screen_type') == "Portrait" ? 'selected' : '' }}  value="Portrait">Portrait</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option {{ old('status') == "1" ? 'selected' : '' }}  value="1">Active</option>
                                            <option  {{ old('status') == "0" ? 'selected' : '' }}  value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="assign_container" class="d-none">
                                    <hr>
                                    <h6>Assign To Meeting Room Display</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="button" id="btn_remove_schedule"
                                                class="btn btn-danger btn-icon icon-left float-right"><i
                                                    class="fas fa-trash"></i>
                                                Remove Schedule</button>
                                        </div>
                                        <div class="form-group col-12">
                                            <label>Meeting Room Display</label>
                                            <select name="display" id="display" class="form-control select2">
                                                <option value="">Select Meeting Room Display</option>
                                                @foreach ($displays as $display)
                                                    <option {{ old('display') == $display->id ? 'selected' : '' }} value="{{ $display->id }}">
                                                        {{ $display->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-6">
                                            <label>Start Date</label>
                                            <input type="date" id="start_date" class="form-control" name="start_date" value="{{ old('start_date') }}">
                                        </div>
                                        <div class="form-group col-sm-12 col-md-6">
                                            <label>End Date</label>
                                            <input type="date" id="end_date" class="form-control" name="end_date" value="{{ old('end_date') }}">
                                        </div>
                                        <div class="form-group col-sm-12 col-md-6">
                                            <label>Start Time</label>
                                            <input type="time" id="start_time" class="form-control" name="start_time" value="{{ old('start_time') }}">
                                        </div>
                                        <div class="form-group col-sm-12 col-md-6">
                                            <label>End Time</label>
                                            <input type="time" id="end_time" class="form-control" name="end_time" value="{{ old('end_time') }}">
                                        </div>
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
        var old = {!! json_encode(old('display')) !!};
        if (old) {
            $('#assign_container').removeClass("d-none");
        }

        $('#btn_assign').on('click', function() {
            $('#assign_container').removeClass("d-none");
            $("#display").prop('required', true);
            $("#start_date").prop('required', true);
            $("#end_date").prop('required', true);
            $("#start_time").prop('required', true);
            $("#end_time").prop('required', true);
        });

        $('#btn_remove_schedule').on('click', function() {
            $('#assign_container').addClass("d-none");
            $("#display").val('').change();
            $("#start_date").val('');
            $("#end_date").val('');
            $("#start_time").val('');
            $("#end_time").val('');
            $("#display").prop('required', false);
            $("#start_date").prop('required', false);
            $("#end_date").prop('required', false);
            $("#start_time").prop('required', false);
            $("#end_time").prop('required', false);
        });
    </script>
@endsection
