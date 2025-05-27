<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            padding: 6px;
            border: 1px solid #000;
            text-align: left;
        }
    </style>
</head>
<body>
    <h3>{{ $title }}</h3>
    <table>
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
			@php $no = 1; @endphp
            @foreach($payments as $item)
                <tr>
                    <td>{{ $no }}</td>
                    <td>{{ $item->payment_date ?? '-' }}</td>
                    <td>{{ $item->guest_name ?? '-' }}</td>
                    <td>{{ $item->room_number ?? '-' }}</td>
                    <td>{{ $item->amount }}</td>
                    <td>{{ ucfirst($item->method) ?? '-' }}</td>
                </tr>
				@php $no++; @endphp
            @endforeach
        </tbody>
    </table>
</body>
</html>
