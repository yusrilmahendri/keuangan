@extends('welcome')

@section('content')

<div class="main" style="margin-top: 100px;">
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel">

        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('saldos.store') }}" method="POST" enctype="multipart/form-data">

                        @csrf

                        <div class="form-group @error('amount') has-error @enderror">
                            <label for="amount">Jumlah Saldo</label>
                            <input type="text" class="form-control"
                                name="amount" id="amount" placeholder="masukan Jumlah Saldo"
                                autofocus/>
                        </div>

                        <div class="form-group @error('category_id') has-error @enderror">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 5px;">
                                <label for="category_id" style="margin-bottom: 0;">Kategori Saldo</label>
                                <a href="#" id="toggleCategoryForm" style="font-size: 12px; white-space: nowrap;">+ Tambah Kategori Baru</a>
                            </div>

                            <select class="form-control" name="category_id" id="category_id">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="categoryFormContainer" style="display: none; margin-bottom: 10px; margin-top: 10px;">
                            <div class="form-group">
                                <label for="new_category_name">Nama Kategori Baru</label>
                                <input type="text" class="form-control" id="new_category_name" placeholder="Masukan nama kategori">
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="saveCategoryBtn">Simpan Kategori</button>
                            <button type="button" class="btn btn-default btn-sm" id="cancelCategoryBtn">Batal</button>
                        </div>

                        <div class="form-group @error('description') has-error @enderror">
                            <label for="description">Keterangan / Catatan</label>
                            <input type="text" class="form-control"
                                name="description" placeholder="masukan keterangan/catatan" autofocus/>
                        </div>

                        <div class="form-group @error('periode_saldo') has-error @enderror">
                            <label for="date">Tanggal Masuk Saldo</label>
                            <input type="date" class="form-control"
                                name="periode_saldo" placeholder="masukan tanggal" autofocus/>
                        </div>

                        <div class="form-group @error('nota_image') has-error @enderror">
                            <label for="nota_image">Upload Gambar Nota <span style="font-weight: normal; color: #888; font-size: 90%;">(Opsional)</span></label>
                            <input type="file" class="form-control" name="nota_image" id="nota_image" accept="image/*" />
                            @error('nota_image')
                                <span class="help-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            Simpan Data
                        </button>

                    </form>
                </div>
            </div>
        </div>

      </div>
   </div>
</div>

@endsection

@push('scripts')
<script>
    const amountInput = document.getElementById('amount');
    const submitBtn = document.getElementById('submitBtn');

    amountInput.addEventListener('keyup', function(e) {
        this.value = formatRupiah(this.value);
        validateForm();
    });

    function formatRupiah(angka) {
        angka = angka.replace(/[^,\d]/g, '').toString();

        let split = angka.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah ? 'Rp ' + rupiah : '';
    }

    // Validasi form
    function validateForm() {
        const amount = document.getElementById('amount').value;
        const categoryId = document.getElementById('category_id').value;
        const description = document.querySelector('input[name="description"]').value;
        const periodeSaldo = document.querySelector('input[name="periode_saldo"]').value;
        const categoryFormVisible = document.getElementById('categoryFormContainer').style.display === 'block';

        // Disable jika form kategori sedang ditampilkan
        if (categoryFormVisible) {
            submitBtn.disabled = true;
            return;
        }

        // Disable jika ada field yang kosong
        if (amount && categoryId && description && periodeSaldo) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    // Event listeners untuk semua input
    document.getElementById('category_id').addEventListener('change', validateForm);
    document.querySelector('input[name="description"]').addEventListener('input', validateForm);
    document.querySelector('input[name="periode_saldo"]').addEventListener('change', validateForm);

    // Toggle category form
    document.getElementById('toggleCategoryForm').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('categoryFormContainer').style.display = 'block';
        document.getElementById('category_id').parentElement.style.display = 'none';
        submitBtn.disabled = true; // Disable submit button
    });

    // Cancel category form
    document.getElementById('cancelCategoryBtn').addEventListener('click', function() {
        document.getElementById('categoryFormContainer').style.display = 'none';
        document.getElementById('category_id').parentElement.style.display = 'block';
        document.getElementById('new_category_name').value = '';
        validateForm(); // Re-validate form
    });

    // Save category via AJAX
    document.getElementById('saveCategoryBtn').addEventListener('click', function() {
        const categoryName = document.getElementById('new_category_name').value;

        if (!categoryName) {
            swal('Error!', 'Nama kategori tidak boleh kosong!', 'error');
            return;
        }

        // Kirim AJAX request
        fetch('{{ route("categories.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                name: categoryName
            })
        })
        .then(response => {
            return response.json().then(data => ({
                status: response.status,
                data: data
            }));
        })
        .then(({status, data}) => {
            if (status === 422) {
                // Validation error
                swal('Error!', data.message, 'error');
            } else if (data.success) {
                swal('Berhasil!', data.message, 'success').then(() => {
                    // Redirect ke halaman tambah saldo
                    window.location.href = '{{ route("saldos.create") }}';
                });
            } else {
                swal('Error!', data.message || 'Terjadi kesalahan saat menyimpan kategori', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            swal('Error!', 'Terjadi kesalahan saat menyimpan kategori', 'error');
        });
    });

    // Initial validation
    validateForm();
</script>
@endpush
