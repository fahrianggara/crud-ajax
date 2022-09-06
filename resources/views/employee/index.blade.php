@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="d-inline">Employee Table</h4>
                        <a href="#" class="btn btn-info float-right" data-toggle="modal" data-target="#modalCreate">
                            <i class="uil uil-plus"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div id="show_all_employe" class="table-responsive">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create -->
    <div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="modalCreate"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreate">Add Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="createDataForm" action="" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">

                        <div class="form-group mb-3">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name">
                            <span class="text-danger error-text name_error"></span>
                        </div>
                        <div class="form-group mb-3">
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone">
                            <span class="text-danger error-text phone_error"></span>
                        </div>
                        <div class="custom-file">
                            <input type="file" name="image" id="image" class="custom-file-input">
                            <label class="custom-file-label" for="image">Choose Image</label>
                            <span class="text-danger error-text image_error"></span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                        <button type="submit" class="btn btn-success buttonInsert">ADD</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEdit" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEdit">Edit Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editDataForm" action="{{ route('employee.store') }}" method="POST" enctype="multipart/form-data"
                    autocomplete="off">
                    @method('PUT')
                    @csrf

                    <div class="modal-body">
                        <input type="hidden" name="old_name">
                        <input type="hidden" name="emp_id" id="emp_id">

                        <div class="form-group mb-3">
                            <input type="text" name="name" id="edit_name" class="form-control" placeholder="Name">
                            <span class="text-danger error-text name_error_edit"></span>
                        </div>
                        <div class="form-group mb-3">
                            <input type="text" name="phone" id="edit_phone" class="form-control" placeholder="Phone">
                            <span class="text-danger error-text phone_error_edit"></span>
                        </div>
                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input">
                            <label class="custom-file-label" id="edit_image" for="image"></label>
                            <span class="text-danger error-text image_error_edit"></span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                        <button type="submit" class="btn btn-warning buttonEdit">UPDATE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Create -->
    <div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="modalDelete"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <form id="deleteDataForm" action="" method="DELETE">

                    <div class="modal-body">
                        <input type="hidden" name="emp_id" id="del_emp_id">
                        <p style="font-size: 18px; font-weight: 600;display: inline;">Are you sure? want to delete this
                            data?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                        <button type="submit" id="buttonDelete" class="btn btn-danger buttonDelete">DELETE</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // mengaktifkan csrf form kalo pakai ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // MENAMPILAKAN EMPLOYEE DATA DARI EMPLOYECONTROLLER
            function fetchEmployee() {
                $.ajax({
                    url: "{{ route('employee.table') }}",
                    method: "GET",

                    success: function(response) {
                        $("#show_all_employe").html(response);
                        $("table").DataTable();
                    }
                });
            }

            // MENJALANKAN FUNGSI AGAR MUNCUL DATANYA
            fetchEmployee();

            // SHOWING MODAL DELETE PER ID
            $(document).on('click', '.del_btn', function(e) {
                e.preventDefault();

                let emp_id = $(this).val();
                $("#modalDelete").modal('show');
                $("#del_emp_id").val(emp_id);

            });

            // PROSES DELETING DATA
            $("#deleteDataForm").on("submit", function(e) {
                e.preventDefault();

                let idDataEmp = $('#del_emp_id').val();

                $.ajax({
                    url: "{{ url('delete-employee') }}/" + idDataEmp,
                    method: $(this).attr('method'),
                    dataType: "json",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": idDataEmp
                    },
                    beforeSend: function() {
                        $('.buttonDelete').attr('disable', 'disable');
                        $('.buttonDelete').html('<i class="fa fa-spin fa-spinner"></i>');
                    },
                    complete: function() {
                        $('.buttonDelete').removeAttr('disable');
                        $('.buttonDelete').html('DELETE');
                    },
                    success: function(response) {
                        if (response.status == 404) {
                            $("#modalDelete").modal('hide');

                            alertify
                                .delay(4500)
                                .log(response.message);
                        } else {
                            fetchEmployee();

                            $("#modalDelete").modal('hide');

                            alertify
                                .delay(4500)
                                .log(response.message);

                        }
                    }
                });
            });

            // SHOWING DATA PER ID FOR EDIT
            $(document).on('click', '.edit_btn', function(e) {
                e.preventDefault();

                let emp_id = $(this).val();
                $("#modalEdit").modal('show');

                $.ajax({
                    type: "GET",
                    url: "{{ url('employee') }}/" + emp_id + "",
                    success: function(response) {
                        // Jika id tidak ditemukan
                        if (response.status == 404) {
                            alertify
                                .delay(4500)
                                .log(response.message);

                            $("#modalEdit").modal('hide');

                            $(document).find('span.error-text').text('');
                            $(document).find('input.form-control').removeClass(
                                'is-invalid');
                            $(document).find('input.custom-file-input').removeClass(
                                'is-invalid');
                            // Jika id ditemukan
                        } else {
                            $("#emp_id").val(emp_id);
                            $("#edit_name").val(response.dataEmployee.name);
                            $("#edit_phone").val(response.dataEmployee.phone);
                            // $("#edit_image").val(response.dataEmployee.image);
                            $('#edit_image').html(response.dataEmployee.image);

                            // Close validation
                            $(document).find('span.error-text').text('');
                            $(document).find('input.form-control').removeClass(
                                'is-invalid');
                            $(document).find('input.custom-file-input').removeClass(
                                'is-invalid');
                        }
                    },
                    error: function(response) {
                        alert(response.status + "\n" + response.message + "\n" + thrownError);
                    }
                });

            });

            // UPDATED NEW DATA EMPLOYEE
            $("#editDataForm").on("submit", function(e) {
                e.preventDefault();

                let idDataEmp = $('#emp_id').val();

                $.ajax({
                    url: "{{ url('update-employee') }}/" + idDataEmp + "",
                    method: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $('.buttonEdit').attr('disable', 'disable');
                        $('.buttonEdit').html('<i class="fa fa-spin fa-spinner"></i>');
                        // Ketika benar sudah melewati validasi maka hilangkan error validasinya
                        $(document).find('span.error-text').text('');
                        $(document).find('input.form-control').removeClass(
                            'is-invalid');
                        $(document).find('input.custom-file-input').removeClass(
                            'is-invalid');
                    },
                    complete: function() {
                        $('.buttonEdit').removeAttr('disable');
                        $('.buttonEdit').html('UPDATE');
                    },
                    success: function(response) {
                        if (response.status == 400) {
                            $.each(response.errors, function(key, value) {
                                // kasih is-invalid dan pesan error dan ini class sama id harus beda dari form create kalo sama nanti di form create akan muncul errornya juga maka akan bentrok
                                $("input#" + "edit_" + key).addClass('is-invalid');
                                $('span.' + key + '_error_edit').text(value[0]);
                            });
                        } else if (response.status == 404) {
                            alertify
                                .delay(4500)
                                .log(response.message);

                            $("#modalEdit").modal('hide');
                        } else {

                            $('#editDataForm')[0].reset();
                            $('#editDataForm').find('.custom-file-label').html(
                                'Choose Image');
                            $("#modalEdit").modal('hide');

                            fetchEmployee();

                            alertify
                                .delay(3500)
                                .log(response.message);
                        }
                    },
                    error: function(response) {
                        alert(response.status + "\n" + response.errors + "\n" + thrownError);
                    }
                });
                return false;
            });

            // CREATE NEW DATA EMPLOYEE
            $("#createDataForm").on("submit", function(e) {
                e.preventDefault();

                // Ajax Request
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $('.buttonInsert').attr('disable', 'disable');
                        $('.buttonInsert').html('<i class="fa fa-spin fa-spinner"></i>');
                        // Ketika benar sudah melewati validasi maka hilangkan error validasinya
                        $(document).find('span.error-text').text('');
                        $(document).find('input.form-control').removeClass(
                            'is-invalid');
                        $(document).find('input.custom-file-input').removeClass(
                            'is-invalid');
                    },
                    complete: function() {
                        $('.buttonInsert').removeAttr('disable');
                        $('.buttonInsert').html('ADD');
                    },
                    success: function(response) {
                        if (response.status == 400) {
                            $.each(response.errors, function(key, value) {
                                // kasih is-invalid dan pesan error
                                $("input#" + key).addClass('is-invalid');
                                $('span.' + key + '_error').text(value[0]);
                            });
                        } else {
                            // ketika benar semua maka reset dan close modalnya
                            $('#createDataForm')[0].reset();
                            $('#createDataForm').find('.custom-file-label').html(
                                'Choose Image');
                            $("#modalCreate").modal('hide');

                            fetchEmployee();
                            // kasih alert biar ganteng
                            alertify
                                .delay(3500)
                                .log(response.message);
                        }
                    },
                    error: function(response) {
                        alert(response.status + "\n" + response.errors + "\n" + thrownError);
                    }
                });
                return false;
            });



            // Input File name show
            $(document).on('change', 'input[type="file"]', function(event) {
                var imageName = $(this).val();
                if (imageName == undefined || imageName == "") {
                    $(this).next('.custom-file-label').html('No image chosen');
                } else {
                    $(this).next('.custom-file-label').html(event.target.files[0].name);
                }
            });
        });
    </script>
@endpush
