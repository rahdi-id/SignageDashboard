@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Add Admin</h1>
        </div>
        <div class="section-body">
            <a href="{{ route('admin.index') }}" class="btn btn-primary"><i class="fas fa-list"></i>
                Administrator</a>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert">
                                            <span>&times;</span>
                                        </button>
                                        {{ $errors->first() }}
                                    </div>
                                </div>
                            @endif
                            <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label>Name</label>
                                        <input type="text" id="name" class="form-control" name="name"
                                            value="{{ old('name') }}" required>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>Email</label>
                                        <input type="email" id="email" class="form-control" name="email"
                                            value="{{ old('email') }}" required>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>Password</label>
                                        <input type="password" id="password" class="form-control" name="password"
                                            value="{{ old('password') }}" required>
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
