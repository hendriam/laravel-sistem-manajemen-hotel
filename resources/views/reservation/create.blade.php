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
                        <h1 class="m-0">Buat {{ $title }}</h1>
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
                                        <button type="button" id="btnCreateGuest" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Tamu</button>
                                        <a href="{{ route('reservation.index') }}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @include('reservation.form', ['data' => null, 'guests' => null, 'rooms' => null, 'isCreate' => true])
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
            <div class="modal-dialog modal-lg">
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
    <script src="{{ asset('assets/dist/js/jquery.maskMoney.min.js') }}"></script>
    <script>

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

        // Tangkap perubahan pada Select2
        $('#room_id').on('change', function() {
            var id = $(this).val();
            $.ajax({
                url: '{{ route("room.index") }}/json/'+id,
                type: 'GET',
                dataType: "json",
                success: function (res) {
                    const number = 400000;
                    const formatter =new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0 
                    })

                    var totalPrice = res.room.price * parseInt($('#number_of_days').val());
                    var roomPrice = res.room.price;

                    $('#total_price').val(formatter.format(totalPrice));
                    $('#room_price').val(formatter.format(roomPrice));
                },
                error: function (xhr) {
                }
            });
        });
        
        //Date picker check_in_date
        $('#check_in_date').datetimepicker({
            format: 'YYYY-MM-DD',
            defaultDate: moment().format('YYYY-MM-DD'),
        });

        //Date picker check_out_date
        $('#check_out_date').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $('#check_in_date').click(function () {
            $('#check_out_date').val("");
            $('#room_id').val("");
            $('#room_id').text("");
            $('#number_of_days').val(1);
            $('#room_price').val(0);
            $('#total_price').val(0);
        })

        $('#check_out_date').click(function () {
            $('#room_id').val("");
            $('#room_id').text("");
            $('#room_price').val(0);
            $('#total_price').val(0);
        })

        // Mengambil nilai saat ada perubahan
        $('#check_out_date').on('change.datetimepicker', function (e) {
            var checkInDate = moment($('#check_in_date').val());
            var checkOutDate = moment($(this).val());
            var jumlahHari = checkOutDate.diff(checkInDate, 'days');
            $('#number_of_days').val(jumlahHari)
        });

        $("#room_price").maskMoney({
            prefix: 'Rp ',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: true,
            precision: 0
        });

        $("#total_price").maskMoney({
            prefix: 'Rp ',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: true,
            precision: 0
        });
        
        $("#mask_down_payment").maskMoney({
            prefix: 'Rp ',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: true,
            precision: 0
        });

        $("#mask_down_payment").keyup(function () {
            var jumlah_bayar = $("#mask_down_payment").val().replace(/[^0-9-]+/g, '');
            $('#down_payment').val(jumlah_bayar)
        });

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

                        if (errors.down_payment) {
                            // $('#down_payment').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.down_payment}</div>`);
                            $('#mask_down_payment').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.down_payment}</div>`);
                        }

                        if (errors.down_payment_method) {
                            $('#down_payment_method').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.down_payment_method}</div>`);
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