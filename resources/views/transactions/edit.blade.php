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

                                    <form action="{{ route('transactions.update', $transaction->id) }}" 
                                          method="POST" enctype="multipart/form-data">
                                      
                                        @csrf
                                        @method('PUT')

                                        <div class="form-group">
                                            <label>Kategori Transaksi</label>
                                            <select class="form-control" name="category_id" required>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" 
                                                        {{ $category->id == $transaction->category_id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Total Transaksi</label>
                                            <input type="text" 
                                                class="form-control"
                                                name="total"
                                                id="total_transaksi"
                                                value="Rp {{ number_format($transaction->amount, 0, ',', '.') }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Keterangan / Catatan</label>
                                            <input type="text" 
                                                class="form-control"
                                                name="description" 
                                                value="{{ $transaction->description }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal / Waktu Transaksi</label>
                                            <input type="date" 
                                                class="form-control"
                                                name="date" 
                                                value="{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d') }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Upload Nota (Opsional)</label>
                                            <input type="file" class="form-control" name="nota">
                                        </div>

                                        <hr>

                                        <h4>Detail Barang</h4>

                                        <div id="detail-container">

                                            @foreach($transaction->items as $i => $item)
                                            <div class="row item-row mb-2" style="margin-top:15px;">

                                                <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">

                                                <div class="col-md-3">
                                                    <input type="text" name="items[{{ $i }}][name]" 
                                                        value="{{ $item->name }}"
                                                        class="form-control item-nama"
                                                        placeholder="Nama Barang">
                                                </div>

                                                <div class="col-md-2">
                                                    <input type="number" name="items[{{ $i }}][quantity]"
                                                        value="{{ $item->quantity }}"
                                                        class="form-control item-jumlah"
                                                        placeholder="Jumlah" min="1">
                                                </div>

                                                <div class="col-md-2">
                                                    <input type="text" name="items[{{ $i }}][price]"
                                                        value="Rp {{ number_format($item->price, 0, ',', '.') }}"
                                                        class="form-control item-harga"
                                                        placeholder="Harga">
                                                </div>

                                                <div class="col-md-3">
                                                    <input type="text" name="items[{{ $i }}][note]"
                                                        value="{{ $item->note }}"
                                                        class="form-control"
                                                        placeholder="Keterangan (opsional)">
                                                </div>

                                                <div class="col-md-2 d-flex" style="margin-top:5px;">
                                                    <button type="button" class="btn btn-danger btn-remove ml-1">
                                                        X
                                                    </button>
                                                    <button type="button" class="btn btn-success btn-add ml-1">
                                                        +
                                                    </button>
                                                </div>

                                            </div>
                                            @endforeach

                                        </div>

                                        <hr>

                                        <button type="submit" class="btn btn-primary">Update Data</button>

                                    </form>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
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

        return rupiah ? 'Rp ' + rupiah : '';
    }

    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('.item-harga').forEach(input => {
            let angka = parseFloat(input.value.replace(/[^0-9]/g, '')) || 0;
            total += angka;
        });
        document.getElementById('total_transaksi').value = formatRupiah(total.toString());
    }

    let itemIndex = {{ count($transaction->items) }};

    function generateRow() {
        return `
        <div class="row item-row mb-2" style="margin-top:15px;">
            <div class="col-md-3">
                <input type="text" name="items[${itemIndex}][name]" class="form-control" placeholder="Nama Barang">
            </div>

            <div class="col-md-2">
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Jumlah" min="1">
            </div>

            <div class="col-md-2">
                <input type="text" name="items[${itemIndex}][price]" class="form-control item-harga" placeholder="Harga">
            </div>

            <div class="col-md-3">
                <input type="text" name="items[${itemIndex}][note]" class="form-control" placeholder="Keterangan">
            </div>

            <div class="col-md-2 d-flex" style="margin-top:5px;">
                <button type="button" class="btn btn-danger btn-remove ml-1">X</button>
                <button type="button" class="btn btn-success btn-add ml-1">+</button>
            </div>
        </div>`;
    }

    document.getElementById('detail-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-add')) {
            itemIndex++;
            document.getElementById('detail-container').insertAdjacentHTML('beforeend', generateRow());
        }

        if (e.target.classList.contains('btn-remove')) {
            if (document.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
                hitungTotal();
            }
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('item-harga')) {
            e.target.value = formatRupiah(e.target.value);
            hitungTotal();
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.item-harga').forEach(input => {
            let angka = input.value.replace(/[^0-9]/g, '');
            input.value = formatRupiah(angka);
        });
        hitungTotal();
    });
</script>
@endpush