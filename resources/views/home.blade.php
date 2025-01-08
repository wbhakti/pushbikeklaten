<!-- resources/views/home.blade.php -->
@extends('home-page.layouts.app-home')

@section('content')
    <style>
        .custom-border-carousel {
            border: 2px solid #adb5bd;
            /* Warna border biru */
            border-radius: 10px;
            /* Sudut membulat */
            padding: 10px;
            /* Padding untuk jarak */
        }

        .custom-border-card {
            border: 2px solid #adb5bd;
            /* Border lebih tebal */
            border-radius: 10px;
            /* Sudut membulat */
        }

        .carousel-caption {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            background: rgba(0, 0, 0, 0.5);
            /* Semi-transparan hitam */
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .carousel-caption h1,
        .carousel-caption p {
            margin: 20px;
        }

        .section-header {
            border-bottom: 2px solid #adb5bd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>

    <!-- Heading Row as Slider-->
    <div id="carouselExampleCaptions" class="carousel slide my-5 custom-border-carousel" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach ($data as $index => $item)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                    <img src="{{ asset('img/' . $item->img_slider) }}" class="img-fluid d-block w-100" alt="" style="object-fit: cover;">
                    <div class="carousel-caption d-none d-md-block">
                        <h1>{{ $item->title_slider }}</h5>
                            <p>{{ $item->desc_slider }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Call to Action-->
    <!--<div class="card text-white bg-secondary my-5 py-4 text-center">-->
    <!--    <div class="card-body">-->
    <!--        <p class="text-white m-0">This call to action card is a great place to showcase some important information or-->
    <!--            display a clever tagline!</p>-->
    <!--    </div>-->
    <!--</div>-->

    <!-- Blog Section -->
    <section class="container my-5">
        <div class="section-header">
            <h2>Daftar Event Terbaru!</h2>
        </div>

        <!-- Content Row -->
        <div class="row gx-4 gx-lg-5">
            @foreach ($event as $item)
                <div class="col-md-4 mb-5">
                    <div class="card shadow h-100">
                        <img src="{{ asset('img/' . $item->img_event) }}" class="card-img-top"
                            alt="{{ $item->title_event }}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h4 class="card-title">{{ $item->title_event }}</h4>
                            <p class="card-text">{{ Str::limit($item->desc_event, 100) }}</p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <a class="btn btn-outline-primary btn-sm" href="{{ $item->action . '?event=' . $item->id_event }}">Daftar Sekarang</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination Links -->
        {{ $event->links('pagination::bootstrap-5') }}

    </section>
@endsection
