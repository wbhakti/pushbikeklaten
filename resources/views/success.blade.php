@extends('home-page.layouts.app-home')

@section('content')

<style>
    footer {
    position: absolute;
    bottom: 0;
    width: 100%;
}

</style>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success shadow-lg">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">Upload Berhasil</h4>
                </div>
                <div class="card-body text-center">
                    <h5 class="card-title text-success">Terima kasih!</h5>
                    <p class="text-muted">Bukti transfer Anda telah berhasil diunggah.</p>
                    <p class="fw-bold">Harap menunggu proses verifikasi selama 2x24 jam.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
