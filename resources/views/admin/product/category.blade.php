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

<div class="row">
    <div class="col-md-8">
        <div class="ermsg2"></div>
        @component('components.widget')
            @slot('title')
                Product Code
            @endslot
            @slot('description')
                Particular Code information
            @endslot
            @slot('body')
                @component('components.table')
                    @slot('tableID')
                        categoryTBL
                    @endslot
                    @slot('head')
                        <th>Name</th>
                        <th>ID</th>
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
                <div class="ermsg"></div>
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td><span>New Code </span></td>
                        <td><input type="text" id="category2" value="" class="span6 " required=""></td>
                    </tr>
                    <tr>
                        <td><span>Code ID</span></td>
                        <td><input type="text" id="categoryid" value="" class="span6 " required=""></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                                <button type="submit" onclick="save_category2();"
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

        
        var caturl = "{{URL::to('/admin/product-category')}}";
        var categoryTBL = $('#categoryTBL').DataTable({
            processing: true,
            serverSide: true,
            ajax: caturl,
            deferRender: true,
            // searching:false,
            columns: [
                {
                    data: 'name', name: 'name', render: function (data, type, row, meta) {
                        return `<input id='${row.id}' value="${data}" type="text" class="form-control" maxlength="50px"/>`;
                    }
                },
                {
                    data: 'categoryid', name: 'categoryid', render: function (data, type, row, meta) {
                        return `<input id='categoryid${row.id}' value="${data}" type="number" class="form-control" maxlength="50px"/>`;
                    }
                },
                {
                    data: 'status', name: 'status', render: function (data, type, row, meta) {
                        let pub_category = `<label style="margin-bottom:0px" class="switch"><button  onclick='category_status("unpublished-category","${row.id}")' >
                        <input id="switchMenu" type="checkbox" checked><span class="slider round"></span></button></label>`;

                        let unpub_category = `<label style="margin-bottom:0px" class="switch"><button  onclick='category_status("published-category","${row.id}")'>
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

        function category_status(route, id) {

            $.ajax({
                url: stsurl + "/" + route + "/" + id,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    categoryTBL.draw();
                    showSnakBar();
                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }

        function edit_data(id) {
            let categoryName = $("#" + id).val();
            let categoryid = $("#"+"categoryid"+ id).val();

            console.log(categoryid);


            if (!categoryName) {
                alert("Please Provide Category Name");
                category_load();
                return;
            }

            if (!categoryid) {
                alert("Please Provide Category ID");
                category_load();
                return;
            }

            let data = {
                categoryname: categoryName,
                categoryid: categoryid,
            };
            $.ajax({
                url: stsurl + '/edit-category/' + id,
                data: {
                    data: data
                },
                type: 'POST',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {

                    if (response.status == 303) {
                            $(".ermsg2").html(response.message);
                        } else {
                            
                        categoryTBL.draw();
                        showSnakBar();
                        }




                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }

        var categoryurl = "{{URL::to('/admin/category')}}";
        function save_category2() {
            if ($("#category2").val() == "") {
                alert("Please Provide Category Name");
            }
            if ($("#categoryid").val() == "") {
                alert("Please Provide Category ID");
            } else {
                var category = $("#category2").val()
                var categoryid = $("#categoryid").val()
                $.ajax({
                    data: {
                        category:category,categoryid:categoryid
                    },
                    url: categoryurl,
                    type: 'POST',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        if (response.status == 303) {
                            $(".ermsg").html(response.message);
                        } else {
                            
                        categoryTBL.draw();
                        $("#category2").val("")
                        $("#categoryid").val("")
                        window.setTimeout(function(){location.reload()},2000)
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

@endsection
    
@section('script')


@endsection