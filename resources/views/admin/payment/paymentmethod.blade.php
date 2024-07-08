@extends('admin.layouts.master')
@section('content')


<?php
$user_id = Session::get('categoryEmployId');
$brnach_id = Session::get('brnach_id');
?>
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
<div class="row">
    <div class="col-md-8">
        @component('components.widget')
            @slot('title')
                Payment Method
            @endslot
            @slot('description')
            Payment Method information
            @endslot
            @slot('body')
                @component('components.table')
                    @slot('tableID')
                        paymentmethodTBL
                    @endslot
                    @slot('head')
                        <th>Name</th>
                        <th>Status</th>
                        <th><i class=""></i> Action</th>
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>
    <div class="col-md-4">
        <div class="box-inner">
            <div class="box-content">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td><span>New Method </span></td>
                        <td><input type="text" id="name" value="" class="span6 " required=""></td>
                    </tr>

                    <tr>
                        <td colspan="2">
                                <button type="submit" onclick="save_method2();"
                                        class="btn btn-primary btn-sm center-block"><i
                                            class="fa fa-save"></i> SAVE
                                </button></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('.select2').select2();
    });
    
    var brandurl = "{{URL::to('/admin/payment-method')}}";
    var paymentmethodTBL = $('#paymentmethodTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: brandurl,
        deferRender: true,
        // searching:false,
        columns: [
            {
                data: 'name', name: 'name', render: function (data, type, row, meta) {
                    return `<input id='${row.id}' value="${data}" type="text" class="form-control" maxlength="50px"/>`;
                }
            },
            {
                data: 'status', name: 'status', render: function (data, type, row, meta) {
                    let pub_method = `<label style="margin-bottom:0px" class="switch"><button  onclick='brand_status("unpublished-method","${row.id}")' ><input id="switchMenu" type="checkbox" checked><span class="slider round"></span></button></label>`;

                    let unpub_method = `<label style="margin-bottom:0px" class="switch"><button  onclick='brand_status("published-method","${row.id}")' ><input id="switchMenu" type="checkbox"><span class="slider round"></span></button></label>`;
                    if (row.status == 1)
                        status = pub_method;
                    else {
                        status = unpub_method;
                    }
                    return status
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                "render": function (data, type, row, meta) {
                    return ` <button class="btn btn-flat btn-sm btn-primary" data-toggle="modal" onclick='edit_data("${row.id}")'><i class="fa fa-save"></i> Save</button>`;
                }
            },
        ]
    });

    var stsurl = "{{URL::to('/admin')}}";
    function brand_status(route, id) {

        $.ajax({
            url: stsurl + "/" + route + "/" + id,
            type: 'GET',
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                paymentmethodTBL.draw();
                showSnakBar();
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    }

    function edit_data(id) {
        let name = $("#" + id).val();
        if (!name) {
            alert("Please Provide Name");
            method_load();
            return;
        }
        let data = {
            name: name,
        };
        $.ajax({
            url: stsurl +'/edit-method/' + id,
            data: {
                data: data
            },
            type: 'POST',
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                paymentmethodTBL.draw();
                showSnakBar();
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    }

    
    var methodurl = "{{URL::to('/admin/payment-method')}}";
    function save_method2() {
        if ($("#name").val() == "") {
            alert("Please Provide method Name");
        } else {
            
            var name = $("#name").val()
            $.ajax({
                data: {
                    name: name
                },
                url: methodurl,
                type: 'POST',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    paymentmethodTBL.draw();
                    $("#name").val("")
                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }


    }
</script>




@endsection
    
@section('script')


@endsection