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
								{{-- filter --}}
                                <form id="filter-form" class="mb-4">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Dari Tanggal</label>
                                            <input type="date" class="form-control" name="start_date" id="filter-start-date">
                                        </div>
										<div class="col-md-2">
                                            <label>Status</label>
											<select class="form-control" name="filter-month" id="filter-month">
												<option value="">-- Select status --</option>
												<option value="01">January</option>
												<option value="02">Februari</option>
												<option value="03">Maret</option>
												<option value="04">April</option>
												<option value="05">Mei</option>
												<option value="06">Juni</option>
												<option value="07">Juli</option>
												<option value="08">Agustus</option>
												<option value="09">September</option>
												<option value="10">November</option>
												<option value="11">Oktober</option>
												<option value="12">Desember</option>
											</select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Status</label>
											<select class="form-control" name="filter-year" id="filter-year">
												<option value="">-- Select status --</option>
												<option value="2025">2025</option>
												<option value="2024">2024</option>
												<option value="2023">2023</option>
											</select>
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
                                                <th>Tanggal</th>
												<th>Nama Tamu</th>
												<th>No. Kamar</th>
												<th>Jumlah Bayar</th>
												<th>Metode</th>
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
                url: '{{ route("report-payment.index") }}',
                type: 'POST',
                    headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
				data: function (d) {
					d.start_date = $('#filter-start-date').val();
					d.month = $('#filter-month').val();
					d.year = $('#filter-year').val();
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
                { data: 'payment_date' },
				{ data: 'guest_name' },
				{ data: 'room_number' },
				{ data: 'amount', className: 'text-end' },
				{ data: 'method' }
            ],
            // order: [ 2, 'desc' ],
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
			let startDate = $('#filter-start-date').val();
			let month = $('#filter-month').val();
			let year = $('#filter-year').val();

			let url = new URL("{{ route('report-payment.export.excel') }}", window.location.origin);
			url.searchParams.append("start_date", startDate);
			url.searchParams.append("month", month);
			url.searchParams.append("year", year);
			window.location.href = url.toString();
		});

		$('#btn-export-pdf').on('click', function () {
			let startDate = $('#filter-start-date').val();
			let month = $('#filter-month').val();
			let year = $('#filter-year').val();

			let url = new URL("{{ route('report-payment.export.pdf') }}", window.location.origin);
			url.searchParams.append("start_date", startDate);
			url.searchParams.append("month", month);
			url.searchParams.append("year", year);

			window.open(url.toString(), '_blank');
		});
    </script>
@endsection