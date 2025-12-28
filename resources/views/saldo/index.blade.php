@extends('welcome')

@section('content')

    <div class="row mt-5 justify-content-center" style="margin-top: 25px;">
        <!-- Total Saldo Card -->
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

        <!-- Filter Kategori -->
        <div class="col-lg-8 col-md-12 col-sm-12" style="margin-left: 20px; margin-right: 20px; margin-top: 20px;  width: calc(100% - 40px);">
            <!-- Dynamic Category Card -->
            <div id="categoryCard" style="display: none;">
                <div class="card shadow-lg" style="border: 2px solid #f0f0f0; box-shadow: 0px 2px 8px rgba(0,0,0,0.05); border-radius: 12px;">
                    <div class="card-body text-md-left" style="border: none; margin-left: 15px;">
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

    <div class="box-header with-border  sm-6 md-8 lg-12" style="margin-bottom: -25px; margin-right: 10px;">
        <div class="form-group col-lg-8 col-md-12 col-sm-12" style="margin-bottom: 20px; margin-top: 10px;">
                <label for="categoryFilter">Filter Berdasarkan Kategori</label>
                <select class="form-control" id="categoryFilter">
                    <option value="">-- Pilih Kategori Saldo --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" data-name="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
        </div>
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