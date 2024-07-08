@extends('admin.layouts.master')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}"/>
    <div class="row">
        <div class="col-md-12">
            <div class="overview">
                <div class="alert alert-success h4"> You can add new admin from here</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="box box-default box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Add System Admin</h3>
                    <!-- /.box-tools -->
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <div class="">
                        <div class="col-md-12">
                            <div class="ermsg"></div>

                            @if (\Session::has('success'))
                                <div class="alert alert-success">
                                    {!! \Session::get('success') !!}
                                </div>
                            @endif

                            @if (\Session::has('error'))
                                <div class="alert alert-warning">
                                    {!! \Session::get('error') !!}
                                </div>
                            @endif

                                <div class="card-body">
                                    <form method="POST" action="{{ route('save_admin') }}">
                                        @csrf

                                        <div class="row mb-3 form-group">
                                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                                            <div class="col-md-6">
                                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3 form-group">
                                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('User Name') }}</label>
                                            <div class="col-md-6">
                                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                                                @error('username')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3 form-group">
                                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                            <div class="col-md-6">
                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3 form-group">
                                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Phone Number') }}</label>
                                            <div class="col-md-6">
                                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus>
                                                @error('phone')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3 form-group">
                                            <label for="branch_id" class="col-md-4 col-form-label text-md-end">{{ __('Branch') }}</label>

                                            <div class="col-md-6">
                                                <select name="branch_id[]" id="branch_id" class="form-control" multiple>
                                                    @foreach (\App\Models\Branch::where('status','1')->get(); as $branch)
                                                    <option value="{{$branch->id}}">{{$branch->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3 form-group">
                                            <label for="role_id" class="col-md-4 col-form-label text-md-end">{{ __('Role') }}</label>

                                            <div class="col-md-6">
                                                <select name="role_id" id="role_id" class="form-control">
                                                    <option value="">Select</option>
                                                    @foreach (\App\Models\Role::all(); as $role)
                                                    <option value="{{$role->id}}">{{$role->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3 form-group">
                                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                            <div class="col-md-6">
                                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3 form-group">
                                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                            <div class="col-md-6">
                                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                                @error('password_confirmation')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-0">
                                            <div class="col-md-6 offset-md-4">
                                                <button type="submit" class="btn btn-primary">
                                                    {{ __('Register') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
            <!-- /.box -->
        </div>

        <script>
            $(function () {
                $('.select2').select2();

            });


            var url = "{{URL::to('/admin/save-user')}}";

            function save_user_info() {
                if ($("#username").val() == "") {
                    alert("Please Provide the Name of the User");
                    return;
                }

                if ($("#email").val() == "") {
                    alert("Please Provide the Email of the User");
                    return;
                }


                if ($("#password").val() == "") {

                    alert("Please Provide the Password of the User");
                    return;
                }

                if ($("#branchdropdown").val() == "") {

                    alert("Please Provide the Branch of the User");
                    return;
                }

                data = {
                    name: $("#username").val(),
                    email: $("#email").val(),
                    branch: $("#branchdropdown").val(),
                    password: $("#password").val(),
                };
                $.ajax({
                    data: {
                        data: data
                    },
                    url: url,
                    type: 'POST',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        console.log(response);
                        if (response == "Duplicate") {
                            alert('Duplicate email found! please check.');

                        } else {
                            $(".ermsg").html(response.message);
                            $("#username").val("");
                            $("#email").val("");
                            $("#branchdropdown").val("");
                            $("#password").val("");
                        }

                    },
                    error: function (err) {
                        console.log(err);
                        alert("Something Went Wrong, Please check again");
                    }
                });
            }

            

            let helpersbranch =
                {
                    buildDropdown: function (result, table, emptyMessage) {
                        // Remove current options
                        table.html('');
                        // Add the empty option with the empty message
                        table.append('<option value="">' + emptyMessage + '</option>');
                        // Check result isnt empty
                        if (result != '') {
                            // Loop through each of the results and append the option to the table
                            $.each(result, function (k, v) {
                                if (v.branchstatus == 1)
                                    table.append('<option value="' + v.branchid + '">' + v.branchname + '</option>');

                            });
                        }
                    }
                }

            

            let helpersrole =
                {
                    buildDropdown: function (result, table, emptyMessage) {
                        // Remove current options
                        table.html('');
                        // Add the empty option with the empty message
                        table.append('<option value="">' + emptyMessage + '</option>');
                        // Check result isnt empty
                        if (result != '') {
                            // Loop through each of the results and append the option to the table
                            $.each(result, function (k, v) {
                                if (v.status == 1)
                                    table.append('<option value="' + v.id + '">' + v.name + '</option>');

                            });
                        }
                    }
                }

        </script>
@endsection


    
@section('script')


@endsection