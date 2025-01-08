
@extends('sb-admin-2.layouts.app')

@section('content')

<style>
.card-header {
    background-color: #fff;
}
.mr-0 {
    margin-right: 0;
}
.ml-auto {
    margin-left: auto;
}
.d-block {
    display: block;
}
.button-group a {
    margin-bottom: 10px;
}
</style>

<div class="card shadow mb-4 custom-card-header">
    <div class="card-header py-3">
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Report Pendaftaran</h1>
        <p class="mb-4" style="color:black">
            loremipsum loremipsum loremipsum loremipsum loremipsum loremipsum
        </p>
    </div>

    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="GET" action="/postreport">
                    @csrf
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Tanggal Awal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ isset($start_date) ? $start_date : old('start_date') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ isset($end_date) ? $end_date : old('end_date') }}" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success w-50" name="action" value="download">Download Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@if (session('error'))
        <script>
            alert('{{ session('error') }}');
        </script>
    @endif

@endsection
