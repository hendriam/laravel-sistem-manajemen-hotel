<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @media print {
            body {
                font-family : "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
                width: 90%;
            }
        }
        @page  
        { 
            size: auto;
            margin: 3mm 3mm 3mm 3mm;
        } 
        body {
            font-family : "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            font-family: monospace;
            font-size: 14px;
                width: 90%;
        }
        .header{
            display: flex;
            justify-content: space-between;
        }

        .header > div {
            line-height: 0.1;
        }

        .table-reservation, .table-reservation th, .table-reservation td {
            border: 1px solid black;
            padding: 5px;
        }

        .table-reservation{
            width: 100%;
            border-collapse: collapse;
        }

        #divPrint{
            letter-spacing: -1px;
            line-height: 70%;
        }
        td{
            line-height:10px;
        }
        .title-struk{
            font-size: 18px;
            line-height:14px;
        }
    </style>
</head>
<body>
    <div id="divPrint">
        <div class="header">
            <div>
                <h3>Hotel Sandika</h3>
                <p>Jln. Bandung No. 40</p>
                <p>Telp: (022) 1234567</p>
                <p>Email: hotelsandika@gmail.com</p>
            </div>
            <div>
                <h2>INVOICE</h2>
                <p>No. Invoice: {{ $reservation->reservation_number }}</p>
                <p>Tanggal: {{ \Carbon\Carbon::parse($reservation->created_at)->format('d M Y') }}</p>
            </div>
        </div>
        <div class="content">
            <h3 style="margin-bottom:5px;">Customer</h3>
            <table>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $reservation->guest->name }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $reservation->guest->address }}</td>
                </tr>
                <tr>
                    <td>No. Telepon</td>
                    <td>:</td>
                    <td>{{ $reservation->guest->phone }}</td>
                </tr>
            </table>

            <h3 style="margin-bottom:5px;">Reservasi</h3>
            <table class="table-reservation">
                <tr>
                    <th style="text-align: start">No.</th>
                    <th style="text-align: start">Kamar</th>
                    <th style="text-align: end">Qty</th>
                    <th style="text-align: end">Harga Kamar</th>
                    <th style="text-align: end">Total</th>
                </tr>
                <tr>
                    <td style="text-align: start">1.</td>
                    <td style="text-align: start">Kamar {{ $reservation->room->room_number }} ( Tipe {{ $reservation->room->roomType->name }}, {{ $reservation->room->floor->name }})</td>
                    <td style="text-align: end">{{ $reservation->duration }}</td>
                    <td style="text-align: end">{{ number_format($reservation->room->price, 0, ',', '.') }}</td>
                    <td style="text-align: end">{{ number_format($reservation->room->price * $reservation->duration, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: end; border:0px;"><strong>Total</strong></td>
                    <td colspan="2" style="text-align: end"><strong>{{ number_format($reservation->room->price * $reservation->duration, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: end; border:0px;"><strong>Pajak</strong></td>
                    <td colspan="2" style="text-align: end"><strong>0</strong></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: end; border:0px;"><strong>Total Pembayaran</strong></td>
                    <td colspan="2" style="text-align: end"><strong>{{ number_format($reservation->room->price * $reservation->duration, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>

<script type="text/javascript">
    window.print();
    self.close();
</script>