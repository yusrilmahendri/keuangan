<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Transaksi - {{ date('d M Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        table td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
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
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DATA TRANSAKSI</h2>
        <p>Periode: {{ date('d M Y') }}</p>
        <p>Total Transaksi: Rp {{ number_format($totalTransaksi, 0, ',', '.') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="10%">Kode</th>
                <th width="12%">Kategori</th>
                <th width="15%">Total</th>
                <th width="20%">Keterangan</th>
                <th width="20%">Item Transaksi</th>
                <th width="10%">Tgl Transaksi</th>
                <th width="10%">Tgl Input</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $no++ }}</td>
                <td>TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $transaction->category ? $transaction->category->name : '-' }}</td>
                <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                <td>{{ $transaction->description ?: '-' }}</td>
                <td>
                    @if($transaction->items->count() > 0)
                        @foreach($transaction->items as $item)
                            {{ $item->name }} ({{ $item->quantity }}){{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</td>
                <td>{{ $transaction->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">TOTAL:</td>
                <td colspan="5">Rp {{ number_format($totalTransaksi, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d M Y H:i:s') }}</p>
    </div>
</body>
</html>
