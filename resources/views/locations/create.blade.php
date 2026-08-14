@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Add Location</h1>
        </div>
        <div class="section-body">
            <a href="{{ route('location.index') }}" class="btn btn-primary"><i class="fas fa-list"></i>
                Location</a>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('location.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Location Name</label>
                                        <input type="text" id="name" class="form-control" name="name"
                                            placeholder="ex: Meeting Room Melati" required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Location Category</label>
                                        <select name="category" id="category" class="form-control" required>
                                            <option value="">Select Location Category</option>
                                            <option value="Public">Public</option>
                                            <option value="Lobby">Lobby</option>
                                            <option value="Meeting">Meeting</option>
                                            <option value="Restaurant">Restaurant</option>
                                            <option value="Lift">Lift</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Floor</label>
                                        <input type="text" id="floor" class="form-control" name="floor"
                                            placeholder="ex: 1" required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option selected value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>Description</label>
                                        <textarea id="description" class="form-control" name="description" rows="5" style="height:100%;"
                                            placeholder="Description of area"></textarea>
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
