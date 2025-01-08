<!-- resources/views/checkout.blade.php -->
@extends('home-page.layouts.app-home')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-dark shadow-sm">
                    <div class="card-header bg-info text-white text-center">
                        <h4 class="mb-0">Pemesanan Berhasil</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">Terima kasih, {{ $nama_lengkap }}!</h5>
                        <p class="text-muted text-center mb-4">Proses pemesanan anda telah sukses, Segera lakukan pembayaran</p>
                        <p class="text-muted text-center mb-4">Berikut adalah detail pendaftaran dan informasi pembayaran Anda:</p>
                        
                         <!-- Ringkasan Pendaftaran -->
                        <div class="text-center">
                            <div class="mb-4">
                                <h6 class="card-subtitle mb-3 text-primary">Ringkasan Pendaftaran:</h6>
                                <ul class="list-group mb-4 d-inline-block text-start">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="fw-bold">Email:</span>
                                        <span class="ms-2 text-secondary">{{ $email }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="fw-bold">Nomor HP:</span>
                                        <span class="ms-2 text-secondary">{{ $nomor_hp }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="fw-bold">Nama Lengkap:</span>
                                        <span class="ms-2 text-secondary">{{ $nama_lengkap }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="fw-bold">Nama Team/Komunitas:</span>
                                        <span class="ms-2 text-secondary">{{ $nama_team }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="fw-bold">Number Plate:</span>
                                        <span class="ms-2 text-secondary">{{ $number_plate }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="fw-bold">Kategori:</span>
                                        <span class="ms-2 text-secondary">{{ $kategori_id }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="fw-bold">Size Jersey:</span>
                                        <span class="ms-2 text-secondary">{{ $size_slim_suit }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Informasi Pembayaran -->
                        <div class="mb-4">
                            <h4 class="mb-2">Total Pembayaran:</h4>
                            <div class="p-3 border border-success rounded bg-light">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <!-- Icon (Optional) -->
                                        <i class="bi bi-cash-stack fs-3 text-success"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0"><span class="text-success">Rp 200.000</span></h4>
                                        <p class="mb-0 text-muted">Jumlah yang harus dibayar untuk pendaftaran.</p>
                                        <p class="mb-0 text-muted">Contoh 200.023, kode angka belakang sesuai plate number riders "23"</p>
                                        <p class="mb-0 text-muted">Harap Memberi keterangan transfer (nama rider_kategori class)</p>
                                    </div>
                                </div>
                                <!-- Informasi Rekening -->
                                <div class="mt-3">
                                    <h6 class="text-primary">Informasi Rekening:</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between border-0 p-0">
                                            <span class="fw-bold">Bank:</span>
                                            <span class="text-secondary">BCA</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between border-0 p-0">
                                            <span class="fw-bold">Nomor Rekening:</span>
                                            <span class="text-secondary">030-134-4952</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between border-0 p-0">
                                            <span class="fw-bold">Atas Nama:</span>
                                            <span class="text-secondary">Wisnu Bhakti Prasetyo</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <form method="POST" action="/postbuktitransfer" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3 text-start">
                                    <label for="bukti_transfer" class="form-label"><b>Bukti Transfer</b></label>
                                    <input type="file" name="bukti_transfer" id="bukti_transfer" class="form-control" required>
                                    <input type="text" name="nohp" value="{{ $nomor_hp }}" hidden>
                                    <input type="text" name="email" value="{{ $email }}" hidden>
                                </div>
                                <button type="submit" class="btn btn-primary">Kirim Bukti Transfer</button>
                            </form>
                        </div>                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>

    @if (session('error'))
        <script>
            alert('{{ session('error') }}');
        </script>
    @endif

@endsection
