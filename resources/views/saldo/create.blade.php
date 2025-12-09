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
                    <form action="{{ route('saldos.store') }}" method="POST">
                        @csrf

                        <div class="form-group @error('amount') has-error @enderror">
                            <label for="amount">Jumlah Saldo</label>
                            <input type="text" class="form-control"
                                name="amount" id="amount" placeholder="masukan Jumlah Saldo"
                                autofocus/>
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

                        <button type="submit" class="btn btn-primary">
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

    amountInput.addEventListener('keyup', function(e) {
        this.value = formatRupiah(this.value);
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
</script>
@endpush