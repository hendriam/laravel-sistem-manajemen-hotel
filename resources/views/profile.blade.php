@extends('app.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">
@endsection

<!-- Content Header (Page header) -->
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $title }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active">Index</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- /.col-md-6 -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="m-0">Profile</h5>
                            </div>
                            <div class="card-body">
                                 <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td><strong>Nama</strong></td>
                                            <td>{{ auth()->user()->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Username</strong></td>
                                            <td>{{ auth()->user()->username }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Role</strong></td>
                                            <td>{{ auth()->user()->role }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#ganti-password" data-toggle="tab">Ganti Password</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#edit-profile" data-toggle="tab">Edit Profile</a></li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="active tab-pane" id="ganti-password">
                                        <form method="post" action="{{ route('profile.ganti-password', auth()->user()->id) }}" id="formGantiPassword" enctype="multipart/form-data" autocomplete="off">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-2">
                                                <label for="password" class="form-label">Password</label>
                                                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password baru" />
                                            </div>
                                            <div class="mb-2">
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-save"></i> <i class='fas fa-spinner fa-spin' style="display: none"></i> Simpan</button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane" id="edit-profile">
                                        <form method="post" action="{{ route('profile.update-profile', auth()->user()->id) }}" id="formEditProfile" enctype="multipart/form-data" autocomplete="off">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-2">
                                                <label for="name" class="form-label">Nama</label>
                                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" placeholder="Masukkan nama baru" />
                                            </div>
                                            <div class="mb-2">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" name="username" id="username" class="form-control" value="{{ old('username', auth()->user()->username) }}" placeholder="Masukkan username baru" />
                                            </div>
                                            <div class="mb-2">
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-save"></i> <i class='fas fa-spinner fa-spin' style="display: none"></i> Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-md-6 -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
    </div>
@endsection
<!-- /.content -->

@section('js')
    <script src="{{ asset('assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script>

        // submit ganti passwor
        $('#formGantiPassword').on('submit', function (e) {
            e.preventDefault();
            
            // Hapus pesan error sebelumnya
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $('#formGantiPassword .fa-spin').show();
            $('#formGantiPassword .fa-save').hide();

            const formData = {
                password: $('#password').val(),
                _token: '{{ csrf_token() }}'
            };

            const route = $('#formGantiPassword').attr('action');
           
            $.ajax({
                url: route,
                method: 'PUT',
                data: formData,
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                    }).then(() => {
                        window.location.href = res.redirect;
                    });
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.password) {
                            $('#password').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.password}</div>`);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON.message,
                        });
                    }
                    $('#formGantiPassword .fa-spin').hide();
                    $('#formGantiPassword .fa-save').show();
                }
            });
        });

        // submit edit profile
        $('#formEditProfile').on('submit', function (e) {
            e.preventDefault();
            
            // Hapus pesan error sebelumnya
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $('#formEditProfile .fa-spin').show();
            $('#formEditProfile .fa-save').hide();

            const formData = {
                name: $('#name').val(),
                username: $('#username').val(),
                _token: '{{ csrf_token() }}'
            };

            const route = $('#formEditProfile').attr('action');
           
            $.ajax({
                url: route,
                method: 'PUT',
                data: formData,
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                    }).then(() => {
                        window.location.href = res.redirect;
                    });
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.name) {
                            $('#name').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.name}</div>`);
                        }
                        if (errors.username) {
                            $('#username').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.username}</div>`);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON.message,
                        });
                    }
                    $('#formEditProfile .fa-spin').hide();
                    $('#formEditProfile .fa-save').show();
                }
            });
        });
    </script>
@endsection