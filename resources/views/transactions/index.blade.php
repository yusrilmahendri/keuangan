@extends('welcome')

@section('content')

      <div class="row mt-5 justify-content-center" style="margin-top: 40px; padding-left: 15px; padding-right: 15px;">
        <div class="col-lg-6 col-md-6 col-sm-12" style="margin-bottom: 20px; padding-left: 10px; padding-right: 10px; margin-top: 20px;">
            <div class="card shadow-lg" style="border: 2px solid #f0f0f0; box-shadow: 0px 2px 8px rgba(0,0,0,0.05); border-radius: 12px;">
                <div class="card-body text-md-left" style="border: none; padding: 25px;">

                    <h5 class="card-title text-muted">Total Sisa Saldo</h5>

                    <h2 class="font-weight-bold text-primary">
                        Rp {{ number_format($sisaSaldo, 0, ',', '.') }}
                    </h2>

                   <p class="mb-0 text-muted">
                        Update terakhir: {{ $dateTransaksi ? \Carbon\Carbon::parse($dateTransaksi->transaction_date)->format('d M Y') : '-' }}
                    </p>
                </div>
            </div>
        </div>

           <div class="col-lg-6 col-md-6 col-sm-12" style="margin-bottom: 20px; padding-left: 10px; padding-right: 10px;  margin-top: 20px;">
            <div class="card shadow-lg" style="border: 2px solid #f0f0f0; box-shadow: 0px 2px 8px rgba(0,0,0,0.05); border-radius: 12px;">
                <div class="card-body text-md-left" style="border: none; padding: 25px;">

                    <h5 class="card-title text-muted">Total Transaksi</h5>

                    <h2 class="font-weight-bold text-primary">
                        Rp {{ number_format($totalSemua, 0, ',', '.') }}
                    </h2>

                    <p class="mb-0 text-muted">
                        Update terakhir:
                        {{ $dateTransaksi ? \Carbon\Carbon::parse($dateTransaksi->transaction_date)->format('d M Y') : '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" style="padding-left: 15px; padding-right: 15px; margin-top: 20px;">
        <div class="row">
            <div class="col-12">
                <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; margin-bottom: 20px;">
                    <a href="{{ route('transactions.export.excel') }}"
                       class="btn btn-success btn-sm"
                       style="min-width: 110px; margin-bottom: 5px;">
                       <i class="fa fa-file-excel-o"></i> Excel
                    </a>
                    <a href="{{ route('transactions.export.pdf') }}"
                       class="btn btn-danger btn-sm"
                       target="_blank"
                       style="min-width: 110px; margin-bottom: 5px;">
                       <i class="fa fa-file-pdf-o"></i> PDF
                    </a>
                    <a href="{{ route('transactions.create') }}"
                       class="btn btn-primary btn-sm"
                       style="min-width: 110px; margin-bottom: 5px;">
                       <i class="fa fa-plus"></i> Tambah Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>


     <!-- tabel -->
    <div class="container-fluid" style="padding-left: 15px; padding-right: 15px;">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Kategori</th>
                        <th>Total Transaksi</th>
                        <th>Keterangan</th>
                        <th>Item Transaksi</th>
                        <th>Tanggal & Waktu Transaksi</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

   <!-- trigger pada menghapus data pengguna -->
    <form action="" method="post" id="deleteForm">
             @csrf
             @method("DELETE")
             <input type="submit" value="Hapus"
             style="display: none ">
    </form>
@endsection()

@push('scripts')
     <!-- boostrap notify -->
     <script src="{{ asset('js/bs-notify.min.js') }}">
     </script>

    <!-- alertnya boostrap notify -->
    @include('templates.partials.alerts')


 <script>
        $(function(){
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('transactions.data') }}",
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'amount'},
                    {data: 'description'},
                    {data: 'keterangan_detail'},
                    {data: 'transaction_date'},
                    {data: 'action'}
                ]
            });
        });
     </script>
@endpush
