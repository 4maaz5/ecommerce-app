@extends('admin.layout.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            @if(session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session()->has('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Change Password</h1>
                </div>

            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">

            <form action="" method="Post" id="changePasswordForm" name="changePasswordForm">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="old_password">Old Password</label>
                                    <input type="password" name="old_password" id="old_password" class="form-control"
                                        placeholder="old_password">
                                    <p></p>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="new_password">New Password</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control"
                                        placeholder="new_password">
                                    <p></p>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                                        placeholder="confirm_password">
                                    <p></p>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
@section('customjs')
    <script>
        $('#changePasswordForm').submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('admin.changePassword') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);

                    if (response["status"] === true) {
                         window.location.href = "{{ route('admin.changePasswordForm') }}";

                    } else {
                        var errors = response['errors'];

                        if (errors['old_password']) {
                            $('#old_password').addClass('is-invalid').siblings('p').addClass('invalid-feedback')
                                .html(errors['old_password']);
                        } else {
                            $('#old_password').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("");
                        }
                        if (errors['new_password']) {
                            $('#new_password').addClass('is-invalid').siblings('p').addClass('invalid-feedback')
                                .html(errors['new_password']);
                        } else {
                            $('#new_password').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("");
                        }
                        if (errors['confirm_password']) {
                            $('#confirm_password').addClass('is-invalid').siblings('p').addClass('invalid-feedback')
                                .html(errors['confirm_password']);
                        } else {
                            $('#confirm_password').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("");
                        }
                    }
                },
                error: function(jqXHR, exception) {
                    console.log('something went wrong!');
                }

            });
        });




    </script>
@endsection
