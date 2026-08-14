@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Edit Promotion</h1>
        </div>
        <div class="section-body">
            <a href="{{ route('promotion.index') }}" class="btn btn-primary"><i class="fas fa-list"></i>
                Promotion</a>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('promotion.update', $promotion->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @METHOD('PUT')
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Promotion Name</label>
                                        <input type="text" id="name" class="form-control" name="name"
                                            placeholder="ex: Promotion Room Weekend" value="{{ $promotion->name }}" required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Promotion Date</label>
                                        <input type="date" id="date" class="form-control" name="date" value="{{ $promotion->date }}"  required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>View Screen Type</label>
                                        <select name="screen_type" id="screen_type" class="form-control" required>
                                            <option  {{ $promotion->screen_type == "Landscape" ? 'selected' : '' }}  value="Landscape">Landscape</option>
                                            <option {{ $promotion->screen_type == "Portrait" ? 'selected' : '' }}  value="Portrait">Portrait</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option {{  $promotion->status== "1" ? 'selected' : '' }}  value="1">Active</option>
                                            <option  {{  $promotion->status == "0" ? 'selected' : '' }}  value="0">Inactive</option>
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
