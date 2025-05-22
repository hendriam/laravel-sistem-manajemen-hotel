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
                        <li class="breadcrumb-item"><a href="{{ route('floor.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
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
                        <form action="{{ route('room.store') }}" method="post" id="formStore" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Form Tambah {{ $title }}</h5>
                                    <div class="card-tools">
                                        <a href="{{ route('room.index') }}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                   <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label for="name" class="form-label">Nama Kamar</label>
                                                <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Kamar 1">
                                            </div>

                                            <div class="mb-2">
                                                <label for="price" class="form-label">Harga</label>
                                                <input type="number" name="price" id="price" class="form-control" placeholder="Contoh: 150000">
                                            </div>

                                            <div class="mb-2">
                                                <label for="floor_id" class="form-label">Lantai</label>
                                                <select class="form-control select2bs4" name="floor_id" id="floor_id"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value="">-- Select status --</option>
                                                    <option value="available">Tersedia</option>
                                                    <option value="occupied">Terisi</option>
                                                    <option value="closed">Tutup</option>
                                                </select>
                                            </div>

                                            <div class="mb-2">
                                                <label for="description" class="form-label">Keterangan</label>
                                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Contoh: Kamar 1 sedang diperbaiki"></textarea>
                                            </div>
                                        </div>
                                   </div>
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
        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            placeholder: "-- Pilih lantai --",
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("floor.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id,
                                text: item.name,
                                name: item.name,
                            };
                        })
                    };
                },
                cache: true
            }
        })

        $('#formStore').on('submit', function (e) {
            e.preventDefault();
            
            // Hapus pesan error sebelumnya
            $('#formStore .is-invalid').removeClass('is-invalid');
            $('#formStore .invalid-feedback').remove();

            $('#formStore .fa-spinner').show();
            $('#formStore .fa-save').hide();

            const route = $('#formStore').attr('action');
            const formData = $("#formStore").serialize();
           
            $.ajax({
                url: route,
                method: 'POST',
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

                        if (errors.price) {
                            $('#price').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.price}</div>`);
                        }

                        if (errors.floor_id) {
                            $('#floor_id').addClass('is-invalid');
                            $('.select2bs4 .select2-container').after(`<div class="invalid-feedback">${errors.floor_id}</div>`);
                        }

                        if (errors.status) {
                            $('#status').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.status}</div>`);
                        }

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON.message,
                        });
                    }
                    $('#formStore .fa-spinner').hide();
                    $('#formStore .fa-save').show();
                }
            });
        });

    </script>
@endsection