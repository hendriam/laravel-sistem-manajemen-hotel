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
                                <!-- /.card-tools -->
                            </div>
                            <div class="card-body">
								{{-- filter by tanggal --}}
                                <form id="filter-form" class="mb-4">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Dari Tanggal</label>
                                            <input type="date" class="form-control" name="start_date" id="filter-start-date">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Sampai Tanggal</label>
                                            <input type="date" class="form-control" name="end_date" id="filter-end-date">
                                        </div>
                                        <div class="col-md-6 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary me-2">Filter</button> &nbsp
                                            <button type="button" class="btn btn-success" id="btn-export-excel">Export Excel</button> &nbsp
                                            <button type="button" class="btn btn-info" id="btn-export-pdf">Export PDF</button> &nbsp
                                            <button type="button" class="btn btn-secondary" id="reset-filters">Reset</button>
                                        </div>
                                    </div>
                                </form>

                                <div class="table-responsive-md">
                                    <table id="data_table" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>No. Reservasi</th>
                                                <th>Nama Tamu</th>
                                                <th>Kamar</th>
                                                <th>Tgl. Check-In</th>
                                                <th>Tgl. Check-Out</th>
                                                <th>Status</th>
                                                <th>Keterangan</th>
                                                <th>Diinput oleh</th>
                                                <th>Tgl.Input</th>
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
                url: '{{ route("report-reservation.index") }}',
                type: 'POST',
                    headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
				data: function (d) {
					d.start_date = $('#filter-start-date').val();
					d.end_date = $('#filter-end-date').val();
				},
				dataSrc: 'data'            },
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
                    data: 'reservation_number',
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
                    orderable: true,
                },
                {
                    data: 'check_out_date',
                    orderable: true,
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
                                return '<span class="badge badge-info">Sudah Check-out</span>';
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
                
            ],
            order: [ 4, 'desc' ],

            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                // tanggal hari ini
                const hariIni = moment();

                // tanggal check-out
                const checkOutDate = moment(aData.check_out_date);

                if (aData.completed) {
                    if (checkOutDate.isSame(hariIni, 'day')) {
                        $('td', nRow).addClass('bg-danger');
                    }
                }
            }

        });
        
		// Submit form filter
		$('#filter-form').on('submit', function (e) {
			e.preventDefault();
			table.ajax.reload();
		});

		// Reset filter
		$('#reset-filters').on('click', function () {
			$('#filter-form')[0].reset();
			table.ajax.reload();
		});

		$('#btn-export-excel').on('click', function () {
			// let status = $('#export-status').val();
			let startDate = $('#filter-start-date').val();
			let endDate = $('#filter-end-date').val();

			let url = new URL("{{ route('report-reservation.export.excel') }}", window.location.origin);
			// url.searchParams.append("status", status);
			url.searchParams.append("start_date", startDate);
			url.searchParams.append("end_date", endDate);
			window.location.href = url.toString();
		});

		$('#btn-export-pdf').on('click', function () {
			// let status = $('#filter-status').val();
			let startDate = $('#filter-start-date').val();
			let endDate = $('#filter-end-date').val();

			let url = new URL("{{ route('report-reservation.export.pdf') }}", window.location.origin);
			// url.searchParams.append("status", status);
			url.searchParams.append("start_date", startDate);
			url.searchParams.append("end_date", endDate);

			window.open(url.toString(), '_blank');
		});
    </script>
@endsection