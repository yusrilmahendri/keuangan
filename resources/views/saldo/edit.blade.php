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
                    <form action="{{ route('saldos.update', $Saldo->id) }}" method="POST" enctype="multipart/form-data">

                      @csrf
                      @method("PUT")

                      <div class="form-group @error('amount') has-error @enderror">
                        <label for="amount">Jumlah Saldo</label>
                        <input type="text"
                          class="form-control"
                          name="amount"
                          value="{{ old('amount', 'Rp ' . number_format($Saldo->amount, 0, ',', '.')) }}"
                          placeholder="masukan Jumlah Saldo"
                         autofocus/>
                    </div>

                    <div class="form-group @error('category_id') has-error @enderror">
                        <label for="category_id">Kategori Saldo</label>
                        <select class="form-control" name="category_id" id="category_id">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $Saldo->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group @error('description') has-error @enderror">
                        <label for="description">Keterangan / Catatan</label>
                        <input type="text"
                          class="form-control"
                          name="description"
                          value="{{ old('description', $Saldo->description) }}"
                          placeholder="masukan keterangan/catatan"  autofocus/>
                    </div>


                    <div class="form-group @error('periode_saldo') has-error @enderror">
                        <label for="date">Tanggal Masuk Saldo</label>
                        <input type="date"
                          class="form-control"
                          value="{{ old('periode_saldo', $Saldo->periode_saldo) }}"
                          name="periode_saldo"
                          placeholder="masukan tanggal"  autofocus/>
                    </div>

                    <div class="form-group @error('nota_image') has-error @enderror">
                        <label for="nota_image">Upload Gambar Nota <span style="font-weight: normal; color: #888; font-size: 90%;">(Opsional)</span></label>
                        @if (!empty($Saldo->nota_image))
                            <div style="margin-bottom:10px;">
                                <a href="{{ asset('storage/' . $Saldo->nota_image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $Saldo->nota_image) }}" alt="Nota" style="max-width:120px;max-height:120px;border:1px solid #eee;">
                                </a>
                            </div>
                        @endif
                        <input type="file" class="form-control" name="nota_image" id="nota_image" accept="image/*" />
                        @error('nota_image')
                            <span class="help-block">{{ $message }}</span>
                        @enderror
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

@endsection()

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
