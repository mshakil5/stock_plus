@extends('admin.layouts.master')
@section('content')




<style>
/*.stock-alert{
  color: red;
  font-weight: bold;
}*/
.transferProduct .select2-container--default {
    width: 100% !important;
    text-align: left;
}

.transferProduct .row {
    text-align: center;
    margin: 10px 0px;
}

.stock-alert {
    font-weight: bold;
    animation-duration: 1200ms;
    animation-name: blink;
    animation-iteration-count: infinite;
    animation-direction: alternate;
    -webkit-animation: blink 1200ms infinite; /* Safari and Chrome */
}

@keyframes blink {
    from {
        background-color: red;
        color: white;
    }
    to {
        background-color: white;
        color: red;
    }
}

@-webkit-keyframes blink {
    from {
        background-color: red;
        color: white;
    }
    to {
        background-color: white;
        color: red;
    }
}
</style>
<!-- get stock alert limit quantity -->

{{-- @php $stockAlertLimit = App\StockAlertLimit::first() @endphp
@if($stockAlertLimit->status == 1)
<input type="hidden" name="stockAlertLimit" id="stockAlertLimit" value="{{ $stockAlertLimit->quantity }}">
@endif --}}


<div class="row">
<div class="col-md-12">
    <div class="box box-widget">
        <div class="box-body">
            <table id="stockTBL" class="table table-striped stckTbl">
                <thead>
                <tr>
                    <th><i class="icon-sort"></i>Date</th>
                    <th><i class="icon-sort"></i>Invoice No</th>
                    <th><i class="icon-sort"></i>Branch</th>
                    <th class="text-center"><i class="icon-sort"></i>Supplier</th>
                    <th class="text-center"><i class="icon-sort"></i>Transaction Type</th>
                    <th class="text-center"><i class="icon-sort"></i>Ref</th>
                    <th class="text-center"><i class="icon-sort"></i>Total Amount</th>
                    <th class="text-center"><i class="icon-sort"></i>Due Amount</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($purchase as $data)
                        <tr>
                            <td class="text-center">{{ $data->date }}</td>
                            <td class="text-center">{{ $data->invoiceno }}</td>
                            <td>{{ \App\Models\Branch::where('id',$data->branch_id)->first()->name }}</td>
                            <td class="text-center">{{ \App\Models\Vendor::where('id',$data->vendor_id)->first()->name }}</td>
                            <td class="text-center">{{ $data->purchase_type }}</td>
                            <td class="text-center">{{ $data->ref }}</td>
                            <td class="text-center">{{ $data->net_amount }}</td>
                            <td class="text-center">{{ $data->due_amount }}</td>
                            <td class="text-center">
                                <a class="btn btn-primary btn-sm btn-return" href="{{ route('admin.purchaseedit', $data->id )}}" > <i class='fa fa-pencil'></i> Edit
                                </a>
                                <a class="btn btn-primary btn-sm btn-return" href="{{ route('admin.purchasereturn', $data->id )}}" > <i class='fa fa-undo'></i> Return
                                </a>
                                <button class="btn btn-primary btn-sm btn-return" data-toggle="modal" data-target="#viewModal{{$data->id}}" > <i class='fa fa-eye'></i> View
                                </button>
                              
                                <!----------------------------ViewModal ------------------------->
                                <div class="modal fade transfer-modal" id="viewModal{{$data->id}}">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header alert alert-success" style="text-align: left;">
                                                <div>
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title">#Products Purchase Details</h4>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                                                            <ul id="myTabs" class="nav nav-tabs" role="tablist">
                                                                <li role="presentation" class="active"><a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">Product Details</a></li>                                     
                                                            </ul>
                                                            <div id="myTabContent" class="tab-content">
                                                                <div role="tabpanel" class="tab-pane fade active in" id="home"
                                                                    aria-labelledby="home-tab">
                                                                    &nbsp;
                                                                    @component('components.table')
                                                                        @slot('tableID')
                                                                            purchaseDtlTBL
                                                                        @endslot
                                                                        @slot('head')
                                                                            <th>Product Name</th>
                                                                            <th>Product Part#</th>
                                                                            <th>Quantity</th>
                                                                            <th>Price</th>
                                                                            <th>Total Vat</th>
                                                                            <th>Net Total</th>
                                                                        @endslot

                                                                        @slot('body')
                                                                        @foreach ($data->purchasehistory as $key => $purchasedtl)
                                                                            
                                                                        <tr>
                                                                            <td>{{ $purchasedtl->product->productname}} </td>
                                                                            <td>{{ $purchasedtl->product->part_no}} </td>
                                                                            <td>{{ $purchasedtl->quantity}}</td>
                                                                            <td>{{ $purchasedtl->purchase_price}}</td>
                                                                            <td>{{ $purchasedtl->total_vat}}</td>
                                                                            <td>{{ $purchasedtl->total_amount_with_vat}}</td>
                                                                        </tr>

                                                                        @endforeach
                                                                        
                                                                        @endslot

                                                                    @endcomponent
                                                                </div>
                                                                
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="card bg-light mb-3">
                                                            <div class="card-header">Summery</div>
                                                            <div class="card-body">

                                                                <table class="table table-responsive table-hover">
                                                                    <tbody>

                                                                        <tr>
                                                                            <td>Item Total Amount</td>
                                                                            <td>{{$data->total_amount }}</td>
                                                                        </tr>
                                                                        
                                                                        <tr>
                                                                            <td>VAT</td>
                                                                            <td>{{$data->total_vat_amount }}</td>
                                                                        </tr>
                                                                        
                                                                        <tr>
                                                                            <td>Discount</td>
                                                                            <td>{{$data->discount }}</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>Net Amount</td>
                                                                            <td>{{$data->net_amount }}</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>Paid Amount</td>
                                                                            <td>{{$data->paid_amount }}</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>Due Amount</td>
                                                                            <td>{{$data->due_amount }}</td>
                                                                        </tr>


                                                                    </tbody>
                                                                </table>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                </div>
                                 <!----------------------------ViewModal END ------------------------->

                            </td>
                        </tr>
                    @endforeach
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
$(document).ready(function () {
   stockAlert();
   $('#stockTBL').on( 'page.dt', function () {
       stockAlert();
   }).dataTable({
        "order": [[ 0, "DESC" ]],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print',
                className: 'btn btn-md',
                exportOptions: {
                    columns: [0, 1, 2, 3,4]
                }
            },
            {
                extend: 'csv',
                className: 'btn btn-md',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                className: 'btn btn-md',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
        ]
   });
});



function emptyTableData() {
    var table = $(".display tbody");
    table.empty();
}

$(".allownumericwithoutdecimal").on("keypress keyup blur", function (event) {
    $(this).val($(this).val().replace(/[^\d].+/, ""));
    if ((event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});



$('#transferModal').on('show.bs.modal', function (event) {
    var modal = $(this)
    modal.find('.modal-body input').val("");
});


$(document).on('click', '.btn-return', function () {
    let phistryid = $(this).val();
    branchid = $(this).attr('bid');
    productid = $(this).attr('pid');
    pname = $(this).attr('pname');
    purchaseqty = $(this).attr('tstock');
    pcode = $(this).attr('pcode');

    $('#transferModal').find('.modal-body #productname').val(pname);
    $('#transferModal').find('.modal-body #productid').val(productid);
    $('#transferModal').find('.modal-body #purchaseqty').val(purchaseqty);
    $('#transferModal').find('.modal-body #phistryid').val(phistryid);
    $('#transferModal').find('.modal-body #branchid').val(branchid);
    $('#transferModal').find('.modal-body #pcode').val(pcode);
});

        var returnurl = "{{URL::to('/admin/save-product-return')}}";

        function saveTransfer() {
            if ($('#returnQty').val() == "") {
                alert("Please input quantity to return!");
            } else if ($('#reason').val() == "") {
                alert("Please input Reason field to return!");
            } else if (Number($('#returnQty').val()) <= 0) {
                alert("Quantity should not be zero/negative value!");
            } else if (Number($('#returnQty').val()) > Number($('#purchaseqty').val())) {
                alert("You have not sufficient stock!");
            } else {
                let data = {
                    productid: $('#productid').val(),
                    phistryid: $('#phistryid').val(),
                    branchid: $('#branchid').val(),
                    purchaseqty: $('#purchaseqty').val(),
                    returnQty: $('#returnQty').val(),
                    reason: $('#reason').val(),
                };
                
                $.ajax({
                    data: {
                        data: data
                    },
                    url: returnurl,
                    type: 'POST',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        if (response.status == 303) {
                            $(".ermsg").html(response.message);
                        }else if(response.status == 300){
                            console.log(response.message);
                            $(".ermsg").html(response.message);
                            $("#returnQty").val("");
                            window.setTimeout(function(){location.reload()},2000)
                        }
                    },
                    error: function (data) {
                        var errors = data.responseJSON;
                        $(".ermsg").html(data.message);
                    }

                });
            }
        }


function stockAlert() {
    var stckQ = document.getElementsByClassName("stockQuantity");
    var limit = $('#stockAlertLimit').val();
    //alert(limit);
    for (var i = 0; i < stckQ.length; i++) {
        var stq = stckQ[i].innerHTML;

        if (Number(stq) <= Number(limit)) {
            //alert("Your stock is below limit. Please check your stock!");
            //document.getElementById('lowStock').play();
            $(stckQ[i]).addClass("stock-alert");

        } else {
            //$(stckQ).removeClass( "alert alert-danger" );

        }
    }
    
}
</script>



@endsection
    
@section('script')

@endsection