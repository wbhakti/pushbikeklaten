<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 - Not Found</title>
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Begin Page Content -->
 <div class="container-fluid">

    <!-- 404 Error Text -->
    <div class="text-center">
        <div class="error mx-auto" data-text="404">404</div>
        <p class="lead text-gray-800 mb-5">Page Not Found</p>
        <p class="text-gray-500 mb-0">It looks like you found a glitch in the matrix...</p>
        {{-- <a href="{{ url()->previous() }}">&larr; Go to Previous Page</a> --}}
        <a href="{{ url('/') }}">&larr; Go to Home Page</a>
    </div>

</div>
<!-- /.container-fluid -->
</body>
</html>