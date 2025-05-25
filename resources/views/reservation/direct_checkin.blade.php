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
                        <li class="breadcrumb-item active">Check-in Lansung</li>
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
                        <form action="{{ route('reservation.direct.store') }}" method="post" id="formStore" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Form Checkin Lansung</h5>
                                    <div class="card-tools">
                                        <button type="button" id="btnCreateGuest" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Tamu</button>
                                        <!-- <a href="{{ route('guest.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambahkan Tamu</a> -->
                                        <a href="{{ route('reservation.index') }}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-2" id="roomType">
                                                <label for="guest_id" class="form-label">Nama Tamu</label>
                                                <select class="form-control select2bs44" name="guest_id" id="guest_id"></select>
                                            </div>

                                            <div class="mb-2" id="floor">
                                                <label for="room_id" class="form-label">Kamar</label>
                                                <select class="form-control select2bs4" name="room_id" id="room_id"></select>
                                            </div>

                                            <div class="mb-2">
                                                <label for="check_out_date" class="form-label">Tgl. Check-Out</label>
                                                <input type="text" id="check_out_date" name="check_out_date" data-target="#checkOutDate" data-toggle="datetimepicker" class="form-control datetimepicker-input" placeholder="Contoh: 2025-05-14">
                                            </div>

                                            <div class="mb-2">
                                                <label for="notes" class="form-label">Catatan  (optional)</label>
                                                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Masukkan catatan jika perlu"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label for="total_amount" class="form-label">Total Harga</label>
                                                <input type="number" name="total_amount" id="total_amount" class="form-control" placeholder="Contoh : 100000" min="5">
                                            </div>

                                            <div class="mb-2">
                                                <label for="payment_method" class="form-label">Metode Bayar</label>
                                                <select class="form-control" name="payment_method" id="payment_method">
                                                    <option value="">-- Select metode pembayaran --</option>
                                                    <option value="cash">Cash</option>
                                                    <option value="transfer">Transfer</option>
                                                </select>
                                            </div>
                                        
                                            <div class="mb-2">
                                                <label for="notes_down_payment" class="form-label">Catatan Pembayaran (optional)</label>
                                                <textarea name="notes_down_payment" id="notes_down_payment" class="form-control" rows="3" placeholder="Masukkan catatan pembayaran jika perlu"></textarea>
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

        <!-- Modal Create Tamu -->
        <div class="modal fade" id="modalCreateGuest" tabindex="-1" aria-labelledby="modalCreateGuestLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                @include('reservation.form_create_tamu')
            </div>
        </div>
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
                        
                        if (errors.check_out_date) {
                            $('#check_out_date').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.check_out_date}</div>`);
                        }

                        if (errors.total_amount) {
                            $('#total_amount').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.total_amount}</div>`);
                        }

                        if (errors.payment_method) {
                            $('#payment_method').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.payment_method}</div>`);
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

        // create btn new guest
        $('#btnCreateGuest').click(function () {
            $('#formCreateGuest')[0].reset();
            $('#formCreateGuest .modal-title').text("Tambah Tamu");
            
            // Hapus pesan error sebelumnya
            $('#formCreateGuest .is-invalid').removeClass('is-invalid');
            $('#formCreateGuest .invalid-feedback').remove();

            $('#formCreateGuest .fa-spinner').hide();
            $('#formCreateGuest .fa-save').show();

            $('#formCreateGuest input[name="_method"]').remove();

            $('#modalCreateGuest').modal('show');
        });

        // form create new guest
        $('#formCreateGuest').on('submit', function (e) {
            e.preventDefault();
            
            // Hapus pesan error sebelumnya
            $('#formCreateGuest .is-invalid').removeClass('is-invalid');
            $('#formCreateGuest .invalid-feedback').remove();

            $('#formCreateGuest .fa-spinner').show();
            $('#formCreateGuest .fa-save').hide();

            const route = $('#formCreateGuest').attr('action');
            const formData = $("#formCreateGuest").serialize();
           
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
                    })
                    $('#modalCreateGuest').modal('hide');
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
                    $('#formCreateGuest .fa-spinner').hide();
                    $('#formCreateGuest .fa-save').show();
                }
            });
        });
    </script>
@endsection