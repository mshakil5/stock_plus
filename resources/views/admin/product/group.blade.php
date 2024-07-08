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
                Product Group
            @endslot
            @slot('description')
                Particular products information
            @endslot
            @slot('body')
                @component('components.table')
                    @slot('tableID')
                        groupTBL
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
                        <td><span>New Group </span></td>
                        <td><input type="text" id="groupName2" value="" class="span6 " required=""></td>
                    </tr>
                    
                    <tr>
                        <td><span>Group ID</span></td>
                        <td><input type="text" id="groupid" value="" class="span6 " required=""></td>
                    </tr>

                    <tr>
                        <td colspan="2">
                                <button type="submit" onclick="save_group2();"
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
    
    var groupurl = "{{URL::to('/admin/product-group')}}";
    var groupTBL = $('#groupTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: groupurl,
        deferRender: true,
        // searching:false,
        columns: [
            {
                data: 'name', name: 'name', render: function (data, type, row, meta) {
                    return `<input id='${row.id}' value="${data}" type="text" class="form-control" maxlength="50px"/>`;
                }
            },
            {
                data: 'groupid', name: 'groupid', render: function (data, type, row, meta) {
                    return `<input id='groupid${row.id}' value="${data}" type="text" class="form-control" maxlength="50px"/>`;
                }
            },
            {
                data: 'status', name: 'status', render: function (data, type, row, meta) {
                    let pub_group = `<label style="margin-bottom:0px" class="switch"><button  onclick='group_status("unpublished-group","${row.id}")' ><input id="switchMenu" type="checkbox" checked><span class="slider round"></span></button></label>`;

                    let unpub_group = `<label style="margin-bottom:0px" class="switch"><button  onclick='group_status("published-group","${row.id}")' ><input id="switchMenu" type="checkbox"><span class="slider round"></span></button></label>`;
                    if (row.status == 1)
                        status = pub_group;
                    else {
                        status = unpub_group;
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
    function group_status(route, id) {

        $.ajax({
            url: stsurl + "/" + route + "/" + id,
            type: 'GET',
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                groupTBL.draw();
                showSnakBar();
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    }

    function edit_data(id) {
        let groupName = $("#" + id).val();
        let groupid = $("#"+"groupid"+ id).val();
        if (!groupName) {
            alert("Please Provide Group Name");
            group_load();
            return;
        }
        if (!groupid) {
            alert("Please Provide Group ID");
            group_load();
            return;
        }
        let data = {
            groupname: groupName,groupid:groupid
        };
        $.ajax({
            url: stsurl +'/edit-group/' + id,
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
                    groupTBL.draw();
                    showSnakBar();
                }

            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    }

    
    var groupurl = "{{URL::to('/admin/group')}}";
    function save_group2() {
        if ($("#groupName2").val() == "") {
            alert("Please Provide Group Name");
        }
        if ($("#groupid").val() == "") {
            alert("Please Provide Group ID");
        } else {
            
            var group = $("#groupName2").val()
            var groupid = $("#groupid").val()
            $.ajax({
                data: {
                    group: group,groupid:groupid
                },
                url: groupurl,
                type: 'POST',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    if (response.status == 303) {
                            $(".ermsg").html(response.message);
                        } else {
                            
                        groupTBL.draw();
                        $("#groupName").val("")
                        $("#groupid").val("")
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