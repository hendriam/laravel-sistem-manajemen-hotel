@extends('app.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">
@endsection

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
                        <li class="breadcrumb-item"><a href="{{ route('guest.index') }}">{{ $title }}</a></li>
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
                        <form action="{{ route('guest.update', $data->id) }}" method="post" id="formUpdate" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            @method('PUT')
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Form Edit {{ $title }}</h5>
                                    <div class="card-tools">
                                        <a href="{{ route('guest.index') }}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @include('guest.form', ['data' => $data])
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <i class='fas fa-spinner fa-spin' style="display: none"></i> Simpan</button>
                                    <button type="button" class="btn btn-default" id="btnCancel"><i class="fas fa-window-close"></i> Batal</button>
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

@section('js')
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script>
        $('#formUpdate').on('submit', function (e) {
            e.preventDefault();
            
            // Hapus pesan error sebelumnya
            $('#formUpdate .is-invalid').removeClass('is-invalid');
            $('#formUpdate .invalid-feedback').remove();

            $('#formUpdate .fa-spinner').show();
            $('#formUpdate .fa-save').hide();

            const route = $('#formUpdate').attr('action');
            const formData = $("#formUpdate").serialize();
           
            $.ajax({
                url: route,
                method: 'PUT',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
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

                        if (errors.identity_type) {
                            $('#identity_type').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.identity_type}</div>`);
                        }

                        if (errors.identity_number) {
                            $('#identity_number').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.identity_number}</div>`);
                        }

                        if (errors.phone) {
                            $('#phone').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.phone}</div>`);
                        }

                        if (errors.address) {
                            $('#address').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.address}</div>`);
                        }
                        
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON.message,
                        });
                    }
                    $('#formUpdate .fa-spinner').hide();
                    $('#formUpdate .fa-save').show();
                }
            });
        });

    </script>
@endsection