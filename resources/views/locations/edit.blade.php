@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Edit Location</h1>
        </div>
        <div class="section-body">
            <a href="{{ route('location.index') }}" class="btn btn-primary"><i class="fas fa-list"></i>
                Location</a>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('location.update', $location->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="form-group  col-sm-12 col-md-6">
                                        <label>Location Name</label>
                                        <input type="text" id="name" class="form-control" name="name"
                                            value="{{ $location->name }}" placeholder="ex: Meeting Room Melati" required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Location Category</label>
                                        <select name="category" id="category" class="form-control" required>
                                            <option value="">Select Location Category</option>
                                            <option {{ $location->category == 'Public' ? 'selected' : '' }} value="Public">
                                                Public</option>
                                            <option {{ $location->category == 'Lobby' ? 'selected' : '' }} value="Lobby">
                                                Lobby</option>
                                            <option {{ $location->category == 'Meeting' ? 'selected' : '' }}
                                                value="Meeting">Meeting</option>
                                            <option {{ $location->category == 'Restaurant' ? 'selected' : '' }}
                                                value="Restaurant">Restaurant</option>
                                            <option {{ $location->category == 'Lift' ? 'selected' : '' }} value="Lift">
                                                Lift</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Floor</label>
                                        <input type="text" id="floor" class="form-control" name="floor"
                                            value="{{ $location->floor }}" placeholder="ex: 1" required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option {{ $location->status == 1 ? 'selected' : '' }} value="1">Active
                                            </option>
                                            <option {{ $location->status == 0 ? 'selected' : '' }} value="0">Inactive
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>Description</label>
                                        <textarea id="description" class="form-control" name="description" rows="5" style="height:100%;"
                                            placeholder="Description of area">{{ $location->description }}</textarea>
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
