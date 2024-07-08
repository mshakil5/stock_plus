@extends('admin.layouts.master')
@section('content')



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
<div class="conainer-fluid">
    <div class="col-md-12">
        <div class="overview">
            <div id="ermsg" class="ermsg"></div>
        </div>
    </div>
</div>
</div>
<div class="row">
<div class="col-md-12">
    <div class="box box-widget">
        <div class="box-body">
            <table id="stockTBL" class="table table-striped stckTbl">
                <thead>
                <tr>
                    <th><i class="icon-sort"></i>ID</th>
                    <th><i class="icon-sort"></i>Product</th>
                    <th><i class="icon-sort"></i>Requested Date</th>
                    <th class="text-center"><i class="icon-sort"></i>Request From Branch</th>
                    <th class="text-center"><i class="icon-sort"></i>Request To Branch</th>
                    <th class="text-center"><i class="icon-sort"></i>Request Quantity</th>
                    <th class="text-center"><i class="icon-sort"></i>Request User Name</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $item)
                        <tr>
                            <td>
                                {{ $key + 1 }}
                            </td>
                            <td>{{ $item->product->productname }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td class="text-center">{{ \App\Models\Branch::where('id','=', $item->from_branch_id)->first()->name }}</td>
                            <td class="text-center">{{ \App\Models\Branch::where('id','=', $item->to_branch_id)->first()->name }}</td>
                            <td class="text-center stockQuantity">{{ $item->requestqty }}</td>
                            <td class="text-center">{{  \App\Models\User::where('id','=', $item->created_by)->first()->name  }}</td>
                            <td class="text-center">
                                @if ($item->status == 1)
                                    <button class="btn btn-success">Stock Transferred</button>
                                @else
                                    <button class="btn btn-primary btn-sm btn-transfer" data-toggle="modal"
                                            data-target="#transferModal" pname="{{$item->product->productname}}" frombid="{{$item->from_branch_id}}" frombname="{{ \App\Models\Branch::where('id','=', $item->from_branch_id)->first()->name }}" tobid="{{$item->to_branch_id}}" tobname="{{ \App\Models\Branch::where('id','=', $item->to_branch_id)->first()->name }}" pid="{{ $item->product_id }}" tstock="{{ $item->requestqty }}" value="{{$item->id}}">
                                        <i class="fa fa-arrow-up"></i> Transfer
                                    </button>
                                @endif
                                    
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>


        </div>
    </div>
</div>
</div>



<!----------------------------TransferModal ------------------------->
<div class="modal fade transfer-modal" id="transferModal">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header alert alert-success" style="text-align: left;">
            <div>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">#Transfer Products</h4>
            </div>
        </div>
        <div class="modal-body transferProduct">
            <div class="row">
                
            <div id="ermsg" class="ermsg"></div>
                <div class="col-sm-6">
                    <label for="productname">Product Name</label>
                    <input type="text" name="productname" id="productname" class="form-control text-center"
                           readonly="">
                    <input type="hidden" name="productid" id="productid">
                    <input type="hidden" name="tranReqid" id="tranReqid">
                    <input type="hidden" name="frombranchid" id="frombranchid">
                    <input type="hidden" name="tobranchid" id="tobranchid">
                </div>
                <div class="col-sm-6">
                    <label for="stockQuantity">Reqest Stock QTY</label>
                    <input type="text" name="stockQuantity" id="stockQuantity" class="form-control text-center"
                           readonly="">
                </div>
                {{-- <div class="col-sm-3">
                    <label for="branch">Belongs Branch</label>
                    <input type="text" id="belongsBranchName" class="form-control text-center" readonly="">
                    <input type="hidden" name="belongsBranchId" id="belongsBranchId">
                </div> --}}
            </div>
            <hr>
            <div class="row text-left tValues">
                <div class="col-sm-8 col-sm-offset-2">
                    
                    <div class="row">
                        <div class="col-sm-4 text-left">
                            <label for="frombname">Request From Branch: </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="frombname" id="frombname" style="width: 100%;" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 text-left">
                            <label for="brnachToTransfer">Request To Branch: </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="tobname" id="tobname" style="width: 100%;" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 text-left">
                            <label for="transferQty">Transfer Quantity: </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="number" class="form-control allownumericwithoutdecimal"
                                   name="transferQty" id="transferQty" style="width: 100%;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-success" onclick="saveTransfer()"><i
                                class="fa fa-arrow-up"></i> Transfer
                    </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
        "order": [[ 3, "asc" ]],
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

function manageStockPurchaseDetails($barcodeid) {
    let purchaseDetails = null;
    var table = $(".display tbody");
    $.ajax({
        url: '/manage-stock-purchase-details/' + $barcodeid,
        data: {},
        type: 'GET',
        xhrFields: {
            withCredentials: true
        },
        beforeSend: function (request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
        },
        success: function (data) {
            //console.log(data);

            $.each(data, function (a, b) {
                //table.empty();
                table.append("<tr><td class='text-left'>" + `${moment(b.created_at).format('DD MMMM YYYY hh:mm A')}` + "</td>" +
                    "<td class='text-success text-left'>" + "Purchased" + "</td>" +
                    "<td class='text-left'>" + '<a target="_blank" title="Show Invoice Detail" href="/purchase-invoice/'+ b.get_product_purchase_record[0].purchaseid +'/details">' + "Inv-" + b.get_product_purchase_record[0].purchaseid + "</a></td>" +
                    "<td class='text-center'>" + b.qty + "</td>" +
                    "<td class='text-center'>" + b.purchaseprice + "</td>" +
                    "<td class='text-left'>" + b.products.productname +"(" + b.products.productid + ")" + "</td>" +
                    "<td class='text-left'>" + b.barcodeid + "</td>" +
                    "</tr>");
            });
            $(".display").DataTable();
            //console.log(data);
        },
        error: function (err) {
            console.log(err);
            alert("Something Went Wrong, Please check again");
        }
    });

    return purchaseDetails;
}

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

$(document).on('click', '.btn-transfer', function () {
    let tranReqid = $(this).val();
    frombranchid = $(this).attr('frombid');
    tobranchid = $(this).attr('tobid');

    frombname = $(this).attr('frombname');
    tobname = $(this).attr('tobname');

    productid = $(this).attr('pid');
    pname = $(this).attr('pname');
    reqstock = $(this).attr('tstock');
    $('#transferModal').find('.modal-body #productname').val(pname);
    $('#transferModal').find('.modal-body #productid').val(productid);
    $('#transferModal').find('.modal-body #stockQuantity, #transferQty').val(reqstock);
    $('#transferModal').find('.modal-body #tranReqid').val(tranReqid);
    $('#transferModal').find('.modal-body #frombranchid').val(frombranchid);
    $('#transferModal').find('.modal-body #frombname').val(frombname);
    $('#transferModal').find('.modal-body #tobranchid').val(tobranchid);
    $('#transferModal').find('.modal-body #tobname').val(tobname);
});

$('#transferModal').on('show.bs.modal', function (event) {
    var modal = $(this)
    modal.find('.modal-body input').val("");
    // modal.find('.modal-body #brnachToTransfer').select2("").trigger('change');
});

        var tranurl = "{{URL::to('/admin/save-stock-transfer')}}";

        function saveTransfer() {
            if ($('#transferQty').val() == "") {
                alert("Please input quantity to stock transfer!");
            } else if (Number($('#transferQty').val()) <= 0) {
                alert("Quantity should not be zero/negative value!");
            } else {
                let data = {
                    productid: $('#productid').val(),
                    tranReqid: $('#tranReqid').val(),
                    reqfrombranchid: $('#frombranchid').val(),
                    reqtobranchid: $('#tobranchid').val(),
                    transferQty: $('#transferQty').val(),
                };

                console.log(data);
                
                $.ajax({
                    data: {
                        data: data
                    },
                    url: tranurl,
                    type: 'POST',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        if (response.status == 303) {
                            $(".ermsg").html(response.message);
                        }else if(response.status == 300){
                            $(".ermsg").html(response.message);
                            $("#brnachToTransfer").val("");
                            $("#transferQty").val("");
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

// calculation start 
// $("#transferQty").keyup(function(){
//     var qtyInput = this.value;
//     var totalstockqty = $("#stockQuantity").val();
//     if (qtyInput > totalstockqty) {
//         alert("There are not available quantity to transfer");
//         $('#stockQuantity').val(totalstockqty);
//         $('input[name=transferQty').val('');
//     }
// });
//calculation end




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