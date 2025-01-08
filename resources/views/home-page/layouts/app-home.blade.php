<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Robo Race - Push Bike Competition</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('frontend-vendor/assets/favicon.ico') }}" />
    <!-- Custom styles for this template-->
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
                <a class="navbar-brand" href="{{ url('/') }}">Robo Race - Push Bike Competition</a>
                <!--<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>-->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!--<ul class="navbar-nav ms-auto mb-2 mb-lg-0">-->
                    <!--    <li class="nav-item"><a class="nav-link active" aria-current="page" href="{{ url('/') }}">Home</a></li>-->
                    <!--    <li class="nav-item"><a class="nav-link" href="#!">About</a></li>-->
                    <!--    <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>-->
                    <!--    <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Login</a></li>-->
                    <!--</ul>-->
                </div>
            </div>
        </nav>
        <!-- Page Content-->
        <div class="container px-4 px-lg-5">
            
            @yield('content')

        </div>
        <!-- Footer-->
        <footer class="py-5 bg-dark">
            <div class="container px-4 px-lg-5"><p class="m-0 text-center text-white">Copyright &copy; Roborace 2025</p></div>
        </footer>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('frontend-vendor/bootstrap-5.2.3-dist/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('frontend-vendor/js/scripts.js') }}"></script>

</body>

</html>