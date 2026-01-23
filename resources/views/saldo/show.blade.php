@extends('welcome')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <div class="card shadow-lg border-0" style="border-radius: 18px;">
                <div class="card-header bg-gradient-primary text-white text-center" style="border-radius: 18px 18px 0 0; background: linear-gradient(90deg, #4f8cff 0%, #38b6ff 100%);">
                    <h4 class="mb-0" style="font-weight: 700; letter-spacing: 1px;">Detail Saldo</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6 col-12 mb-3">
                            <div class="d-flex flex-column gap-2">
                                <div><span class="fw-bold text-secondary">Kategori:</span> <span class="text-dark">{{ $Saldo->category ? $Saldo->category->name : '-' }}</span></div>
                                <div><span class="fw-bold text-secondary">Jumlah Saldo:</span> <span class="text-primary" style="font-size:1.2em; font-weight:600;">Rp {{ number_format($Saldo->amount, 0, ',', '.') }}</span></div>
                                <div><span class="fw-bold text-secondary">Keterangan:</span> <span class="text-dark">{{ $Saldo->description ?: '-' }}</span></div>
                                <div><span class="fw-bold text-secondary">Tanggal Masuk:</span> <span class="text-dark">{{ \Carbon\Carbon::parse($Saldo->periode_saldo)->format('d M Y') }}</span></div>
                                <div><span class="fw-bold text-secondary">Waktu Input:</span> <span class="text-dark">{{ $Saldo->created_at->format('d M Y H:i') }}</span></div>
                            </div>
                        </div>
                        @if (!empty($Saldo->nota_image))
                        <div class="col-md-6 col-12 mb-3 text-center">
                            <div class="nota-preview p-2" style="background: #f8f9fa; border-radius: 12px; min-height: 120px;">
                                <span class="fw-bold text-secondary">Nota:</span><br>
                                <a href="{{ asset('storage/' . $Saldo->nota_image) }}" target="_blank" style="display:inline-block; margin-top:8px;">
                                    <img src="{{ asset('storage/' . $Saldo->nota_image) }}" alt="Nota" style="max-width: 100%; max-height: 180px; border-radius: 8px; box-shadow:0 2px 8px rgba(0,0,0,0.07); border:1px solid #e0e0e0; transition:0.2s;">
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('saldos.index') }}" class="btn btn-outline-primary px-4 py-2" style="border-radius: 8px; font-weight: 600;">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


