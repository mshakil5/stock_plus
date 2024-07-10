@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div id="alert-container"></div>
        @component('components.widget')
            @slot('title')
               Equity
                    <button class="btn btn-lg btn-success pull-right" data-toggle="modal"
                            data-target="#chartModal"
                            data-purpose="0">+
                        Add New
                    </button>
            @endslot
            @slot('description')

            @endslot
            @slot('body')
                @component('components.table')
                    @slot('tableID')
                        expenseTBL
                    @endslot
                    @slot('head')
                        <th>ID</th>
                        <th>Date</th>
                        <th>Account</th>
                        <th>Ref</th>
                        <th>Description</th>
                        <th>Transaction Type</th>
                        <th>Payment Type</th>
                        <th>Gross Amount</th>
                        <th>Tax Rate</th>
                        <th>Tax Amount</th>
                        <th>Net Amount</th>
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
                <h4 class="modal-title">Equity</h4>
            </div>
            <form class="form-horizontal" id="customer-form">
                <div class="modal-body">
                    {{csrf_field()}}
                    
                    <div class="form-group">
                        <label for="date" class="col-sm-3 control-label">Date</label>
                        <div class="col-sm-9">
                            <input type="date" name="date" class="form-control " id="date" value="{{date('Y-m-d')}}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="chart_of_account_id" class="col-sm-3 control-label">Chart of Account</label>
                            <div class="col-sm-9">
                                <select class="form-control" id="chart_of_account_id" name="chart_of_account_id">
                                    <option value="">Select chart of account</option>
                                    @php
                                        use App\Models\ChartOfAccount;
                                        $accounts = ChartOfAccount::where('sub_account_head', 'Account Receivable')->get(['account_name', 'id']);
                                        $assets = ChartOfAccount::where('account_head', 'Equity')->get();
                                    @endphp
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}">{{ $asset->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                    </div>

                    <div class="form-group">
                        <label for="ref" class="col-sm-3 control-label">Reference</label>
                        <div class="col-sm-9">
                            <input type="text" name="ref" class="form-control " id="ref">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="transaction_type" class="col-sm-3 control-label">Transaction Type</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="transaction_type" name="transaction_type">
                                <option value="">Select transaction type</option>
                                <option value="Payment">Payment</option>
                                <option value="Purchase">Purchase</option>
                                <option value="Sold">Sold</option>
                                <option value="Depreciation">Depreciation</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="col-sm-3 control-label">Amount</label>
                        <div class="col-sm-9">
                            <input type="text" name="amount" class="form-control " id="amount">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tax_rate" class="col-sm-3 control-label">Tax %</label>
                        <div class="col-sm-9">
                            <input type="text" name="tax_rate" class="form-control " id="tax_rate">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tax_amount" class="col-sm-3 control-label">Tax Amount</label>
                        <div class="col-sm-9">
                            <input type="text" name="tax_amount" class="form-control " id="tax_amount">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="at_amount" class="col-sm-3 control-label">Total Amount</label>
                        <div class="col-sm-9">
                            <input type="text" name="at_amount" class="form-control " id="at_amount">
                        </div>
                    </div>

                    <div class="form-group" id="payment_type_container">
                        <label for="payment_type" class="col-sm-3 control-label">Payment Type</label>
                            <div class="col-sm-9">
                                <select class="form-control" id="payment_type" name="payment_type">
                                    <option value="">Select payment type</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank">Bank</option>
                                </select>
                            </div>
                    </div>

                    <div class="form-group d-none" id="showpayable" >
                        <label for="" class="col-sm-3 control-label">Payable Holder Name</label>
                        <div class="col-sm-9">

                        <select class="form-control" id="payable_holder_id" name="payable_holder_id">
                            <option value="">Select payable holder</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                            @endforeach
                        </select>

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

<!-- Payable holder id -->
<script>
    $(document).ready(function() {
        $("#transaction_type").change(function () {
            var transaction_type = $(this).val();
            if (transaction_type == "Due") {
                $("#payment_type").html("<option value=''>Please Select</option><option value='Account Payable'>Account Payable</option>");
            } else if (transaction_type == "Current") {
                $("#showpayable").hide();
                $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                clearPayableHolder();
            } else if (transaction_type == "Payment") {
                $("#showpayable").hide();
                $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                clearPayableHolder();
            } else if (transaction_type == "Prepaid") {
                $("#showpayable").hide();
                $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                clearPayableHolder();
            } else if (transaction_type == "Prepaid Adjust") {
                clearTaxPaymentTypefield();
                $("#showpayable").hide();
                $("#payment_type").html("<option value=''>Please Select</option>");
                clearPayableHolder();
            }
        });

        $("#payment_type").change(function(){
            $(this).find("option:selected").each(function(){
                var val = $(this).val();
                if( val == "Account Payable" ){
                    $("#showpayable").show();
                } else{
                    $("#showpayable").hide();
                    clearPayableHolder();
                }
            });
        }).change();

        function clearPayableHolder() {
            $("#payable_holder_id").val('');
        }
    });
</script>

<!-- Amount and tax rate calculation -->
<script>
    function calculateTotal() {
        var amount = parseFloat(document.getElementById('amount').value) || 0;
        var taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;

        var taxAmount = amount * (taxRate / 100);
        document.getElementById('tax_amount').value = taxAmount.toFixed(2);

        var totalAmount = amount + taxAmount;
        document.getElementById('at_amount').value = totalAmount.toFixed(2);
    }

    document.getElementById('amount').addEventListener('input', calculateTotal);
    document.getElementById('tax_rate').addEventListener('input', calculateTotal);

    calculateTotal();
</script>

<!-- Main script -->
<script>

    var charturl = "{{URL::to('/admin/equity')}}";
    var customerTBL = $('#expenseTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
        url: charturl,
        type: 'GET',
        error: function (xhr, error, thrown) {
            console.log(xhr.responseText);
        }
        },
        deferRender: true,
        columns: [
            {data: 'id', name: 'id'},
            {data: 'date', name: 'date'},
            {data: 'chart_of_account', name: 'chart_of_account'},
            {data: 'ref', name: 'ref'},
            {data: 'description', name: 'description'},
            {data: 'transaction_type', name: 'transaction_type'},
            {data: 'payment_type', name: 'payment_type'},
            {data: 'amount', name: 'amount'},
            {data: 'tax_rate', name: 'tax_rate'},
            {data: 'tax_amount', name: 'tax_amount'},
            {data: 'at_amount', name: 'at_amount'},
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

    // modal

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
                    $('#date').val(response.date);
                    $('#ref').val(response.ref);
                    $('#transaction_type').val(response.transaction_type);
                    $('#amount').val(response.amount);
                    $('#tax_rate').val(response.tax_rate);
                    $('#tax_amount').val(response.tax_amount);
                    $('#at_amount').val(response.at_amount);
                    $('#payment_type').val(response.payment_type);
                    $('#description').val(response.description);

                    $('#chart_of_account_id').val(response.chart_of_account_id);

                    if (response.payment_type == 'Account Payable') {
                        $('#showpayable').show();
                        $("#payment_type").html("<option value=''>Please Select</option><option selected value='Account Payable'>Account Payable</option>")
                        
                    } else {
                        $("#payment_type").html("<option value=''>Please Select</option>" + "<option value='Cash'>Cash</option>" + "<option value='Bank'>Bank</option>");
                        $('#payment_type').val(response.payment_type);
                        $('#showpayable').hide();     
                    }

                    var payableHolderId = response.payable_holder_id;
                    $('#payable_holder_id').val(payableHolderId);

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
        let formDataSerialized = $('#customer-form').serializeArray();
        formDataSerialized.push({ name: 'table_type', value: 'Expenses' });
        let formData = $.param(formDataSerialized);
        console.log(formData);


        $.ajax({
            url: 'charturl',
            type: 'POST',
            data: formData,
            beforeSend: function (request) {
                request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                console.log(response);
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
        console.log(id);
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
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

</script>

<!-- Main script -->

<script>
    $(document).ready(function() {
        $('#chartModal').on('hidden.bs.modal', function (e) {
            $('#payment_type').val('');
            $('#payment_type_container').show();
        });
    });
</script>


@endsection