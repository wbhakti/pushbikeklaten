@extends('sb-admin-2.layouts.app')

@section('content')


<!-- CSS custom -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">

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

<!-- DataTales Example -->
<div class="card shadow mb-4 custom-card-header">
    <div class="card-header py-3">
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Data Master Event</h1>
        <p class="mb-4" style="color:black">
            loremipsum loremipsum loremipsum loremipsum loremipsum loremipsum
        </p>
        @if(isset($error))
        <div align="center">
            <text style="color:red">{{ $error }}</text>
        </div>
        @endif
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>rowid</th>
                        <th>Title Event</th>
                        <th>Desc Event</th>
                        <th>Image Event</th>
                        <th>Action URL</th>
                        <th>Status Event</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($event) && !$event->isEmpty())
                    @foreach ($event as $item)
                    <tr>
                        <td>{{ $item->rowid }}</td>
                        <td>{{ $item->title_event }}</td>
                        <td>{{ $item->desc_event }}</td>
                        <td>
                            <img src="{{ asset('img/' . $item->img_event) }}" alt="Thumbnail" style="max-width: 100px; max-height: 100px;">
                        </td>
                        <td>{{ $item->action }}</td>
                        <td>{{ $item->is_active }}</td>
                        <td>
                        <div class="button-group">
                            <form method="POST" action="/postevent">
                                @csrf
                                <input type="hidden" name="rowid" value="{{ $item->rowid }}">
                                <input type="hidden" name="old_img_event" value="{{ $item->img_event }}">
                                <div class="button-group">
                                    <button type="button" class="btn btn-primary mb-2 btn-edit"
                                        data-rowid="{{ $item->rowid }}"
                                        data-title="{{ $item->title_event }}"
                                        data-desc="{{ $item->desc_event }}"
                                        data-action="{{ $item->action }}"
                                        data-img="{{ $item->img_event }}"
                                        data-isactive="{{ $item->is_active }}">Edit</button>
                                    <button type="submit" name="proses" value="delete" class="btn btn-danger mb-2">Delete</button>
                                </div>
                            </form>
                        </div>
                        </td>
                    </tr>
                    @endforeach
                    @else

                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <hr>
    <div align="center">
        <button id="toggleButton" class="btn btn-success">Add New Event</button>
    </div>
    <br>
    <div id="myForm" style="display: none;">
        <div class="col-xl-8 col-lg-7 mx-auto">
            <!-- Project Card Example -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="POST" action="/postevent" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="form-group col-sm-6">
                                <label for="title_event"><b>Title Event</b></label>
                                <input type="text" name="title_event" class="form-control" required />
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="action_event"><b>Action URL</b></label>
                                <input type="text" name="action_event" class="form-control" required />
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="form-group col-sm-6">
                                <label for="desc_event"><b>Desc Event</b></label>
                                <textarea name="desc_event" class="form-control" rows="4" required></textarea>
                            </div>                            
                            <div class="form-group col-sm-6">
                                <label for="img_event"><b>Image Event (900x400)</b></label>
                                <input type="file" name="img_event" class="form-control" accept="image/*" />
                            </div>                            
                        </div>
                        <div class="row justify-content-left">
                            <div class="form-group col-sm-6">
                                <label><b>Form Input</b></label><br>
                                <div class="d-flex flex-wrap" role="group" aria-label="Kategori">
                                    @foreach ($listform as $item)
                                    <button type="button" class="btn btn-outline-primary m-1" onclick="toggleCategory('{{ $item->nama_form }}','{{ $item->desc_form }}')">{{ $item->desc_form }}</button>
                                    @endforeach
                                </div>
                                <!-- Hidden input to store the selected categories -->
                                <input type="hidden" id="category_event" name="category_event" required>
                            </div>
                        </div>
                        <br />
                        <div align="center">
                            <button type="submit" name="proses" value="save" class="btn btn-success">Save Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="/postevent" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="proses" value="edit">
                        <input type="hidden" name="rowid" id="editRowid">
                        <input type="hidden" name="old_img_event" id="editOldImgEvent">

                        <div class="form-group">
                            <label for="editIsActive"><b>Is Active</b></label>
                            <select name="is_active" id="editIsActive" class="form-control">
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editTitleEvent"><b>Title Event</b></label>
                            <input type="text" name="title_event" id="editTitleEvent" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label for="editActionEvent"><b>Action URL</b></label>
                            <input type="text" name="action_event" id="editActionEvent" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label for="editDescEvent"><b>Desc Event</b></label>
                            <textarea name="desc_event" id="editDescEvent" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="editImgEvent"><b>Image Event</b></label>
                            <input type="file" name="img_event" id="editImgEvent" class="form-control" accept="image/*" />
                        </div>
                        <div class="form-group">
                            <label><b>Current Image</b></label>
                            <img id="currentImage" src="" alt="Current Image" style="max-width: 100px; max-height: 100px;">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@if(session('success'))
<script>
    alert('{{ session('success') }}');
</script>
@endif

<!-- Page level plugins -->
<script src="{{ asset('vendor/jquery/jquery-3.3.1.min.js')}}"></script>
<script src="{{ asset('vendor/jquery/jquery.validate.min.js')}}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<script>
$(document).ready(function() {
    $('#dataTable').dataTable({
        "lengthMenu": [10, 20, 50, 100],
        "pageLength": 5,
        searching: true
    });
});
</script>

<script>
    $(document).ready(function() {
        $("#toggleButton").click(function() {
            $("#myForm").toggle();
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Edit button click event
        $('.btn-edit').on('click', function() {
            var rowid = $(this).data('rowid');
            var title = $(this).data('title');
            var desc = $(this).data('desc');
            var action = $(this).data('action');
            var img = $(this).data('img');
            var isactive = $(this).data('isactive');

            // Set modal data
            $('#editRowid').val(rowid);
            $('#editTitleEvent').val(title);
            $('#editDescEvent').val(desc);
            $('#editActionEvent').val(action);
            $('#editOldImgEvent').val(img);
            $('#editIsActive').val(isactive);
            $('#currentImage').attr('src', "{{ asset('img/') }}/" + img);

            // Show modal
            $('#editModal').modal('show');
        });
    });
</script>

<script>
    let selectedCategories = [];

    function toggleCategory(category, categoryText) {
        const categoryInput = document.getElementById('category_event');

        // Toggle the selected category in the array
        const categoryIndex = selectedCategories.findIndex(item => item.category === category);
        if (categoryIndex > -1) {
            // If category is already selected, remove it from the array
            selectedCategories.splice(categoryIndex, 1);
        } else {
            // If category is not selected, add it to the array
            selectedCategories.push({ category, categoryText });
        }

        // Update the hidden input value with the selected categories (optional: store just text or both category and text)
        categoryInput.value = selectedCategories.map(item => `${item.category}|${item.categoryText}`).join(', ');

        // Toggle button appearance
        const button = event.target;
        button.classList.toggle('btn-primary');
        button.classList.toggle('btn-outline-primary');
    }
</script>

@endsection