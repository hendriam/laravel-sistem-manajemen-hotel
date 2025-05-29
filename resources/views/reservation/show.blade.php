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
                        <li class="breadcrumb-item"><a href="{{ route('reservation.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active">Detail</li>
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
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title"><strong>Reservasi atas nama : {{ $reservation->guest->name }} | No. Reservasi : {{ $reservation->reservation_number }} </strong>
                                    @if($reservation->status == 'pending')
                                        <span class="badge badge-secondary">Belum Bayar DP</span>
                                    @endif
                                </h5>
                                <div class="card-tools">
                                    @if($reservation->total_paid < $reservation->room->price * $reservation->duration)
                                        <a href="{{ route('reservation.payment.create', $reservation->id) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Pembayaran</a>
                                    @endif
                                    <button type="button" id="btnPrint" data-id="{{ $reservation->id }}" class="btn btn-dark"><i class="fas fa-print"></i><i class='fas fa-spinner fa-spin' style="display: none"></i> Cetak</button>
                                    <a href="{{ route('reservation.index') }}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali</a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if ($reservation->payments->count() > 0)
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jumlah (Rp)</th>
                                                <th>Metode</th>
                                                <th>Catatan</th>
                                                <th>Dibuat Oleh</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reservation->payments as $payment)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                                    <td>{{ ucfirst($payment->method) }}</td>
                                                    <td>{{ $payment->notes ?? '-' }}</td>
                                                    <td>{{ $payment->createdBy->name ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="alert alert-info mt-2">
                                        <strong>Total:</strong> Rp {{ number_format($reservation->room->price * $reservation->duration, 0, ',', '.') }} <br>
                                        <strong>Dibayar:</strong> Rp {{ number_format($reservation->total_paid, 0, ',', '.') }}<br>
                                        <strong>Sisa:</strong> Rp {{ number_format(($reservation->room->price * $reservation->duration) - $reservation->total_paid, 0, ',', '.') }}
                                    </div>
                                @else
                                    <p>Belum ada pembayaran.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
    </div>
@endsection
<!-- /.content -->

@section('js')
    <script>
        $('#btnPrint').click(function () {
            const id = $(this).data('id');
            
            $('.fa-spinner').show();
            $('.fa-print').hide();

            $.ajax({
                url: '{{ route("reservation.index") }}/invoice/'+id,
                type: 'GET',
                dataType: "html",
                success: function (print) {
                    $('.fa-spinner').hide();
                    $('.fa-print').show();
                    
                    var w = window.open('about:blank','popup','width=950,height=650,titlebar=no,toolbars=no,menubar=no,scrollbars=yes,status=no,resizable=yes,location=no');
                    w.document.open();
                    w.document.write(print);
                    w.document.close();
                },
                error: function (xhr) {
                    let message = 'Gagal konfirmasi';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: "Failed!",
                        text: message,
                        icon: "error",
                    });

                    $('.fa-spinner').hide();
                    $('.fa-print').show();
                }
            });
        });
    </script>
@endsection