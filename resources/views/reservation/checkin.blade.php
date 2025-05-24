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
                        <h1 class="m-0">Check-in {{ $title }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('reservation.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active">Check-in</li>
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
                        <form action="{{ route('reservation.checkInProcess', $reservation->id) }}" method="post" id="formUpdate" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            @method('PUT')
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Check-in Reservasi {{ $reservation->id }}</h5>
                                    <div class="card-tools">
                                        <a href="{{ route('reservation.index') }}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        @if($reservation->total_paid >= $reservation->room->price * $reservation->duration) 
                                            <span class="badge badge-success">Sudah Lunas</span><br>
                                        @endif
                                        <strong>Total:</strong> Rp{{ number_format($reservation->room->price * $reservation->duration, 0, ',', '.') }} <br>
                                        <strong>Dibayar:</strong> Rp{{ number_format($reservation->total_paid, 0, ',', '.') }}<br>
                                        <strong>Sisa:</strong> Rp{{ number_format(($reservation->room->price * $reservation->duration) - $reservation->total_paid, 0, ',', '.') }}
                                    </div>
                                    @if($reservation->total_paid < $reservation->room->price * $reservation->duration) 
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <label for="amount" class="form-label">Jumlah Pembayaran (Rp)</label>
                                                    <input type="number" id="amount" name="amount" class="form-control" value="{{ ($reservation->room->price * $reservation->duration) - $reservation->total_paid }}" readonly>
                                                </div>

                                                <div class="mb-2">
                                                    <label for="method" class="form-label">Metode Bayar</label>
                                                    <select class="form-control" name="method" id="method">
                                                        <option value="">-- Select metode pembayaran --</option>
                                                        <option value="cash">Cash</option>
                                                        <option value="transfer">Transfer</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <label for="notes" class="form-label">Catatan (optional)</label>
                                                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Masukkan catatan jika perlu"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @if($reservation->total_paid < $reservation->room->price * $reservation->duration) 
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <i class='fas fa-spinner fa-spin' style="display: none"></i> Simpan</button>
                                        <button type="button" class="btn btn-default" id="btnCancel"><i class="fas fa-window-close"></i> Batal</button>
                                    </div>
                                @endif
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

                        if (errors.amount) {
                            $('#amount').addClass('is-invalid').after(`<div class="invalid-feedback">${errors.amount}</div>`);
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
                    $('#formUpdate .fa-spinner').hide();
                    $('#formUpdate .fa-save').show();
                }
            });
        });

    </script>
@endsection