@extends('app.layout')

@section('css')
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
                        <form action="{{ route('reservation.payment.store', $reservation->id) }}" method="post" id="formStore" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Form Tambah {{ $title }}</h5>
                                    <div class="card-tools">
                                        <a href="{{ route('reservation.show', $reservation->id) }}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <strong>Total:</strong> Rp{{ number_format($reservation->room->price * $reservation->duration, 0, ',', '.') }} <br>
                                        <strong>Dibayar:</strong> Rp{{ number_format($reservation->total_paid, 0, ',', '.') }}<br>
                                        <strong>Sisa:</strong> Rp{{ number_format(($reservation->room->price * $reservation->duration) - $reservation->total_paid, 0, ',', '.') }}
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label for="amount" class="form-label">Jumlah Pembayaran (Rp)</label>
                                                <input type="number" id="amount" name="amount" class="form-control " placeholder="Contoh: 100000">
                                            </div>

                                            <div class="mb-2">
                                                <label for="payment_date" class="form-label">Tgl. Pembayaran</label>
                                                <input type="text" id="payment_date" name="payment_date" data-target="#payment_date" data-toggle="datetimepicker" class="form-control datetimepicker-input" placeholder="Contoh: 2025-05-14">
                                            </div>

                                            <div class="mb-2">
                                                <label for="method" class="form-label">Metode Bayar</label>
                                                <select class="form-control" name="method" id="method">
                                                    <option value="">-- Select metode pembayaran --</option>
                                                    <option value="cash" {{ ($reservation->method ?? '') == "cash" ? "selected" : "" }}>Cash</option>
                                                    <option value="transfer" {{ ($reservation->method ?? '') == "transfer" ? "selected" : "" }}>Transfer</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label for="notes" class="form-label">Catatan</label>
                                                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Masukkan catatan jika perlu (optional)"></textarea>
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
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/moment/moment.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{ asset('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
    <script>
        //Date picker check_in_date
        $('#payment_date').datetimepicker({
            format: 'YYYY-MM-DD'
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
                       
                        if (errors.amount) {
                            $('#amount').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.amount}</div>`);
                        }
                        
                        if (errors.payment_date) {
                            $('#payment_date').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.payment_date}</div>`);
                        }

                        if (errors.method) {
                            $('#method').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.method}</div>`);
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