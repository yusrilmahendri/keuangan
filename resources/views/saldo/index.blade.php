@extends('welcome')

@section('content')

    <div class="row mt-5 justify-content-center" style="margin-top: 25px;">
        <div class="col-lg-4 col-md-6 col-sm-12" style="margin-left: 20px; margin-right: 20px; margin-top: 20px; width: calc(100% - 40px);">
            <div class="card shadow-lg sm-6 md-8 lg-12" style="border: 2px solid #f0f0f0; box-shadow: 0px 2px 8px rgba(0,0,0,0.05); border-radius: 12px; ">
                <div class="card-body text-md-left" style="border: none; margin-left: 15px;">
                    
                    <h5 class="card-title text-muted">Total Saldo Yang Masuk</h5>

                    <h2 class="font-weight-bold text-primary">
                        Rp {{ number_format($total_saldo, 0, ',', '.') }}
                    </h2>

                    <p class="mb-0 text-muted">
                        Update terakhir:  
                        {{ $updated_saldo ? \Carbon\Carbon::parse($updated_saldo->periode_saldo)->format('d M Y') : '-' }}
                    </p>
                                        
                </div>
            </div>
        </div>
    </div>

    <div class="box-header with-border  sm-6 md-8 lg-12" style="margin-bottom: -25px; margin-right: 10px;">
        <h3 class="box-title">.</h3>
        <div class="box-tools pull-right">
            <a href="{{ route('saldos.create') }}" 
               class="btn btn-primary btn-sm">
               <i class="fa fa-plus"></i> Tambah Saldo
            </a>
        </div>
    </div>

     <!-- tabel -->
    <div class="box-body" style="margin-top: 100px;">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th>Saldo</th>
                        <th>Keterangan</th>
                        <th>Tanggal & Waktu Input</th>
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
                ajax: "{{ route('saldos.data') }}",
                columns: [
                    {data: 'amount'},
                    {data: 'description'},
                    {data: 'periode_saldo'},
                    {data: 'action'}
                ]
            });
        });
     </script>
@endpush