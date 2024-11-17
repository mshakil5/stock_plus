@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div id="alert-container"></div>

        <div class="row well">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.addchartofaccount.filter') }}">
                {{ csrf_field() }}

                <div class="col-md-3">
                    <label class="label label-primary">Branch</label>
                    <select class="form-control select2" name="branch_id" 
                        {{ Auth::user()->role_id != 1 ? 'disabled' : '' }}>
                        <option value="">Select Branch...</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" 
                                {{ request()->input('branch_id') == $branch->id || (Auth::user()->branch_id == $branch->id && Auth::user()->role_id != 1) ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="label label-primary">Account Head</label>
                    <select class="form-control select2" name="account_head">
                        <option value="">Select Account Head..</option>
                        @foreach ($accountHeads as $head)
                            <option value="{{ $head }}" {{ request()->input('account_head') == $head ? 'selected' : '' }}>
                                {{ $head }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="label label-primary">Sub Account Head</label>
                    <select class="form-control select2" name="sub_account_head">
                        <option value="">Select Sub Account Head..</option>
                        @foreach ($subAccountHeads as $subHead)
                            <option value="{{ $subHead }}" {{ request()->input('sub_account_head') == $subHead ? 'selected' : '' }}>
                                {{ $subHead }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <br>
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                </div>
            </form>
        </div>

        @component('components.widget')
            @slot('title')
               Chart Of Accounts
                    <button class="btn btn-lg btn-success pull-right" data-toggle="modal"
                            data-target="#chartModal"
                            data-purpose="0">+
                        Add New Chart Of Account
                    </button>

                    <button class="btn btn-lg btn-success" onclick="window.location.href='{{ route('admin.balancesheet') }}'">
                        Format
                    </button>
            @endslot
            @slot('description')
                Account description
            @endslot
            @slot('body')
                @component('components.table')
                    @slot('tableID')
                        chartTBL
                    @endslot
                    @slot('head')
                        <th>Account Name</th>
                        <th>Account Head</th>
                        <th>Sub Account Head</th>
                        <th>Branch</th>
                        <th>Serial</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th><i class=""></i> Action</th>
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>
</div>


<div class="modal fade" id="chartModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Chart Of Account</h4>
            </div>
            <form class="form-horizontal" id="customer-form">
                <div class="modal-body">
                    {{csrf_field()}}

                    <div class="form-group">
                        <label for="account_head" class="col-sm-3 control-label">Account Head</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="account_head" id="account_head">
                                <option value="">Select</option>
                                <option value="Assets">Assets</option>
                                <option value="Expenses">Expenses</option>
                                <option value="Income">Income</option>
                                <option value="Liabilities">Liabilities</option>
                                <option value="Equity">Equity</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sub_account_head" class="col-sm-3 control-label">Account Sub Head</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="sub_account_head" id="sub_account_head">

                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contingent" class="col-sm-3 control-label">Contingent</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="contingent" id="contingent">
                                <option value="Non-Contingent">Non-Contingent</option>
                                <option value="Contingent">Contingent</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="serial" class="col-sm-3 control-label">Serial</label>
                        <div class="col-sm-9">
                            <input type="number" name="serial" class="form-control " id="serial"
                                placeholder="Ex: 1">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="account_name" class="col-sm-3 control-label">Account Name</label>
                        <div class="col-sm-9">
                            <input type="text" name="account_name" class="form-control " id="account_name"
                                   placeholder="John Doe">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-sm-3 control-label">Description</label>
                        <div class="col-sm-9">
                                <textarea class="form-control" id="description" rows="3" placeholder="Description" name="description"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary submit-btn save-btn"> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
    
@section('script')

<script>

    $(document).ready(function() {
        $('.select2').select2();
    });
    
    var charturl = "{{URL::to('/admin/chart-of-account')}}";
    var customerTBL = $('#chartTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: charturl,
            data: function (d) {
                d.branch_id = $('select[name=branch_id]').val();
                d.account_head = $('select[name=account_head]').val();
                d.sub_account_head = $('select[name=sub_account_head]').val();
            }
        },
        deferRender: true,
        columns: [
            {data: 'account_name', name: 'account_name'},
            {data: 'account_head', name: 'account_head'},
            {data: 'sub_account_head', name: 'sub_account_head'},
            { data: 'branch_name', name: 'branch_name' },
            { data: 'serial', name: 'serial' },
            {data: 'description', name: 'description'},
            {
                data: 'status', name: 'status', render: function (data, type, row, meta) {
                    let status = null;
                    if (row.status == 1) {
                        status = `<label style="margin-bottom:0px" class="switch"><button class="status-btn" value='${row.id}'>
                          <input id="switchMenu" type="checkbox" checked><span class="slider round"></span></button></label>`;
                    } else {
                        status = `<label style="margin-bottom:0px" class="switch"><button class="status-btn" value='${row.id}'>
                          <input id="switchMenu" type="checkbox"><span class="slider round"></span></button></label>`;
                    }
                    return ` ${status} `;
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    let button = `<button type="button" class="btn btn-warning btn-xs edit-btn" data-toggle="modal" data-target="#chartModal" value="${row.id}" title="Edit" data-purpose='1'><i class="fa fa-edit" aria-hidden="true"></i> Edit</button>`;
                    if (row.amount < 0) {
                    }
                    return button;
                }
            },
        ]
    });

    $('form').on('submit', function(e) {
        e.preventDefault();
        customerTBL.ajax.reload();
    });

    $(document).on('click', '.status-btn', function () {
        let confirmation = confirm("Are you sure to change the status?");
        if (confirmation) {
            let id = $(this).val();
            // console.log(id);
            $.ajax({
                url: charturl + '/' + id + '/change-status',
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    showSnakBar('Status Changed Successfully');
                    customerTBL.draw();
                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }
    });

    $('#chartModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        let purpose = button.data('purpose');
        var modal = $(this);
        if (purpose) {
            let id = button.val();
            $.ajax({
                url: charturl +'/' + id,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    var accountHead = response.account_head;
                    var selectedSubHead = response.sub_account_head;

                    $("#sub_account_head").empty();

                    switch (accountHead) {
                        case "Assets":
                            $("<option>").val("Current Asset").text("Current Asset").prop('selected', selectedSubHead === "Current Asset").appendTo("#sub_account_head");
                            $("<option>").val("Fixed Asset").text("Fixed Asset").prop('selected', selectedSubHead === "Fixed Asset").appendTo("#sub_account_head");
                            $("<option>").val("Account Receivable").text("Account Receivable").prop('selected', selectedSubHead === "Account Receivable").appendTo("#sub_account_head");
                            break;
                        case "Expenses":
                            $("<option>").val("Cost Of Good Sold").text("Cost Of Good Sold").prop('selected', selectedSubHead === "Cost Of Good Sold").appendTo("#sub_account_head");
                            $("<option>").val("Overhead Expense").text("Overhead Expense").prop('selected', selectedSubHead === "Overhead Expense").appendTo("#sub_account_head");
                            $("<option>").val("Operating Expense").text("Operating Expense").prop('selected', selectedSubHead === "Operating Expense").appendTo("#sub_account_head");
                            $("<option>").val("Administrative Expense").text("Administrative Expense").prop('selected', selectedSubHead === "Administrative Expense").appendTo("#sub_account_head");
                            break;
                        case "Income":
                            $("<option>").val("Direct Income").text("Direct Income").prop('selected', selectedSubHead === "Direct Income").appendTo("#sub_account_head");
                            $("<option>").val("Indirect Income").text("Indirect Income").prop('selected', selectedSubHead === "Indirect Income").appendTo("#sub_account_head");
                            break;
                        case "Liabilities":
                            $("<option>").val("Current Liabilities").text("Current Liabilities").prop('selected', selectedSubHead === "Current Liabilities").appendTo("#sub_account_head");
                            $("<option>").val("Long Term Liabilities").text("Long Term Liabilities").prop('selected', selectedSubHead === "Long Term Liabilities").appendTo("#sub_account_head");
                            $("<option>").val("Account Payable").text("Account Payable").prop('selected', selectedSubHead === "Account Payable").appendTo("#sub_account_head");
                            $("<option>").val("Short Term Liabilities").text("Short Term Liabilities").prop('selected', selectedSubHead === "Short Term Liabilities").appendTo("#sub_account_head");
                            break;
                        case "Equity":
                            $("<option>").val("Equity Capital").text("Equity Capital").prop('selected', selectedSubHead === "Equity Capital").appendTo("#sub_account_head");
                            $("<option>").val("Retained Earnings").text("Retained Earnings").prop('selected', selectedSubHead === "Retained Earnings").appendTo("#sub_account_head");
                            break;
                        default:
                            return;
                    }

                    // console.log(response);
                    modal.find('#account_head').val(response.account_head);
                    modal.find('#account_name').val(response.account_name);
                    modal.find('#description').val(response.description);
                    modal.find('#contingent').val(response.contingent);
                    modal.find('#serial').val(response.serial);
                    $('#chartModal .submit-btn').removeClass('save-btn').addClass('update-btn').text('Update').val(response.id);
                }
            });
        } else {
            $('#customer-form').trigger('reset');
            $('#customer-form textarea').text('');
            $('#chartModal .submit-btn').removeClass('update-btn').addClass('save-btn').text('Save').val("");
        }
    });

    // save button event

    $(document).on('click', '.save-btn', function () {
        let formData = $('#customer-form').serialize();
        $.ajax({
            url: charturl,
            type: 'POST',
            data: formData,
            beforeSend: function (request) {
                request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                if (response.status === 200) {
                    $('#chartModal').modal('toggle');
                    showSnakBar(response.message);
                    customerTBL.draw();
                    $('#alert-container').html('');
                } else if (response.status === 303) {
                    let alertMessage = `<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>${response.message}</b></div>`;
                    $('#alert-container').html(alertMessage);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    // update button event

    $(document).on('click', '.update-btn', function () {
        let formData = $('#customer-form').serialize();
        let id = $(this).val();
        $.ajax({
            url: charturl + '/' + id,
            type: 'PUT',
            data: formData,
            beforeSend: function (request) {
                request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                if (response.status === 200) {
                    $('#chartModal').modal('toggle');
                    showSnakBar(response.message);
                    customerTBL.draw();
                    $('#alert-container').html('');
                } else if (response.status === 303) {
                    let alertMessage = `<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>${response.message}</b></div>`;
                    $('#alert-container').html(alertMessage);
                }
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    });

</script>

<script>

    function clearSubAccountHead() {
        $("#sub_account_head").empty();
    }

    $('#chartModal').on('hidden.bs.modal', function () {
        clearSubAccountHead();
    });

    function clearfield() {
        $("#sub_account_head").html("<option value=''>Please Select</option>");
    }

    $("#account_head").change(function(){
          $(this).find("option:selected").each(function(){
              var val = $(this).val();
              if( val == "Assets"){
                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Current Asset'>Current Asset</option><option value='Fixed Asset'>Fixed Asset</option><option value='Account Receivable'>Account Receivable</option>");

              } else if(val == "Expenses"){

                  clearfield();
                  $("#sub_account_head").html(
                    "<option value=''>Please Select</option>" +
                    "<option value='Cost Of Good Sold'>Cost Of Good Sold</option>" +
                    "<option value='Overhead Expense'>Overhead Expense</option>" +
                    "<option value='Operating Expense'>Operating Expense</option>" +
                    "<option value='Administrative Expense'>Administrative Expense</option>"
                );

              }else if(val == "Income"){

                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Direct Income'>Direct Income</option><option value='Indirect Income'>Indirect Income</option>");

              }else if(val == "Liabilities"){

                  clearfield();
                  $("#sub_account_head").html(`
                    <option value=''>Please Select</option>
                    <option value='Current Liabilities'>Current Liabilities</option>
                    <option value='Long Term Liabilities'>Long Term Liabilities</option>
                    <option value='Account Payable'>Account Payable</option>
                    <option value='Short Term Liabilities'>Short Term Liabilities</option>
                `);


              }else if(val == "Equity"){

                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Equity Capital'>Equity Capital</option><option value='Retained Earnings'>Retained Earnings</option>");

              }else{
                
              }
          });
    }).change();
</script>

@endsection