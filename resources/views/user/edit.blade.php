@extends('app.layout')

<!-- Content Header (Page header) -->
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $title }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('user.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-8">
                        <form action="{{ route('user.update', $data->id) }}" method="post" id="formAdd" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                             @method('PUT')
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Form Edit {{ $title }}</h5>
                                    <div class="card-tools">
                                        <a href="{{ route('user.index') }}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if(session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    <div class="mb-2">
                                        <label for="name" class="form-label">Nama</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $data->name) }}" placeholder="John Doe">
                                        @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $data->username) }}" placeholder="rath.dannie">
                                        @error('username')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="password" class="form-label">Role</label>
                                        <select class="form-control @error('role') is-invalid @enderror" name="role">
                                            <option >-- Role --</option>
                                            <option value="administrator" {{ $data->role == 'administrator' ? 'selected' : '' }}>Administrator</option>
                                            <option value="operator" {{ $data->role == 'operator' ? 'selected' : '' }}>Operator</option>
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                   
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
    </div>
@endsection
<!-- /.content -->
