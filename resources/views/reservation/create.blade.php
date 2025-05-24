@extends('app.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
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
                        <li class="breadcrumb-item"><a href="{{ route('reservation.index') }}">{{ $title }}</a></li>
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
                        <form action="{{ route('reservation.store') }}" method="post" id="formStore" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Form Tambah {{ $title }}</h5>
                                    <div class="card-tools">
                                        <a href="{{ route('guest.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambahkan Tamu</a>
                                        <a href="{{ route('reservation.index') }}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @include('reservation.form', ['data' => null, 'guests' => null, 'rooms' => null])
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
    <script src="{{ asset('assets/plugins/moment/moment.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{ asset('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
    <script>
        //Date picker check_in_date
        $('#check_in_date').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        //Date picker check_out_date
        $('#check_out_date').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        //Initialize Select2 Elements
        $('.select2bs44').select2({
            theme: 'bootstrap4',
            placeholder: "-- Cari nama tamu --",
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("guest.search") }}',
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

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            placeholder: "-- Pilih kamar --",
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("room.search") }}',
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
                                text: item.text,
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

                        if (errors.guest_id) {
                            $('#guest_id').addClass('is-invalid');
                            $('#roomType .select2-container').after(`<div class="invalid-feedback">${errors.guest_id}</div>`);
                        }

                        if (errors.room_id) {
                            $('#room_id').addClass('is-invalid');
                            $('#floor .select2-container').after(`<div class="invalid-feedback">${errors.room_id}</div>`);
                        }

                        if (errors.check_in_date) {
                            $('#check_in_date').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.check_in_date}</div>`);
                        }
                        
                        if (errors.check_out_date) {
                            $('#check_out_date').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.check_out_date}</div>`);
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