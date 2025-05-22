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
                        <li class="breadcrumb-item"><a href="{{ route('floor.index') }}">{{ $title }}</a></li>
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
                    <div class="col-12">
                        <form action="{{ route('floor.update', $data->id) }}" method="post" id="formAdd" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                             @method('PUT')
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Form Edit {{ $title }}</h5>
                                    <div class="card-tools">
                                        <a href="{{ route('floor.index') }}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            @if(session('success'))
                                                <div class="alert alert-success">
                                                    {{ session('success') }}
                                                </div>
                                            @endif

                                            <div class="mb-2">
                                                <label for="name" class="form-label">Nama Lantai</label>
                                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $data->name) }}" placeholder="Contoh: Lantai 1">
                                                @error('name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="mb-2">
                                                <label for="description" class="form-label">Keterangan</label>
                                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Contoh: Lantai 1 sedang diperbaiki">{{ old('description', $data->description) }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
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
