@extends('app.layout')

@section('css')
    <!-- DataTables -->
    <link rel="stylesheet"  href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">
    <style>
        td.dt-center{
            text-align: center;
        }
    </style>
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
                        <li class="breadcrumb-item active">Index</li>
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
                    <!-- /.col-md-6 -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Daftar {{ $title }}</h5>
                                <div class="card-tools">
                                    <a href="{{ route('reservation.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Reservasi</a>
                                    <a href="{{ route('reservation.direct.create') }}" class="btn btn-warning"><i class="fas fa-plus"></i> Check-in Lansung</a>
                                </div>
                                <!-- /.card-tools -->
                            </div>
                            <div class="card-body">

                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Success!</strong> {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <div class="table-responsive-md">
                                    <table id="data_table" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Tamu</th>
                                                <th>Kamar</th>
                                                <th>Tgl. Check-In</th>
                                                <th>Tgl. Check-Out</th>
                                                <th>Status</th>
                                                <th>Keterangan</th>
                                                <th>Diinput oleh</th>
                                                <th>Tgl.Input</th>
                                                <th style="width: 300px; text-align:center;">#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-md-6 -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
    </div>
@endsection
<!-- /.content -->

@section('js')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/moment/moment.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script>
        const table = $('#data_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("reservation.index") }}',
                type: 'POST',
                    headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataSrc: 'data'
            },
            language: {
                emptyTable: 'Tidak Ada Data Tersedia',
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            responsive: true,
            autoWidth: false,
            ordering: true,
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                    orderable: false,
                },
                {
                    data: 'guest.name',
                    orderable: true,
                },
                {
                    data: 'room.room_number',
                    orderable: false,
                },
                {
                    data: 'check_in_date',
                    orderable: false,
                },
                {
                    data: 'check_out_date',
                    orderable: false,
                },
                {
                    data: 'status',
                    render: function (data, type, row) {
                        switch (data) {
                            case "pending" :
                                return '<span class="badge badge-secondary">Belum Bayar DP</span>';
                                break;
                            case "confirmed" :
                                return '<span class="badge badge-warning">Sudah Bayar DP</span>';
                                break;
                            case "checked_in":
                                return '<span class="badge badge-success">Sudah Check-in</span>';
                                break;
                            case "cancelled":
                                return '<span class="badge badge-danger">Batal</span>';
                                break;
                            case "completed":
                                return '<span class="badge badge-info">Selesai</span>';
                                break;
                        }
                    },
                    orderable: false,
                },
                {
                    data: 'notes',
                    orderable: false,
                },
                {
                    data: 'created_by.name',
                    orderable: false,
                },
                {
                    data: 'created_at',
                    render: function (data, type, row) {
                        return moment(data).format('YYYY-MM-DD HH:mm:ss'); 
                    },
                    orderable: true,
                },
                {
                    data: null,
                    render : function(data, type, row){
                        switch (data.status) {
                            case 'pending':
                                return  '<a href="{{ route("reservation.index") }}/edit/'+data.id+'" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a> &nbsp'+
                                '<button type="button" class="btn btn-sm btn-success btn-confirm" data-id="'+data.id+'"><i class="fas fa-check"></i> Confirm</button> &nbsp' +
                                '<a href="{{ route("reservation.index") }}/show/'+data.id+'" class="btn btn-sm btn-info"><i class="fas fa-wallet"></i> Bayar</a> &nbsp'+
                                '<button type="button" class="btn btn-sm btn-danger btn-cancel" data-id="'+data.id+'"><i class="fas fa-window-close"></i> Batal</button> &nbsp';
                                break;
                            case 'confirmed':
                                return  '<a href="{{ route("reservation.index") }}/edit/'+data.id+'" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a> &nbsp'+
                                // '<button type="button" class="btn btn-sm btn-success btn-checkin" data-id="'+data.id+'"><i class="fas fa-check"></i> Check-in</button> &nbsp' +
                                '<a href="{{ route("reservation.index") }}/check-in/'+data.id+'" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Check-in</a> &nbsp'+
                                '<a href="{{ route("reservation.index") }}/show/'+data.id+'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Detail</a> &nbsp'+
                                '<button type="button" class="btn btn-sm btn-danger btn-cancel" data-id="'+data.id+'"><i class="fas fa-window-close"></i> Batal</button> &nbsp';
                                break;
                            case 'checked_in':
                                return  '<a href="{{ route("reservation.index") }}/edit/'+data.id+'" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a> &nbsp'+
                                '<button type="button" class="btn btn-sm btn-success btn-dark" data-id="'+data.id+'"><i class="fas fa-check"></i> Check-out</button> &nbsp'+
                                '<a href="{{ route("reservation.index") }}/show/'+data.id+'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Detail</a> &nbsp';
                                break;
                            default:
                                return 'Selesai';
                            break;
                        }
                    },
                    orderable: false,
                }
            ],
            order: [ 8, 'desc' ],
            columnDefs: [
                {targets: [9], className: 'dt-center'}
            ],
        });

        $('#data_table').on('click', '.btn-confirm', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: "Anda yakin untuk confirm data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes!",
                cancelButtonText: "Tidak",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("reservation.index") }}/confirm/'+id,
                        type: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            Swal.fire({
                                title: "Success!",
                                text: "Berhasil konfirmasi!",
                                icon: "success",
                            });
                            $('#data_table').DataTable().ajax.reload(null, false); // reload tanpa reset halaman
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
                        }
                    });
                }
            });
        });


        $('#data_table').on('click', '.btn-checkin', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: "Anda yakin untuk check-in data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes!",
                cancelButtonText: "Tidak",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("reservation.index") }}/check-in/'+id,
                        type: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            Swal.fire({
                                title: "Success!",
                                text: "Berhasil check-in!",
                                icon: "success",
                            });
                            $('#data_table').DataTable().ajax.reload(null, false); // reload tanpa reset halaman
                        },
                        error: function (xhr) {
                            let message = 'Gagal checkin-in';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: "Failed!",
                                text: message,
                                icon: "error",
                            });
                        }
                    });
                }
            });
        });

        $('#data_table').on('click', '.btn-checkout', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: "Anda yakin untuk check-out data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes!",
                cancelButtonText: "Tidak",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("reservation.index") }}/check-out/'+id,
                        type: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            Swal.fire({
                                title: "Success!",
                                text: "Berhasil check-out!",
                                icon: "success",
                            });
                            $('#data_table').DataTable().ajax.reload(null, false); // reload tanpa reset halaman
                        },
                        error: function (xhr) {
                            let message = 'Gagal checkin-out';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: "Failed!",
                                text: message,
                                icon: "error",
                            });
                        }
                    });
                }
            });
        });

        $('#data_table').on('click', '.btn-cancel', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: "Anda yakin untuk batalkan data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes!",
                cancelButtonText: "Tidak",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("reservation.index") }}/cancel/'+id,
                        type: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            Swal.fire({
                                title: "Success!",
                                text: "Berhasil batalkan!",
                                icon: "success",
                            });
                            $('#data_table').DataTable().ajax.reload(null, false); // reload tanpa reset halaman
                        },
                        error: function (xhr) {
                            let message = 'Gagal batalkan';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: "Failed!",
                                text: message,
                                icon: "error",
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection