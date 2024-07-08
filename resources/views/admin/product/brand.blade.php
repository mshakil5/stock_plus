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
        <div class="ermsg2"></div>
        @component('components.widget')
            @slot('title')
                Product Brand
            @endslot
            @slot('description')
                Particular products information
            @endslot
            @slot('body')
                @component('components.table')
                    @slot('tableID')
                        brandTBL
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
                        <td><span>New Brand </span></td>
                        <td><input type="text" id="brandName2" value="" class="span6 " required=""></td>
                    </tr>
                    <tr>
                        <td><span> Brand ID</span></td>
                        <td><input type="text" id="brandid" value="" class="span6 " required=""></td>
                    </tr>

                    <tr>
                        <td colspan="2">
                                <button type="submit" onclick="save_brand2();"
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
    
    var brandurl = "{{URL::to('/admin/product-brand')}}";
    var brandTBL = $('#brandTBL').DataTable({
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
                data: 'brandid', name: 'brandid', render: function (data, type, row, meta) {
                    return `<input id='brandid${row.id}' value="${data}" type="text" class="form-control" maxlength="50px"/>`;
                }
            },
            {
                data: 'status', name: 'status', render: function (data, type, row, meta) {
                    let pub_brand = `<label style="margin-bottom:0px" class="switch"><button  onclick='brand_status("unpublished-brand","${row.id}")' ><input id="switchMenu" type="checkbox" checked><span class="slider round"></span></button></label>`;

                    let unpub_brand = `<label style="margin-bottom:0px" class="switch"><button  onclick='brand_status("published-brand","${row.id}")' ><input id="switchMenu" type="checkbox"><span class="slider round"></span></button></label>`;
                    if (row.status == 1)
                        status = pub_brand;
                    else {
                        status = unpub_brand;
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
                brandTBL.draw();
                showSnakBar();
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    }

    function edit_data(id) {
        let brandName = $("#" + id).val();
        let brandid = $("#"+"brandid"+ id).val();
        if (!brandName) {
            alert("Please Provide Brand Name");
            brand_load();
            return;
        }
        if (!brandid) {
            alert("Please Provide Brand ID");
            brand_load();
            return;
        }
        let data = {
            brandname: brandName,brandid:brandid
        };
        $.ajax({
            url: stsurl +'/edit-brand/' + id,
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
                    brandTBL.draw();
                    showSnakBar();
                }



            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    }

    
    var brandurl = "{{URL::to('/admin/brand')}}";
    function save_brand2() {
        if ($("#brandName2").val() == "") {
            alert("Please Provide Brand Name");
        }
        if ($("#brandid").val() == "") {
            alert("Please Provide Brand ID");
        } else {
            
            var brand = $("#brandName2").val()
            var brandid = $("#brandid").val()
            $.ajax({
                data: {
                    brand: brand,brandid:brandid
                },
                url: brandurl,
                type: 'POST',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {

                    if (response.status == 303) {
                            $(".ermsg").html(response.message);
                        } else {
                            
                        brandTBL.draw();
                        $("#brandName").val("")
                        $("#brandid").val("")
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