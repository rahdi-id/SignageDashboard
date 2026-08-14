@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Add Promotion Media</h1>
        </div>
        <div class="section-body">
            <a href="{{ route('promotion-media.index', $promotionId) }}" class="btn btn-primary"><i class="fas fa-list"></i>
                Promotion Media</a>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('promotion-media.store', $promotionId) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label>Media</label>
                                        <input type="file" class="form-control" name="media[]" accept="video/*,image/*">
                                    </div>
                                    <div class="col-12">
                                        <button id="btn_add_more_media" type="button"
                                            class="btn btn-info btn-icon icon-left"><i class='fas fa-images'></i> Add More
                                            Media</button>
                                        <button id="btn_add_more_youtube" type="button"
                                            class="btn btn-danger btn-icon icon-left"><i class='fab fa-youtube'></i> Add
                                            More Youtube Video</button>
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
        $('#btn_add_more_media').on('click', function() {
            $('<div class="form-group col-12"><input type="file" class="form-control" name="media[]" accept="video/*,image/*"></div>')
                .insertAfter('.form-group:last');
        })

        $('#btn_add_more_youtube').on('click', function() {
            $('<div class="form-group col-12"><input type="text" class="form-control" name="youtube[]" placeholder="Type your youtube url"></div>')
                .insertAfter('.form-group:last');
        })
    </script>
@endsection
