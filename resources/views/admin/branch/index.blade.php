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
            Branch
            @endslot
            @slot('description')
            Branch information
            @endslot
            @slot('body')
                @component('components.table')
                    @slot('tableID')
                        branchTBL
                    @endslot
                    @slot('head')
                        <th>Name</th>
                        <th>Invoice Format</th>
                        <th>Quotation Format</th>
                        <th>Status</th>
                        <th>Branch Info</th>
                        <th>Mail</th>
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
                        <td><span>New Branch </span></td>
                        <td><input type="text" id="branchName2" value="" class="span6 " required=""></td>
                    </tr>

                    <tr>
                        <td colspan="2">
                                <button type="submit" onclick="save_branch2();"
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
    
    var branchurl = "{{URL::to('/admin/branch')}}";
    var branchTBL = $('#branchTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: branchurl,
        deferRender: true,
        // searching:false,
        columns: [
            {
                data: 'name', name: 'name', render: function (data, type, row, meta) {
                    return `<input id='${row.id}' value="${data}" type="text" class="form-control" maxlength="50px"/>`;
                }
            },
            {
                data: 'invoice_format', name: 'invoice_format', render: function (data, type, row, meta) {
                    return `<select id="invoiceFormat_${row.id}" class="form-control">
                                <option value="A4" ${data === 'A4' ? 'selected' : ''}>A4</option>
                                <option value="POS" ${data === 'POS' ? 'selected' : ''}>POS printer size</option>
                            </select>`;
                }
            },
            {
                data: 'quotation_format', name: 'quotation_format', render: function (data, type, row, meta) {
                    return `<select id="quotationFormat_${row.id}" class="form-control">
                                <option value="A4" ${data === 'A4' ? 'selected' : ''}>A4</option>
                                <option value="POS" ${data === 'POS' ? 'selected' : ''}>POS printer size</option>
                            </select>`;
                }
            },
            {
                data: 'status', name: 'status', render: function (data, type, row, meta) {
                    let pub_branch = `<label style="margin-bottom:0px" class="switch"><button  onclick='branch_status("unpublished-branch","${row.id}")' ><input id="switchMenu" type="checkbox" checked><span class="slider round"></span></button></label>`;

                    let unpub_branch = `<label style="margin-bottom:0px" class="switch"><button  onclick='branch_status("published-branch","${row.id}")' ><input id="switchMenu" type="checkbox"><span class="slider round"></span></button></label>`;
                    if (row.status == 1)
                        status = pub_branch;
                    else {
                        status = unpub_branch;
                    }
                    return status
                }
            },
            {
                data: 'id', name: 'id', render: function (data, type, row, meta) {
                    return `<a href="{{ url('/admin/branch/details') }}/${row.id}" class="btn btn-info btn-sm"><i class="fa fa-info-circle"></i> Details</a>`;
                }
            },
            {
                data: 'id',
                name: 'mail',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    if (row.has_details) {
                        return '<a href="send-mail/' + data + '" class="btn btn-sm btn-primary">Mail</a>';
                    } else {
                        return '<button class="btn btn-sm btn-primary" disabled>Mail</button>';
                    }
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                "render": function (data, type, row, meta) {
                    return ` <button class="btn btn-flat btn-sm btn-primary" data-toggle="modal" onclick='edit_data("${row.id}")'><i class="fa fa-save"></i> Update</button>`;
                }
            },
        ]
    });

    var stsurl = "{{URL::to('/admin')}}";
    function branch_status(route, id) {

        $.ajax({
            url: stsurl + "/" + route + "/" + id,
            type: 'GET',
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                branchTBL.draw();
                showSnakBar();
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    }

    function edit_data(id) {
        let branchName = $("#" + id).val();
        let invoiceFormat = $(`#invoiceFormat_${id}`).val();
        let quotationFormat = $(`#quotationFormat_${id}`).val();
        if (!branchName) {
            alert("Please Provide Branch Name");
            brand_load();
            return;
        }
        let data = {
            branchName: branchName,
            invoiceFormat: invoiceFormat,
            quotationFormat: quotationFormat
        };
        console.log(data);
        $.ajax({
            url: stsurl +'/edit-branch/' + id,
            data: {
                data: data
            },
            type: 'POST',
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                branchTBL.draw();
                showSnakBar();
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    }

    
    var branchurl = "{{URL::to('/admin/branch')}}";
    function save_branch2() {
        if ($("#branchName2").val() == "") {
            alert("Please Provide Branch Name");
        } else {
            
            var branch = $("#branchName2").val()
            $.ajax({
                data: {
                    branch: branch
                },
                url: branchurl,
                type: 'POST',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    branchTBL.draw();
                    $("#branchName").val("")
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