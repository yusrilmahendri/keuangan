<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Dashboard - {{ date('d M Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
            font-size: 20px;
            color: #667eea;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        .summary-box {
            display: inline-block;
            width: 48%;
            margin: 5px 1%;
            padding: 15px;
            border: 2px solid #f0f0f0;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .summary-box h4 {
            margin: 0 0 10px 0;
            font-size: 12px;
            color: #666;
        }
        .summary-box h3 {
            margin: 0;
            font-size: 18px;
            color: #667eea;
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
        .section-title {
            margin-top: 30px;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #667eea;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DASHBOARD KEUANGAN</h2>
        <p>Periode: {{ date('d F Y') }}</p>
    </div>

    <!-- Summary Boxes -->
    <div style="margin-bottom: 20px;">
        <div class="summary-box">
            <h4>Total Saldo</h4>
            <h3>Rp {{ number_format($totalSaldo, 0, ',', '.') }}</h3>
        </div>
        <div class="summary-box">
            <h4>Total Pengeluaran</h4>
            <h3 style="color: #dc3545;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
        </div>
        <div class="summary-box">
            <h4>Sisa Saldo</h4>
            <h3 style="color: #28a745;">Rp {{ number_format($sisaSaldo, 0, ',', '.') }}</h3>
        </div>
        <div class="summary-box">
            <h4>Jumlah Transaksi</h4>
            <h3>{{ $jumlahTransaksi }}</h3>
        </div>
    </div>

    <!-- Pengeluaran Bulanan -->
    <div class="section-title">Pengeluaran per Bulan</div>
    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="40%">Bulan</th>
                <th width="50%">Total Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($pengeluaranBulanan as $item)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $item['bulan'] }}</td>
                <td>Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Saldo per Kategori -->
    <div class="section-title">Saldo per Kategori</div>
    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="40%">Kategori</th>
                <th width="50%">Total Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($saldoPerKategori as $item)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td>Rp {{ number_format($item['y'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Dicetak pada:</strong> {{ date('d F Y H:i:s') }}</p>
    </div>
</body>
</html>
