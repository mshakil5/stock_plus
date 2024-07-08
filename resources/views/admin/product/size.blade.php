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
<div class="row">
    <div class="col-md-8">
        @component('components.widget')
            @slot('title')
                Product Size
            @endslot
            @slot('description')
                Particular Size information
            @endslot
            @slot('body')
                @component('components.table')
                    @slot('tableID')
                        sizeTBL
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
                        <td><span>New Size </span></td>
                        <td><input type="text" id="size2" value="" class="span6 " required=""></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                                <button type="submit" onclick="save_size2();"
                                        class="btn btn-primary btn-sm center-block"><i
                                            class="fa fa-save"></i> SAVE
                                </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });

        
        var sizeurl = "{{URL::to('/admin/product-size')}}";
        var sizeTBL = $('#sizeTBL').DataTable({
            processing: true,
            serverSide: true,
            ajax: sizeurl,
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
                        let pub_category = `<label style="margin-bottom:0px" class="switch"><button  onclick='size_status("unpublished-size","${row.id}")' >
                        <input id="switchMenu" type="checkbox" checked><span class="slider round"></span></button></label>`;

                        let unpub_category = `<label style="margin-bottom:0px" class="switch"><button  onclick='size_status("published-size","${row.id}")'>
                                 <input id="switchMenu" type="checkbox"><span class="slider round"></span></button></label>`;
                        if (row.status == 1)
                            status = pub_category;
                        else {
                            status = unpub_category;
                        }
                        return status
                    }
                },
                {data: 'action', name: 'action', orderable: false, searchable: false,"render": function ( data, type, row, meta ) {
                        return `<button class="btn btn-flat btn-sm btn-primary" data-toggle="modal" data-target="#edit_service"
                                         onclick='edit_data("${row.id}")'><i class="fa fa-save"></i> Save</button>`;
                    }},
            ]
        });

        var stsurl = "{{URL::to('/admin')}}";

        function size_status(route, id) {

            $.ajax({
                url: stsurl + "/" + route + "/" + id,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    sizeTBL.draw();
                    showSnakBar();
                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }

        function edit_data(id) {
            let sizeName = $("#" + id).val();
            if (!sizeName) {
                alert("Please Provide Size Name");
                category_load();
                return;
            }
            let data = {
                sizename: sizeName,
            };
            $.ajax({
                url: stsurl + '/edit-size/' + id,
                data: {
                    data: data
                },
                type: 'POST',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    sizeTBL.draw();
                    showSnakBar();

                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }

        var sizeurl = "{{URL::to('/admin/size')}}";
        function save_size2() {
            if ($("#size2").val() == "") {
                alert("Please Provide Size Name");
            } else {
                var size = $("#size2").val()
                $.ajax({
                    data: {
                        size:size
                    },
                    url: sizeurl,
                    type: 'POST',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        sizeTBL.draw();
                        $("#size2").val("")
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