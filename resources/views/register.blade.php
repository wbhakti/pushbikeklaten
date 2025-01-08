<!-- resources/views/home.blade.php -->
@extends('home-page.layouts.app-home')

@section('content')
    <style>
        .custom-border-card {
            border: 2px solid #adb5bd;
            /* Border lebih tebal */
            border-radius: 10px;
            /* Sudut membulat */
        }
    </style>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if ($dataEvent)
                    <div class="card custom-border-card">
                        <!-- Image di atas form -->
                        <img src="{{ asset('img/' . $dataEvent->img_event) }}" class="card-img-top img-fluid" alt="Image Card" style="object-fit: cover;">
                        <div class="card-body">
                            <h3 class="card-title text-center mb-4">Pendaftaran {{ $dataEvent->title_event }}</h3>
                            <!-- Deskripsi di bawah form -->
                            <div class="mb-4">
                                <div class="alert alert-primary" role="alert">
                                    <p>{{ $dataEvent->desc_event }}</p>
                                </div>
                            </div>
                            <form method="POST" action="/postregister" enctype="multipart/form-data">
                                @csrf
                                <div class="row justify-content-center">
                                    <div class="col-md-8 mx-auto">
                                        <div class="mb-3">
                                            <label for="kategori" class="form-label">Kategori</label>
                                            <select class="form-select" id="kategori" name="kategori" required>
                                                <option value="" disabled selected>Pilih Kategori</option>
                                                @foreach ($listKategori as $kategori)
                                                    <option value="{{ $kategori->id_kategori }}|{{ $kategori->nama_kategori }}">
                                                        {{ $kategori->nama_kategori }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @foreach ($inputForm as $field)
                                            <div class="mb-3">
                                                <label for="{{ $field['name'] }}" class="form-label">{{ $field['label'] }}</label>
                                                @if ($field['name'] === 'alamat_domisili')
                                                    <textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}" rows="3" placeholder="Masukkan Alamat Domisili" required></textarea>
                                                @elseif ($field['name'] === 'foto_akta_kia')
                                                    <input type="file" class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}" required>
                                                @elseif ($field['name'] === 'number_plate')
                                                    <input class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}" 
                                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                        type = "number"
                                                        maxlength = "3"/>
                                                    <!--<input type="number" class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}" max="999" required>-->
                                                @elseif ($field['name'] === 'nomor_hp')
                                                    <input type="number" class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}" required>
                                                @elseif ($field['name'] === 'size_slim_suit')
                                                    <select class="form-select" id="{{ $field['name'] }}" name="{{ $field['name'] }}" required>
                                                        <option value="" disabled selected>Pilih Ukuran</option>
                                                        <option value="S">S</option>
                                                        <option value="M">M</option>
                                                        <option value="L">L</option>
                                                        <option value="XL">XL</option>
                                                        <option value="2XL">2XL</option>
                                                        <option value="3XL">3XL</option>
                                                        <option value="4XL">4XL</option>
                                                    </select>
                                                @else
                                                    <input type="text" name="{{ $field['name'] }}" id="{{ $field['name'] }}" class="form-control" required />
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <br>
                                <button type="submit" class="btn btn-primary w-100">Daftar</button>
                            </form>
                        </div>
                        <div class="card-footer text-center">
                            <!-- <small>Sudah punya akun? <a href="#">Login di sini</a></small> -->
                        </div>
                    </div>
                @else
                    <p>Event tidak ditemukan.</p>
                @endif
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
