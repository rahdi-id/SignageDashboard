@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Add Custom Design</h1>
        </div>
        <div class="section-body">
            <a href="{{ route('design.index', $display->id) }}" class="btn btn-primary"><i class="fas fa-list"></i>
                Custom Design</a>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('design.store', $display->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Hotel Logo</label>
                                        <select name="hotel_logo" id="hotel_logo" class="form-control" required>
                                            <option value="Hotel Logo Colorful">Hotel Logo Colorful</option>
                                            <option value="Hotel Logo White">Hotel Logo White</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option selected value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Header/Side Image</label>
                                        <input type="file" id="image" class="form-control" name="header_side_image"
                                            required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Main Background Image</label>
                                        <input type="file" id="image" class="form-control" name="main_image"
                                            required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Font Color Header/Side</label>
                                        <input name="font_color_header_side" type="color" class="form-control" required>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Font Color Main</label>
                                        <input name="font_color_main" type="color" class="form-control" required>
                                    </div>
                                    <div class="form-group col-12">
                                        <label id="label_opacity">Opacity 50%</label>
                                        <input type="range" id="opacity" class="form-control" name="opacity"
                                            min="0" max="100" required>
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
        $("[type=range]").change(function() {
            var opacity = $(this).val();
            $("#label_opacity").html("Opacity " + opacity + "%");
        });
    </script>
@endsection
