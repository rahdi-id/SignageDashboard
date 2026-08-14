@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Edit Event</h1>
        </div>
        <div class="section-body">
            <a href="{{ route('event.index') }}" class="btn btn-primary"><i class="fas fa-list"></i>
                Event</a>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('event.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Event Name</label>
                                        <input type="text" id="name" class="form-control" name="name" value="{{ $event->name }}"
                                            placeholder="ex: Rapat Direksi PT ABC"  required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Participant Name</label>
                                        <input type="text" id="participant_name" class="form-control" name="participant_name" value="{{ $event->participant_name }}"
                                            placeholder="ex: PT ABC" required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Event Date</label>
                                        <input type="date" id="date" class="form-control" name="date" value="{{ $event->date }}" required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option  {{ $event->status == 1 ? 'selected' : '' }} value="1">Active</option>
                                            <option  {{ $event->status == 0 ? 'selected' : '' }} value="0">Inactive</option>
                                        </select>
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
