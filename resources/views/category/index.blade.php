@extends('welcome')

@section('content')

    <div class="box-header with-border sm-6 md-8 lg-12" style="margin-top: 100px; margin-bottom: -25px; margin-right: 10px;">
        <h3 class="box-title">Daftar Kategori Saldo</h3>
        <div class="box-tools pull-right">
            <a href="{{ route('categories.create') }}" 
               class="btn btn-primary btn-sm">
               <i class="fa fa-plus"></i> Tambah Kategori
            </a>
        </div>
    </div>

     <!-- tabel -->
    <div class="box-body" style="margin-top: 100px;">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
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
                ajax: "{{ route('categories.data') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });
     </script>
@endpush
