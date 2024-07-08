@extends('admin.layouts.master')
@section('content')


<?php
$user_id = Session::get('categoryEmployId');
$branch_id = Session::get('brnach_id');
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
    <div class="col-md-12">
        @component('components.widget')
            @slot('title')
                Manage Customer
                    <button class="btn btn-lg btn-success pull-right" data-toggle="modal"
                            data-target="#customerModal"
                            data-purpose="0">+
                        Add New Customer
                    </button>
            @endslot
            @slot('description')
                Customer information
            @endslot
            @slot('body')
                @component('components.table')
                    @slot('tableID')
                        customerTBL
                    @endslot
                    @slot('head')
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Vehicle no</th>
                        <th>Vat Number</th>
                        <th>Limitation</th>
                        <th>Membership ID</th>
                        <th>Status</th>
                        <th><i class=""></i> Action</th>
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>
</div>


<div class="modal fade" id="customerModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Customer Details</h4>
            </div>
            <form class="form-horizontal" id="customer-form">
                <div class="modal-body">
                    {{csrf_field()}}

                    <div class="form-group">
                        <label for="member_id" class="col-sm-3 control-label">Member ID</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="member_id" name="member_id"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label">Name</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="form-control" id="name"
                                   placeholder="ex. John Doe" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" class="form-control " id="email"
                                   placeholder="ex. test@gmail.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="col-sm-3 control-label">Phone</label>
                        <div class="col-sm-9">
                            <input type="text" name="phone" class="form-control " id="phone"
                                   placeholder="ex. 0123456789">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-3 control-label">Address</label>
                        <div class="col-sm-9">
                                <textarea class="form-control" id="address" rows="3" placeholder="1355 Market Street, Suite 900 San Francisco, CA 94103 P: (123) 456-7890" name="address"></textarea>
                        </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label">Vehicle No</label>
                        <div class="col-sm-9">
                            <input type="text" name="vehicleno" class="form-control" id="vehicleno" placeholder="ex. 012586" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vat_number" class="col-sm-3 control-label">Vat Number</label>
                        <div class="col-sm-9">
                            <input type="text" name="vat_number" class="form-control" id="vat_number" placeholder="ex. 012586">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="limitation" class="col-sm-3 control-label">Credit Limitation</label>
                        <div class="col-sm-9">
                            <input type="text" name="limitation" class="form-control" id="limitation" >
                        </div>
                    </div>

                    
                    <div class="form-group">
                        <label for="type" class="col-sm-3 control-label">Type</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="type">
                                <option value="0">Customer</option>
                                <option value="1">Distributor</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary submit-btn save-btn"> Save</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- Modal -->
<div class="modal fade" id="customerInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Invoices</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @component('components.table')
                    @slot('tableID')
                        customerInvoiceTBL
                    @endslot
                    @slot('head')
                        <th>ID</th>
                        <th>Qty</th>
                        <th>Total(Exc. others)</th>
                        <th>VAT</th>
                        <th>Offer</th>
                        <th>Discount</th>
                        <th>Rebate</th>
                        <th>Due</th>
                        <th>Action</th>
                    @endslot
                @endcomponent
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    
    var customerurl = "{{URL::to('/admin/customers')}}";
    var customerTBL = $('#customerTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: customerurl,
        deferRender: true,
        // searching:false,
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {
                data: 'type', name: 'type', render: function (data, type, row, meta) {
                    return (row.type) ? "Distributor" : "Customer";
                }
            },
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'address', name: 'address'},
            {data: 'vehicleno', name: 'vehicleno'},
            {data: 'vat_number', name: 'vat_number'},
            {data: 'limitation', name: 'limitation'},
            {data: 'member_id', name: 'member_id'},
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
                    let button = `<button type="button" class="btn btn-warning btn-xs edit-btn" data-toggle="modal" data-target="#customerModal" value="${row.id}" title="Edit" data-purpose='1'><i class="fa fa-edit" aria-hidden="true"></i> Edit</button>`;
                    if (row.amount < 0) {
                        // button += `<button type="button" class="btn btn-success btn-xs omit-btn" value="${row.id}" title="Omitting Due Amount"><i class="fa fa-heart" aria-hidden="true"></i> Ommit Due</button>`;
                    }
                    return button;
                }
            },
        ]
    });

    // setInterval(customer_load,10000);

    $(document).on('click', '.mstatus-btn', function () {
        let confirmation = confirm("Are you sure to change the Membership status?");
        if (confirmation) {
            let id = $(this).val();
            console.log(id);
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
            console.log(id);
            $.ajax({
                url: customerurl + '/' + id + '/change-status',
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

    $(document).on('click', '.omit-btn', function () {
        let confirmation = confirm("Are you sure to change the status?");
        if (confirmation) {
            let id = $(this).val();
            console.log(id);
            $.ajax({
                url: '/customer/' + id + '/due-omit',
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    showSnakBar('Due successfully Cleared');
                    customerTBL.draw();
                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }
    });

    $('#customerModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        let purpose = button.data('purpose');
        var modal = $(this);
        if (purpose) {
            let id = button.val();
            $.ajax({
                url: customerurl +'/' + id,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    modal.find('#member_id').val(response.member_id);
                    modal.find('#name').val(response.customername);
                    modal.find('#email').val(response.email);
                    modal.find('#phone').val(response.phone);
                    modal.find('#address').text(response.address);
                    modal.find('#vehicleno').val(response.vehicleno);
                    modal.find('#limitation').val(response.limitation);
                    modal.find('#vat_number').val(response.vat_number);
                    
                    modal.find("[name=type]").val(response.type);

                    // let data = new Date(response.birthday)
                    // let year = data.getFullYear();
                    // let month = (1 + data.getMonth()).toString().padStart(2, '0');
                    // let day = data.getDate().toString().padStart(2, '0');

                    // date = month + '/' + day + '/' + +year;
                    

                    $('#customerModal .submit-btn').removeClass('save-btn').addClass('update-btn').text('Update').val(response.id);
                }
            });
        } else {
            $('#customer-form').trigger('reset');
            $('#customer-form textarea').text('');
            $('#customerModal .submit-btn').removeClass('update-btn').addClass('save-btn').text('Save').val("");
        }
    });

    $('#customerInvoiceModal').on('show.bs.modal', function (event) {
        let id = $(event.relatedTarget).val();
        $.ajax({
            url: '/customer/' + id + '/invoices',
            type: 'GET',
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                let ctp = $('#customerInvoiceTBL').DataTable();
                ctp.clear().draw(true);
                $.each(response.invoices, function (i, invoice) {
                    let button = `<button type="button" class="btn btn-primary btn-xs view-btn" data-toggle="modal" data-target="#product-details" value="${invoice.invoiceid}"><i class="fa fa-eye" aria-hidden="true"></i> View</button>`;
                    due = invoice.due;
                    if (due > 0) {
                        if (invoice.due_omitted) {
                            due = `<del>${due}</del>`;
                        }
                        due = `<span class="label-danger"  title="Due Omitted"> ${due} </span>`;
                    }
                    ctp.row.add([
                        invoice.invoiceid,
                        invoice.qty,
                        invoice.totalamount,
                        invoice.vatamount,
                        invoice.offeramount,
                        invoice.discount,
                        invoice.rebate,
                        due,
                        button
                    ]).draw(true);
                });
            }
        });
    });
    // save button event

    $(document).on('click', '.save-btn', function () {
        let formData = $('#customer-form').serialize();
        // console.log(customerurl);
        $.ajax({
            url: customerurl,
            type: 'POST',
            data: formData,
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                $('#customerModal').modal('toggle');
                showSnakBar('Added Successfully');
                customerTBL.draw();
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    });

    // update button event

    $(document).on('click', '.update-btn', function () {
        let formData = $('#customer-form').serialize();
        let id = $(this).val();
        $.ajax({
            url: customerurl + '/' + id,
            type: 'PUT',
            data: formData,
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                $('#customerModal').modal('toggle');
                showSnakBar("Updated Successfully");
                customerTBL.draw();
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    });

    // Deposit event
    $(document).on('click', ' .deposite-btn', function () {
        let id = $(this).val();
        let confirmation = confirm("Are you sure to deposite an amount?");
        if (confirmation) {
            let amount = prompt("How much do you want to deposite?");
            amount = parseInt(amount);
            if(!amount){
                return ;
            }
            $.ajax({
                url: '/customer/' + id + '/deposite',
                type: 'POST',
                data: {'amount': amount},
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    showSnakBar('Deposited Successfully');
                    customerTBL.draw();
                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }
    });
</script>






@endsection
    
@section('script')


@endsection