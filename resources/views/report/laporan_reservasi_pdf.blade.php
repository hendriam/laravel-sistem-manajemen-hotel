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
                <th>ID</th>
                <th>No. Reservasi</th>
                <th>Nama Tamu</th>
                <th>Kamar</th>
                <th>Tgl. Check-In</th>
                <th>Tgl. Check-Out</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Diinput oleh</th>
                <th>Tgl. Reservasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->reservation_number ?? '-' }}</td>
                    <td>{{ $item->guest->name ?? '-' }}</td>
                    <td>{{ $item->room->room_number ?? '-' }}</td>
                    <td>{{ $item->check_in_date }}</td>
                    <td>{{ $item->check_out_date ?? '-' }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->notes ?? '-' }}</td>
                    <td>{{ $item->createdBy->name ?? '-' }}</td>
                    <td>{{ $item->created_at ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
