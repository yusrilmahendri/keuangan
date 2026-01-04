@extends('welcome')

@section('content')

    <div class="row mt-5 justify-content-center" style="margin-top: 40px; padding-left: 15px; padding-right: 15px;">
        <!-- Total Saldo Card -->
        <div class="col-lg-6 col-md-6 col-sm-12" style="margin-left: 20px; margin-right: 20px; margin-top: 20px; width: calc(100% - 40px);">
            <div class="card shadow-lg" style="border: 2px solid #f0f0f0; box-shadow: 0px 2px 8px rgba(0,0,0,0.05); border-radius: 12px;">
                <div class="card-body text-md-left" style="border: none; padding: 25px;">
                    
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

        <!-- Filter Kategori -->
        <div class="col-lg-6 col-md-6 col-sm-12"    style="margin-left: 20px; margin-right: 20px; margin-top: 20px; width: calc(100% - 40px);">
            <!-- Dynamic Category Card -->
            <div id="categoryCard" style="display: none; margin-bottom: 20px; margin-top: 20px;">
                <div class="card shadow-lg" style="border: 2px solid #f0f0f0; box-shadow: 0px 2px 8px rgba(0,0,0,0.05); border-radius: 12px;">
                    <div class="card-body text-md-left" style="border: none; padding: 25px;">
                        <h5 class="card-title text-muted">Kategori: <span id="categoryName" class="font-weight-bold"></span></h5>
                        <h2 class="font-weight-bold text-success">
                            <span id="categorySaldo">Rp 0</span>
                        </h2>
                        <p class="mb-0 text-muted">
                            Total saldo masuk untuk kategori ini
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" style="padding-left: 15px; padding-right: 15px;">
        <div class="row" style="margin-top: 40px; margin-bottom: 20px;">
            <!-- Filter Section -->
            <div class="col-lg-8 col-md-12 col-sm-12" style="margin-bottom: 15px;">
                <label for="categoryFilter" style="font-weight: 600; margin-bottom: 8px;">Filter Berdasarkan Kategori</label>
                <select class="form-control" id="categoryFilter" style="border-radius: 8px;">
                    <option value="">-- Pilih Kategori Saldo --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" data-name="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Buttons Section -->
            <div class="col-lg-4 col-md-12 col-sm-12" style="margin-bottom: 15px;">
                <label style="font-weight: 600; margin-bottom: 8px; display: block;">Aksi</label>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('saldos.export.excel') }}" 
                       class="btn btn-success btn-sm"
                       style="flex: 1; min-width: 110px; margin-bottom: 5px;">
                       <i class="fa fa-file-excel-o"></i> Excel
                    </a>
                    <a href="{{ route('saldos.export.pdf') }}" 
                       class="btn btn-danger btn-sm"
                       target="_blank"
                       style="flex: 1; min-width: 110px; margin-bottom: 5px;">
                       <i class="fa fa-file-pdf-o"></i> PDF
                    </a>
                    <a href="{{ route('saldos.create') }}" 
                       class="btn btn-primary btn-sm"
                       style="flex: 1; min-width: 110px; margin-bottom: 5px;">
                       <i class="fa fa-plus"></i> Tambah
                    </a>
                </div>
            </div>
        </div>
    </div>

     <!-- tabel -->
    <div class="container-fluid" style="padding-left: 15px; padding-right: 15px; margin-top: 20px;">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th>Kategori</th>
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
                    {data: 'category'},
                    {data: 'amount'},
                    {data: 'description'},
                    {data: 'periode_saldo'},
                    {data: 'action'}
                ]
            });

            // Format Rupiah function
            function formatRupiah(angka) {
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Handle category filter
            $('#categoryFilter').on('change', function() {
                const categoryId = $(this).val();
                const categoryName = $(this).find('option:selected').data('name');

                if (categoryId) {
                    // Fetch saldo for selected category
                    fetch(`/api/v1/saldos/category/${categoryId}`)
                        .then(response => response.json())
                        .then(data => {
                            // Update card
                            $('#categoryName').text(categoryName);
                            $('#categorySaldo').text(formatRupiah(data.total));
                            $('#categoryCard').slideDown();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Gagal memuat data kategori');
                        });
                } else {
                    // Hide card when no category selected
                    $('#categoryCard').slideUp();
                }
            });
        });
     </script>
@endpush