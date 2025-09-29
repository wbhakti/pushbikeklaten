<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Klaten Push Bike Competition 2025</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('frontend-vendor/assets/favicon.ico') }}" />
    <!-- Custom styles for this template-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="{{ asset('frontend-vendor/bootstrap-5.2.3-dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend-vendor/css/styles.css') }}" rel="stylesheet">

    <style>
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: #0d6efd; /* Ganti dengan warna yang kamu inginkan */
            padding: 10px; /* Opsional: Memberikan jarak antara panah dan tepi */
        }
    </style>

</head>

<body>

    <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-5">
                <a class="navbar-brand" href="{{ url('/') }}">Klaten Push Bike Competition 2025</a>
                <!--<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>-->
                <form class="d-flex" action="/cekstatus" method="GET">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="bi-clock me-1"></i>
                        Status
                    </button>
                </form>
            </div>
        </nav>
        <!-- Page Content-->
        <div class="container px-4 px-lg-5">
            
            @yield('content')

        </div>
        <!-- Footer-->
        <footer class="py-5 bg-dark">
            <div class="container px-4 px-lg-5"><p class="m-0 text-center text-white">Copyright &copy; Pushbike Klaten 2025</p></div>
        </footer>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('frontend-vendor/bootstrap-5.2.3-dist/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('frontend-vendor/js/scripts.js') }}"></script>

</body>

</html>