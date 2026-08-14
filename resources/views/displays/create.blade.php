@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Add Display</h1>
        </div>
        <div class="section-body">
            <a href="{{ route('display.index') }}" class="btn btn-primary"><i class="fas fa-list"></i>
                Display</a>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('display.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Display Name</label>
                                        <input type="text" id="name" class="form-control" name="name"
                                            placeholder="ex: Signage Meeting Room 1" required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Area Name</label>
                                        <select name="location" id="location" class="form-control select2" required>
                                            <option value="">Select Area</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}">
                                                    {{ $location->name . ' (' . $location->category . ')' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>View Screen Type</label>
                                        <select name="screen_type" id="screen_type" class="form-control" required>
                                            <option value="">Select View Screen Type</option>
                                            <option value="Landscape Left">Landscape Left</option>
                                            <option value="Landscape Right">Landscape Right</option>
                                            <option value="Portrait Up">Portrait Up</option>
                                            <option value="Portrait Down">Portrait Down</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Transition Time</label>
                                        <div class="input-group mb-2">
                                            <input type="number" class="form-control" id="transition_time" name="transition_time"
                                                placeholder="ex: 1" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">second</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Default Image</label>
                                        <input type="file" id="image" class="form-control" name="image" required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option selected value="1">Active</option>
                                            <option value="0">Inactive</option>
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
