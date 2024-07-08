

@extends('admin.layouts.master')
@section('content')


<meta name="csrf-token" content="{{ csrf_token() }}"/>

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <?php
    echo Session::put('message', '');
    ?>
    @if (session('info'))
        <div class="alert alert-danger">
            {{ session('info') }}
        </div>
    @endif
    <?php
    echo Session::put('info', '');
    ?>

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
  
    <div class="row">
        <div class="col-md-6">
            @component('components.widget')
                @slot('title')
                    System Admin
                @endslot
                @slot('description')
                    System Admin Information
                @endslot
                @slot('body')
                    @component('components.table')
                        @slot('tableID')
                            userTBL
                        @endslot
                        @slot('head')
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th><i class=""></i> Action</th>
                        @endslot
                        
                        @slot('body')
                            @foreach ($users as $data)
                                <tr>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->email}}</td>
                                    <td>{{$data->role->name}}</td>
                                    <td>{{$data->branch->name}}</td>
                                    @if ($data->status == 1)
                                        <td><label style="margin-bottom:0px" class="switch"><button  onclick='user_status("unpublished-user","{{$data->id}}")' ><input id="switchMenu" type="checkbox" checked><span class="slider round"></span></button></label></td>
                                    @else
                                        <td>
                                            <label style="margin-bottom:0px" class="switch"><button  onclick='user_status("published-user","{{$data->id}}")' ><input id="switchMenu" type="checkbox"><span class="slider round"></span></button></label>
                                        </td>
                                    @endif
                                    
                                    <td>
                                        <span class="btn btn-success btn-sm editThis" id="editThis" vid="{{$data->id}}" name="{{$data->name}}" username="{{$data->username}}" phone="{{$data->phone}}"  email="{{$data->email}}"  role_id="{{$data->role_id}}"  branch_id="{{$data->branch_id}}" branchaccess="{{$data->branchaccess}}"> <i class='fa fa-pencil'></i> Edit </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endslot
                    @endcomponent
                @endslot
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.widget')
                @slot('title')
                    User Information
                @endslot
                @slot('description')
                @endslot
                @slot('body')
                    <hr/>

                    <div class="col-sm-12" id="editDiv">
                        <form class="form-horizontal" action="{{ route('update_admin')}}" method="POST">
                            {{csrf_field()}}
                        
                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label">Name<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" class="form-control" id="name"  value="{{ old('name') }}" required autocomplete="name" autofocus>
                                    <input type="hidden" name="userid" class="form-control" id="userid"  value="{{ old('userid') }}" required autocomplete="userid" autofocus>
                                </div>
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label">User Name<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="username" class="form-control" id="username"  value="{{ old('username') }}" required autocomplete="username">
                                    @if ($errors->has('username'))
                                        <span class="invalid-feedback text-danger" role="alert">
                                        <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" name="email" class="form-control" id="email"  value="{{ old('email') }}" required autocomplete="email">
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback text-danger" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label">Phone<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="phone" class="form-control" id="phone"  value="{{ old('phone') }}" required autocomplete="phone">
                                    @if ($errors->has('phone'))
                                        <span class="invalid-feedback text-danger" role="alert">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            
                            <div class="form-group">
                                <label for="branch_id" class="col-sm-3 control-label">Branch</label>
                                <div class="col-sm-9">
                                    <select name="branch_id[]" id="branch_id" class="form-control" multiple>
                                        @foreach (\App\Models\Branch::where('status','1')->get() as $branch)
                                        @if (old('branch_id') == $branch->id)
                                            <option value="{{$branch->id}}" selected>{{$branch->name}}</option>
                                        @else
                                            <option value="{{$branch->id}}">{{$branch->name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @if ($errors->has('branch_id'))
                                        <span class="invalid-feedback text-danger" role="alert">
                                        <strong>{{ $errors->first('branch_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="role_id" class="col-sm-3 control-label">Role</label>
                                <div class="col-sm-9">
                                    <select name="role_id" id="role_id" class="form-control">
                                        <option value="">Select</option>
                                        @foreach (\App\Models\Role::all() as $role)
                                        @if (old('role_id') == $role->id)
                                            <option value="{{$role->id}}" selected>{{$role->name}}</option>
                                        @else
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    
                                    @if ($errors->has('role_id'))
                                        <span class="invalid-feedback text-danger" role="alert">
                                        <strong>{{ $errors->first('role_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="col-sm-3 control-label">Password<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="password" name="password" class="form-control" id="password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label"></label>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary text-center"><i class="fa fa-save"></i> Update</button>
                                    <input type="button" class="btn btn-warning text-center" id="FormCloseBtn" value="Close">
                                </div>
                            </div>
                            


                        </form>
                    </div>
                @endslot
            @endcomponent
        </div>
        
    </div>


 
@endsection
    
@section('script')

<script>
    var stsurl = "{{URL::to('/admin')}}";
    function user_status(route, id) {

        $.ajax({
            url: stsurl + "/" + route + "/" + id,
            type: 'GET',
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                showSnakBar();
                window.setTimeout(function(){location.reload()},700)
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    }
</script>
<script>
    function save_user_info() {
        let username = $("#username").val();
        let categoryid = $("#categorydropdown").val();
        let password = $("#password").val();

        if (username == "") {
            alert("Please Provide the Username of The User");
            return;
        }
        if (branchid == "") {
            alert("Please Provide the Branch Name");
            return;
        }
        if (categoryid == "") {
            alert("Please Provide the Category");
            return;
        } else {
            let data = {
                id: dataToPush[row].id,
                username: username,
                password: password,
                categoryid: categoryid

            };
            $.ajax({
                data: {data: data},
                url: '/update-user-info',
                type: 'POST',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    if (response == "Duplicate") {
                        alert('Duplicate username found! please check.');

                    } else {
                        showSnakBar();
                        window.setTimeout(function () {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }
    }


</script>
<script>
    $(function () {
        $('.select2').select2();
        $(".show_password"). prop("checked", false);
        function getFormattedDate(date) {
            let data = new Date(date);
            let year = data.getFullYear();
            let month = (1 + data.getMonth()).toString().padStart(2, '0');
            let day = data.getDate().toString().padStart(2, '0');

            return day + '/' + month + '/' + +year;
        }

        let logTBL = $('#logTBL').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': true
        });
        let invoiceTBL = $('#invoiceTBL').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': true
        });
        


    });
</script>

<script>
    $(document).ready(function () {

        // $("#editDiv").hide();
        $("#FormCloseBtn").click(function(){
            $("#editDiv").hide();
        });


        // header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        // 

        // return stock
        $("#userTBL").on('click','#editThis', function(){
            $("#editDiv").show();

            id = $(this).attr('vid');
            role_id = $(this).attr('role_id');
            branch_id = $(this).attr('branch_id');
            name = $(this).attr('name');
            email = $(this).attr('email');
            branchaccess = $(this).attr('branchaccess');
            console.log(branchaccess);
            username = $(this).attr('username');
            phone = $(this).attr('phone');

            $('#userid').val(id);
            $('#role_id').val(role_id);
            // $('#branch_id').val(branchaccess);
            $('#branch_id').val(branch_id);
            $('#name').val(name);
            $('#email').val(email);
            $('#username').val(username);
            $('#phone').val(phone);

            
            
            });
        // return stock end


    });  
    </script>

@endsection