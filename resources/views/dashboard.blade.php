@extends('welcome')

@section('content')
<div class="container mt-4" style="margin-top: 25px;">

    {{-- Card Summary --}}
    <div class="row g-3" style="margin-top: 10px;">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Total Budget</h6>
                    <h3 class="text-primary fw-bold">
                        Rp {{ number_format($totalBudget, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Total Pengeluaran</h6>
                    <h3 class="text-danger fw-bold">
                        Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Sisa Budget</h6>
                    <h3 class="text-success fw-bold">
                        Rp {{ number_format($sisaBudget, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Jumlah Transaksi</h6>
                    <h3 class="fw-bold">{{ $jumlahTransaksi }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Update Terakhir --}}
    <div class="mt-4">
        <small class="text-muted">
            Update terakhir transaksi:
            <strong>
                {{ $lastTrans ? $lastTrans->created_at->format('d M Y') : '-' }}
            </strong>
        </small>
    </div>

    {{-- Grafik Pengeluaran Bulanan --}}
    <div class="card shadow-sm border-0 mt-4" style="margin-top: 100px;">
        <div class="card-body">
            <h5 class="fw-bold">Grafik Pengeluaran per Bulan</h5>
            <canvas id="pengeluaranChart" height="100"></canvas>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
var ctx = document.getElementById('pengeluaranChart').getContext('2d');

var chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            @foreach($pengeluaranBulanan as $p)
                "{{ $p['bulan'] }}",
            @endforeach
        ],
        datasets: [{
            label: 'Pengeluaran Bulanan',
            data: [
                @foreach($pengeluaranBulanan as $p)
                    {{ $p['total'] }},
                @endforeach
            ],
            borderWidth: 3,
            borderColor: '#0d6efd',
            fill: false,
            tension: 0.3
        }]
    }
});
</script>
@endpush