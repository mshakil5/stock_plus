@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        @component('components.widget')
            @slot('title')
               Chart Of Accounts
                    <button class="btn btn-lg btn-success pull-right" data-toggle="modal"
                            data-target="#chartModal"
                            data-purpose="0">+
                        Add New Chart Of Account
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
                        <th>ID</th>
                        <th>Date</th>
                        <th>Accout Name</th>
                        <th>Account Head</th>
                        <th>Sub Account Head</th>
                        <th>Branch</th>
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
                        <label for="account_name" class="col-sm-3 control-label">Account Name</label>
                        <div class="col-sm-9">
                            <input type="account_name" name="account_name" class="form-control " id="account_name"
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
    
    var charturl = "{{URL::to('/admin/chart-of-account')}}";
    var customerTBL = $('#chartTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: charturl,
        deferRender: true,
        columns: [
            {data: 'id', name: 'id'},
            {data: 'date', name: 'date'},
            {data: 'account_name', name: 'account_name'},
            {data: 'account_head', name: 'account_head'},
            {data: 'sub_account_head', name: 'sub_account_head'},
            { data: 'branch_name', name: 'branch_name' },
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

    $(document).on('click', '.mstatus-btn', function () {
        let confirmation = confirm("Are you sure to change the Membership status?");
        if (confirmation) {
            let id = $(this).val();
            // console.log(id);
            $.ajax({
                url: '/customer/' + id + '/member-status',
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    showSnakBar('Membership Status Changed Successfully');
                    customerTBL.draw();
                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }
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
                    // console.log(response);
                    modal.find('#account_head').val(response.account_head);
                    modal.find('#sub_account_head').val(response.sub_account_head);
                    modal.find('#account_name').val(response.account_name);
                    modal.find('#description').val(response.description);
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
        // console.log(formData);
        $.ajax({
            url: charturl,
            type: 'POST',
            data: formData,
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                $('#chartModal').modal('toggle');
                showSnakBar('Created Successfully');
                customerTBL.draw();
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
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                $('#chartModal').modal('toggle');
                showSnakBar("Updated Successfully");
                customerTBL.draw();
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    });

</script>

<script>

    function clearfield() {
        $("#sub_account_head").val('').trigger('change');
        $("#sub_account_head").html("<option value=''>Please Select</option>");
    }

    $("#account_head").change(function(){
          $(this).find("option:selected").each(function(){
              var val = $(this).val();
              if( val == "Assets"){
                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Current Asset'>Current Asset</option><option value='Fixed Asset'>Fixed Asset</option>");

              } else if(val == "Expenses"){

                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Cost Of Good Sold'>Cost Of Good Sold</option><option value='Overhead Expense'>Overhead Expense</option>");

              }else if(val == "Income"){

                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Direct Income'>Direct Income</option><option value='Indirect Income'>Indirect Income</option>");

              }else if(val == "Liabilities"){

                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Current Liabilities'>Current Liabilities</option><option value='Long Term Liabilities'>Long Term Liabilities</option>");

              }else if(val == "Equity"){

                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Equity Capital'>Equity Capital</option><option value='Retained Earnings'>Retained Earnings</option>");

              }else{
                
              }
          });
  }).change();
</script>

@endsection