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
                        <li class="breadcrumb-item"><a href="{{ route('guest.index') }}">{{ $title }}</a></li>
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
                                    <a href="{{ route('guest.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a>
                                </div>
                                <!-- /.card-tools -->
                            </div>
                            <div class="card-body">
                                <table id="data_table" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama</th>
                                            <th>Identitas</th>
                                            <th>Nomor Identitas</th>
                                            <th>Nomor Telpon/Hp</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th>Diinput oleh</th>
                                            <th>Tgl.Input</th>
                                            <th style="width: 200px; text-align:center;">#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
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
                url: '{{ route("guest.index") }}',
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
                    data: 'name',
                    orderable: true,
                },
                {
                    data: 'identity_type',
                    orderable: false,
                },
                {
                    data: 'identity_number',
                    orderable: false,
                },
                {
                    data: 'phone',
                    orderable: false,
                },
                {
                    data: 'email',
                    orderable: false,
                },
                {
                    data: 'address',
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
                        return  '<a href="{{ route("guest.index") }}/edit/'+data.id+'" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a> ';
                        // '<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="'+data.id+'"><i class="fas fa-trash"></i> Hapus</button> ';
                    },
                    orderable: false,
                }
            ],
            order: [ 8, 'desc' ],
            columnDefs: [
                {targets: [9], className: 'dt-center'}
            ],
        });

        $('#data_table').on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: "Anda yakin untuk data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, hapus!",
                cancelButtonText: "Tidak",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("guest.index") }}/'+id,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Data berhasil dihapus!",
                                icon: "success",
                            });
                            $('#data_table').DataTable().ajax.reload(null, false); // reload tanpa reset halaman
                        },
                        error: function (xhr) {
                            let message = 'Gagal menghapus data';
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