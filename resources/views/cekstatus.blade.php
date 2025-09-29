<!-- resources/views/checkout.blade.php -->
@extends('home-page.layouts.app-home')

@section('content')

<style>
    /* Menjadikan body dan html sebagai flex container */
    html, body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    /* Mengatur agar konten utama mengisi ruang yang tersisa */
    .container.px-4.px-lg-5 {
        flex: 1;
    }

    /* Pastikan footer tetap di bawah */
    footer {
        margin-top: auto;
    }
</style>

    
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Cek Status Transaksi</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="" id="statusForm">
                        <div class="mb-3">
                            <label for="id_transaksi" class="form-label">ID Transaksi</label>
                            <input type="text" name="id_transaksi" id="id_transaksi" class="form-control" placeholder="Masukkan ID Transaksi Anda" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Cek Status</button>
                        </div>
                    </form>                 
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('statusForm').onsubmit = function(event) {
        event.preventDefault();
        var idTransaksi = document.getElementById('id_transaksi').value;
        window.location.href = '/statustransaksi/' + idTransaksi;
    };
</script>

    @if (session('error'))
        <script>
            alert('{{ session('error') }}');
        </script>
    @endif

@endsection
