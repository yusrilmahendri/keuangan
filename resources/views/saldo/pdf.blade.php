<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Saldo - {{ date('d M Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #667eea;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-row {
            background-color: #e8f4f8 !important;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DATA SALDO</h2>
        <p>Periode: {{ date('d M Y') }}</p>
        <p>Total Saldo: Rp {{ number_format($totalSaldo, 0, ',', '.') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kategori</th>
                <th width="20%">Jumlah Saldo</th>
                <th width="30%">Keterangan</th>
                <th width="15%">Periode Saldo</th>
                <th width="15%">Tanggal Input</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($saldos as $saldo)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $saldo->category ? $saldo->category->name : '-' }}</td>
                <td>Rp {{ number_format($saldo->amount, 0, ',', '.') }}</td>
                <td>{{ $saldo->description }}</td>
                <td>{{ \Carbon\Carbon::parse($saldo->periode_saldo)->format('d M Y') }}</td>
                <td>{{ $saldo->created_at->format('d M Y H:i') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">TOTAL:</td>
                <td colspan="4">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d M Y H:i:s') }}</p>
    </div>
</body>
</html>
