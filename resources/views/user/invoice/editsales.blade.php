@extends('user.layouts.master')
@section('content')
@php
    $paymentMethod = \App\Models\PaymentMethod::where('status','=', 1)->get();
@endphp

<div class="inner ">
    <div class="content w-90 mx-auto">
        <div class="row gx-2">
            
            <div id="ermsg" class="ermsg"></div>
            <div class="col-lg-4 ">
                
                <div class="box">
                    <form action="">
                        <div class="row">
                            <p  class="box-title" >Delivery Note</p>
                            <div class="row">
                                <div class="col-lg-7 ">

                                    <div class="form-group mb-3 mx-1 flex-fill">
                                        <label for="">Sales Type</label>
                                        <input type="hidden" id="order_id" value="{{ $invoices->id }}">
                                        <select name="salestype" id="salestype" class="form-control input-sm">
                                            <option value="Cash" @if ( $invoices->salestype == "Cash" ) selected @endif>Cash</option>
                                            <option value="Credit" @if ( $invoices->salestype == "Credit" ) selected @endif>Credit</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 mx-1 flex-fill">
                                        <label for="">Product</label>
                                        <select name="product" id="product" class="form-control input-sm selectproduct">
                                            <option value="">Please select</option>
                                            @foreach (\App\Models\Product::select('id','productname','part_no')->get() as $product)
                                            <option value="{{$product->id}}">{{$product->productname}}-{{$product->part_no}}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>

                                </div>
                                <div class="col-lg-5 ">
                                    <div class="form-group mb-3 mx-1 flex-fill">
                                        <label for="">Product Name: <span id="proname" class="btn btn-theme"></span> </label>
                                    </div>
                                    <div class="form-group mb-3 mx-1 flex-fill">
                                        <label for="">Stock Availibility: <span id="availablestock" class="btn btn-theme"></span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                </form>


            </div>
            <div class="col-lg-3 ">
                <form action="">
                    <div class="row gx-2">
                        <div class="col-lg-12 pt-2">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="client" data-bs-toggle="tab"
                                        data-bs-target="#client-pane" type="button" role="tab" style="font-size:15px"
                                        aria-controls="client-pane" aria-selected="true">Customer</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="contact" data-bs-toggle="tab"
                                        data-bs-target="#contact-tab-pane" type="button" role="tab"
                                        aria-controls="contact-tab-pane" style="font-size:15px"
                                        aria-selected="false">Address</button>
                                </li>
                                
                                <li class="nav-item" role="presentation">
                                    <a href="#" class="btn btn-theme mt-2" data-bs-toggle="modal" data-bs-target="#exampleModal">Add New</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="client-pane" role="tabpanel" aria-labelledby="client" tabindex="0">
                                    <div class="box mb-0">
                                        <div class="row">

                                            <div class="col-lg-12">
                                                <div class="form-group mx-1">
                                                    <label for=""> Vehicle No</label>
                                                    <input type="text" id="showcustomervehicleno" class="form-control " value="{{ $invoices->customer->vehicleno }}">
                                                </div>
                                                <div class="form-group mx-1">
                                                    <label for=""> Name</label>
                                                    <input type="text" id="showcustomername" class="form-control " value="{{ $invoices->customer->name }}">
                                                    <input type="hidden" id="customer_id"  name="customer_id" value="{{ $invoices->customer_id }}">
                                                </div>

                                                <div class="form-group mx-1">
                                                    <label for="">Select Customer</label>
                                                    <select name="customers" id="customers" class="form-control selectcustomer">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="address-tab" tabindex="0">
                                    <div class="box mb-0">
                                        <div class="row">

                                            <div class="col-lg-12">
                                                <div class="form-group mx-1">
                                                    <label for="">Email</label>
                                                    <input type="text" id="showcustomeremail" class="form-control" value="{{ $invoices->customer->email }}">
                                                </div>
                                                <div class="form-group mx-1">
                                                    <label for="">Address</label>
                                                    <input type="text" id="showcustomeraddress" class="form-control "value="{{ $invoices->customer->address }}">
                                                </div>
                                                
                                                <div class="form-group mx-1">
                                                    <label for="">Vat No</label>
                                                    <input type="text" id="showcustomervat" class="form-control " value="{{ $invoices->customer->vat_number }}">
                                                </div>
                                                <div class="form-group mx-1">
                                                    <label for=""> Previous Due</label>
                                                    <input type="text" id="showcustomerdue" class="form-control " readonly value="{{ $invoices->customer->amount }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>

            </div>

            <div class="col-lg-2 ">
                
                <div class="box mb-0">
                    <p class="box-title"> Invoice Details</p>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mx-1">
                                <label for="">Invoice Date</label>
                                <input type="date" id="orderdate" name="orderdate" class="form-control " value="{{ $invoices->orderdate }}">
                            </div>
                            <div class="form-group mx-1">
                                <label for="">Due Date</label>
                                <input type="date" class="form-control " id="due_date" name="due_date" {{ $invoices->due_date }}>
                            </div>
                            <div class="form-group mx-1">
                                <label for="">Reference</label>
                                <input type="text" id="ref" class="form-control " placeholder="Refecence" value="{{ $invoices->ref }}">
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <div class="col-lg-3 ">
                <form action="">
                    <div class="row gx-2">
                        <div class="col-lg-12 pt-2">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="allalternatives" data-bs-toggle="tab"
                                        data-bs-target="#allalternatives-pane" type="button" role="tab"
                                        aria-controls="allalternatives-pane" aria-selected="true" style="font-size:15px">Alternatives</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="allreplacement" data-bs-toggle="tab"
                                        data-bs-target="#allreplacement-tab-pane" type="button" role="tab"
                                        aria-controls="allreplacement-tab-pane" style="font-size:15px"
                                        aria-selected="false">Replacement</button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="branchstock" data-bs-toggle="tab"
                                        data-bs-target="#branchstock-tab-pane" type="button" role="tab"
                                        aria-controls="branchstock-tab-pane" style="font-size:15px"
                                        aria-selected="false">Stock</button>
                                </li>
                                
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="allalternatives-pane" role="tabpanel" aria-labelledby="allalternatives" tabindex="0">
                                    <div class="box mb-0">
                                        <div class="row">
                                            <div class="col-lg-12 ">
                                                <table class="table table-striped table-hover altertable">
                                                    <thead>
                                                        <tr>
                                                            <td>Name</td>
                                                            <td>Part No</td>
                                                            <td>Location</td>
                                                            <td>Price</td>
                                                        </tr>
                                                    </thead>                                   
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="allreplacement-tab-pane" role="tabpanel" aria-labelledby="address-tab" tabindex="0">
                                    <div class="box mb-0">
                                        <div class="row">
                                            <div class="col-lg-12 ">
                                                <table class="table table-striped table-hover replacetable">
                                                    <thead>
                                                        <tr>
                                                            <td>Replacement</td>
                                                        </tr>
                                                    </thead>                                   
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="branchstock-tab-pane" role="tabpanel" aria-labelledby="address-tab" tabindex="0">
                                    <div class="box mb-0">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="stockreqermsg"></div>
                                                <table class="table table-striped table-hover stocktable">
                                                    <thead>
                                                        <tr>
                                                            <td>Branch</td>
                                                            <td>Quantity</td>
                                                            <td class="text-center">Action</td>
                                                        </tr>
                                                    </thead>                                   
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div> 
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </form>

            </div>
            
            
        </div>

        <div class="row mx-auto">
            <div class="box">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <td>Part No</td>
                            <td>Product</td>
                            <td>Location</td>
                            <td>Quantity</td>
                            <td>Unit Price</td>
                            <td>Total</td> 
                            <td>Action</td>
                        </tr>
                    </thead>                                   
                    <tbody id="inner">
                        @foreach ($invoices->orderdetails  as $salesdetail)
                            <tr class="item-row pdetails" style="position:realative;">
                                <td>
                                    <input type="text" class="form-control" name="part_no[]"  value="{{$salesdetail->product->part_no}}" readonly>
                                    <input type="hidden" id="orderdtl_id" name="orderdtl_id[]" value="{{ $salesdetail->id }}" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="productname[]" type="text" value="{{$salesdetail->product->productname}}" class="form-control" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="location[]" value="{{$salesdetail->product->location}}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control quantity" name="quantity[]" min="1"  value="{{$salesdetail->quantity}}" placeholder="Type quantity">
                                </td>
                                <td>
                                    <input name="sellingprice[]" type="text" value="{{$salesdetail->sellingprice}}"  class="form-control uamount">
                                    <input type="hidden" name="product_id[]" value="{{$salesdetail->product_id}}">
                                </td>
                                <td>
                                    <input name="total[]" type="text" value="{{$salesdetail->total_amount}}" class="form-control total" readonly>
                                </td>
                                <td width="50px">
                                    {{-- <div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row gx-2">
            <div class="col-lg-5 ">
                <div class="box">
                    <form action="">
                        <div class="row">
                            <!-- <p class="poppins-bold txt-primary">Sales Invoice</p> -->
                            <p class="box-title">Calculation</p>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mx-1 flex-fill">
                                        <label for="">Discount Amount</label>
                                        <input type="number" id="discount_amount" name="discount_amount" class="form-control " style="flex: 0.6;" value="{{$invoices->discount_amount }}" >
                                    </div>
                                    <div class="form-group mx-1 flex-fill">
                                        <label for="">Vat Percent(%)</label>
                                        <input type="number" id="vat_percent" name="vat_percent" value="5"  value="{{$invoices->vatpercentage }}"class="form-control" style="flex: 0.6;">
                                    </div>
                                    <div class="form-group mx-1 flex-fill">
                                        <label for="">Total Vat Amount</label>
                                        <input type="number" id="net_vat_amount" name="net_vat_amount" class="form-control"  style="flex: 0.6;" value="{{$invoices->vatamount }}" readonly>
                                    </div>
                                    

                                </div>
                                <div class="col-lg-6 ">
                                    <div class="form-group mx-1 flex-fill">
                                        <label for="">Grand Total</label>
                                        <input type="number" id="grand_total" name="grand_total" class="form-control "  style="flex: 0.6;" value="{{$invoices->grand_total}}" readonly>
                                    </div>
                                    <div class="form-group mx-1 flex-fill">
                                        <label for="">Net Amount</label>
                                        <input type="number" id="net_total" name="net_total" class="form-control"  style="flex: 0.6;" value="{{$invoices->net_total}}" readonly>
                                    </div>
                                    <div class="form-group mx-1 flex-fill">
                                        <label for="">Customer Paying</label>
                                        <input type="number" id="customer_paid" name="customer_paid" class="form-control" style="flex: 0.6;" value="{{$invoices->customer_paid}}">
                                    </div>
                                    <div class="form-group mx-1 flex-fill">
                                        <label for="">Due</label>
                                        <input type="number" id="due" name="due" min="0" class="form-control"  style="flex: 0.6;"  value="{{$invoices->due}}" readonly>
                                    </div>
                                    <div class="form-group mx-1 flex-fill">
                                        <label for="">Return Amount</label>
                                        <input type="number" id="return_amount" name="return_amount"  style="flex: 0.6;" class="form-control" value="{{$invoices->return_amount}}" readonly>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </form>
                </div>

            </div>
            <div class="col-lg-7 ">
                <form action="">
                    <div class="row gx-2">


                        <div class="col-lg-12">
                            <div class="box mb-0">
                                <p class="box-title">Payments</p>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <td style="font-size:12px">Payment Method</td>
                                                    <td style="text-align: center;font-size:12px">Amount</td>
                                                    <td style="text-align: center;font-size:12px">Card/Bank Number</td>
                                                    <td style="text-align: center;font-size:12px">Name</td>
                                                    <td style="text-align: center;font-size:12px">Comment</td>
                                                    <td style="text-align: center;font-size:12px">Action</td>
                                                </tr>
                                            </thead>                                   
                                            <tbody id="paymentinner">

                                                <tr>
                                                    <td style="width: 17%"><select name="paymentmethod[]" id="paymentmethod" class="form-control paymentmethod" placeholder="Method"></select></td>
                                                    <td><input type="number" id="payment_amount" name="payment_amount[]" class="form-control ms-1 payamount" placeholder="Amount"></td>
                                                    <td><input type="text" class="form-control" id="card_number" name="card_number[]"></td>
                                                    <td><input type="text" class="form-control" id="card_holder_name" name="card_holder_name[]"></td>
                                                    <td><textarea class="form-control" id="comment" name="comment[]"></textarea></td>
                                                    <td><a class="btn btn-sm btn-theme ms-1 add-payment-row" id="addpaymentrow">+</a></td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div> 

                                    <div class="col-lg-12">
                                        <div>
                                            <input class="form-check-input" type="checkbox" value="1" id="partnoshow" @if ($invoices->partnoshow == 1) checked @endif >
                                            <label class="form-check-label" for="partnoshow">
                                            Show Part Number in PDF.
                                            </label>
                                        </div>

                                        @if(Auth::user()->type == '1' && in_array('4', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('4', json_decode(Auth::user()->role->permission)))
                                        <button class="btn btn-theme mt-2" id="updateSalesBtn" type="button">Update Sales</button>
                                        @endif
                                </div>
                                </div> 
                            </div> 
                        </div>

                    </div>
                </form>

            </div>

        </div>

        <div class="row mx-auto"> 
            <div class="box">
                <p class="box-title">Copyright © Next Link Limited. All rights reserved.</p>
            </div> 
        </div>

    </div>

</div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Customer</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form-custom" id="customer-form">
                        {{csrf_field()}}
                        <div class="form-group">
                        <label for="member_id" class="col-sm-3 control-label">Member ID</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="member_id" name="member_id" placeholder="Unique member ID"/>
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
                            <input type="text" name="phone" class="form-control " id="phone" placeholder="ex. 0123456789">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vat_number" class="col-sm-3 control-label">Vat Number</label>
                        <div class="col-sm-9">
                            <input type="text" name="vat_number" class="form-control " id="vat_number" placeholder="ex. 0123456789">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-3 control-label">Address</label>
                        <div class="col-sm-9">
                                <textarea class="form-control" id="address" rows="3" placeholder="1355 Market Street, Suite 900 San Francisco, CA 94103 P: (123) 456-7890" name="address"></textarea>
                        </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <label for="vehicleno" class="col-sm-3 control-label">Vehicle No</label>
                        <div class="col-sm-9">
                            <input type="text" name="vehicleno" class="form-control" id="vehicleno"
                                   placeholder="ex. 012586" required>
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
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary save-btn">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    {{-- stock request modal  --}}

    <div class="modal fade" id="reqStockModal" tabindex="-1" aria-labelledby="reqStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="reqStockModalLabel">Product stock request</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form-custom" id="stockReqForm">
                        {{csrf_field()}}
                    <div class="form-group">
                        <label for="quantity" class="col-sm-3 control-label">Quantity</label>
                        <div class="col-sm-9">
                            <input type="number" name="quantity" class="form-control" id="quantity" required>
                            <input type="hidden" name="productid" class="form-control" id="productid">
                            <input type="hidden" name="stockid" class="form-control" id="stockid">
                            <input type="hidden" name="reqtobranchid" class="form-control" id="reqtobranchid">
                        </div>
                    </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary req-save-btn">Request</button>
                </div>
            </div>
        </div>
    </div>

@endsection
    
@section('script')

<script>
    $(document).ready(function() {
        // Select2 Multiple
        $('.selectproduct').select2({
            placeholder: "Select",
            allowClear: true
        });

        $('.selectcustomer').select2({
            placeholder: "Select",
            allowClear: true
        });

    });

</script>

<script type="text/javascript">
        net_total(); 
        function removePaymentRow(event) {
            event.target.parentElement.parentElement.remove();
        }

        var urlpaymentmethod = "{{URL::to('/getpayment-method')}}";
        payment_method_load()
        $("#addpaymentrow").click(function() {

            var pmarkup = '<tr><td style="width: 17%"><select name="paymentmethod[]" id="paymentmethod" class="form-control paymentmethod" placeholder="Method"><option value="">Select</option>@foreach($paymentMethod as $method)<option value="{{$method->id}}">{{$method->name}}</option>@endforeach</select></td><td><input type="number" id="payment_amount" name="payment_amount[]" class="form-control ms-1 payamount" placeholder="Amount"></td><td><input type="text" class="form-control" id="card_number" name="card_number[]"></td><td><input type="text" class="form-control" id="card_holder_name" name="card_holder_name[]"></td><td><textarea class="form-control" id="comment" name="comment[]"></textarea></td><td><a class="btn btn-sm btn-theme ms-1" onclick="removePaymentRow(event)">-</a></td></tr>';
            $("div #paymentinner ").append(pmarkup);

        });


            function payment_method_load() {
                $.ajax({
                    url: urlpaymentmethod,
                    type: 'GET',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        
                        $('#paymentmethod').append('<option value="">Select</option>');
                        $.each(response, function(){
                            if (this.status == 0) {

                            } else {
                                $('<option/>', {
                                    'value': this.id,
                                    'text': this.name
                                }).appendTo('#paymentmethod');
                            }
                            
                        });
                    },
                    error: function (err) {
                        console.log(err);
                        alert("Something Went Wrong, Please check again");
                    }
                });
            }
 
            

        function removeRow(event) {
            event.target.parentElement.parentElement.remove();
            net_total();   
            }

        function net_total(){
			var dInput = $("#discount_amount").val();
			var customer_paid = $("#customer_paid").val();
            var grand_total=0;
            $('.total').each(function(){
                grand_total += ($(this).val()-0);
            })
            var vatamount = grand_total*.05;
            var amountwithvat = grand_total + grand_total*.05;
            var netamount = amountwithvat-dInput;
            var duamount = netamount-customer_paid;
            $('#grand_total').val(grand_total.toFixed(0));
            $('#net_vat_amount').val(vatamount.toFixed(0));
            $('#net_total').val(netamount.toFixed(0));
            $('#due').val(duamount.toFixed(0));
        }

        $("#chkbranchstocktable").hide();
        $("#chkalternativetable").hide();
        $("#chkreplacementtable").hide();

    $(document).ready(function() {
        // header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        // 

        var urlbr = "{{URL::to('/getproduct')}}";
            $("#product").change(function(){
		            event.preventDefault();
                    var product = $(this).val();

                    var product_id = $("input[name='product_id[]']")
                             .map(function(){return $(this).val();}).get();

                        product_id.push(product);
                        seen = product_id.filter((s => v => s.has(v) || !s.add(v))(new Set));

                        if (Array.isArray(seen) && seen.length) {
                            return;
                        }
                    $("#chkbranchstocktable").hide();
                    $.ajax({
                    url: urlbr,
                    method: "POST",
                    data: {product:product},

                    success: function (d) {
                        if (d.status == 303) {

                        }else if(d.status == 300){
                            
                            // stock table show
                            $("#chkbranchstocktable").show();
                            var stocktable = $(".stocktable tbody");
                            stocktable.empty();
                            $.each(d.stocks, function (a, b) {
                                stocktable.append("<tr><td class='text-left'>" + b.branchname + "</td>" +
                                    "<td class='text-success text-left'>" + b.quantity + "</td>" +
                                    "<td class='text-center'><a href='#' id='transferBtn' pid='" + d.product_id + "'  class='btn btn-sm btn-theme ms-1' branchid='" + b.branch_id + "' stockid='" + b.id + "' data-bs-toggle='modal' data-bs-target='#reqStockModal' >Request to transfer</a></td>" +
                                    "</tr>");
                            });
                            // stock table end  dfgfdg
                               
                            var markup = '<tr class="item-row pdetails" style="position:realative;"><td><input type="text" class="form-control" name="part_no[]" value="'+d.part_no+'" readonly></td><td><input name="productname[]" type="text" value="'+d.productname+'" class="form-control" readonly></td><td><input type="text" class="form-control" name="location[]" value="'+d.location+'" readonly></td><td><input type="number" class="form-control quantity" name="quantity[]" min="1" value="1" placeholder="Type quantity"></td><td><input name="sellingprice[]" type="text" value="'+d.sellingprice+'" class="form-control uamount"><input type="hidden" name="product_id[]" value="'+d.product_id+'"></td><td><input name="total[]" type="text" value="'+d.sellingprice+'" class="form-control total" readonly></td><td width="50px"><div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div></td></tr>';
                            $("table #inner ").append(markup);
                            net_total();

                            // alternatives
                            $("#chkalternativetable").show();
                            var altertable = $(".altertable tbody");
                            altertable.empty();
                            $.each(d.alternatives, function (a, b) {
                                altertable.append("<tr><td class='text-left'>" + b.productname + "</td>" +
                                    "<td class='text-success text-left'>" + b.part_no + "</td>" +
                                    "<td class='text-success text-left'>" + b.location + "</td>" +
                                    "<td class='text-success text-left'>" + b.selling_price + "</td>" +
                                    "</tr>");
                            });
                            // alternatives end

                            // replacements
                            $("#chkreplacementtable").show();
                            var replacetable = $(".replacetable tbody");
                            replacetable.empty();
                            $.each(d.replacements, function (a, b) {
                                replacetable.append("<tr><td class='text-left'>" + b.replacementid + "</td>" +
                                    "</tr>");
                            });
                            // replacements end

                        $("#proname").html(d.productname);   
                        $("#availablestock").html(d.chkstock);   
                        }
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });

                });
        
        // stock request modal show start
        
        $("#chkbranchstocktable").on('click','#transferBtn', function(){
            
            productid = $(this).attr('pid');
            stockid = $(this).attr('stockid');
            branchid = $(this).attr('branchid');
            // console.log(branchid, productid, stockid);
            $('#reqStockModal').find('.modal-body #productid').val(productid);
            $('#reqStockModal').find('.modal-body #stockid').val(stockid);
            $('#reqStockModal').find('.modal-body #reqtobranchid').val(branchid);
                
        });
        // end

        // change quantity start  
        $("body").delegate(".quantity,.total,.uamount","change",function(event){
            event.preventDefault();
            var row = $(this).parent().parent();
            var price = row.find('.uamount').val();
            
            var qty = row.find('.quantity').val();
                if (isNaN(qty)) {
                    qty = 1;
                }
                if (qty < 1) {
                    qty = 1;
                }
            var total = price * qty;
            
            row.find('.total').val(total.toFixed(2));

            var grand_total=0;
            var vat_total=0;
            $('.total').each(function(){
                grand_total += ($(this).val()-0);
            })
            
            $('#grand_total').val(grand_total.toFixed(0));
            $('#net_total').val(grand_total.toFixed(0));
            $('#due').val(grand_total.toFixed(0));
            // $('#ttm').html("<input type='hidden' class='ttm' name='ttm' value="+grand_total+">"); 
            net_total();          
        })
        //Change Quantity end here    

       var orderurl = "{{URL::to('/order-update')}}";
        $("body").delegate("#updateSalesBtn","click",function(event){
                event.preventDefault();

            var partnoshowsts= $('#partnoshow').prop('checked');
            if (partnoshowsts == true) {
                var partnoshow = 1;
            } else {
                var partnoshow = 0;
            }
            
            var order_id= $("#order_id").val();
            var orderdate = $("#orderdate").val();
            var salestype = $("#salestype").val();
            var customer_id = $("#customer_id").val();
            
            var customername = $("#showcustomername").val();
            var customeraddress = $("#showcustomeraddress").val();
            var customervat = $("#showcustomervat").val();
            var customeremail = $("#showcustomeremail").val();
            var customervehicleno = $("#showcustomervehicleno").val();
            var ref = $("#ref").val();
            var due_date = $("#due_date").val();
            var card_number = $("#card_number").val();
            var card_holder_name = $("#card_holder_name").val();
            var comment = $("#comment").val();
            var grand_total = $("#grand_total").val();
            var net_total = $("#net_total").val();
            var discount_percent = $("#discount_percent").val();
            var vat_total = $("#net_vat_amount").val();
            var customer_paid = $("#customer_paid").val();
            var due = $("#due").val();
            var return_amount = $("#return_amount").val();
            var discount_amount = $("#discount_amount").val();
            var vat_percent = $("#vat_percent").val();

            
            var orderdtl_id = $("input[name='orderdtl_id[]']")
              .map(function(){return $(this).val();}).get();

            var product_id = $("input[name='product_id[]']")
              .map(function(){return $(this).val();}).get();

            var sellingprice = $("input[name='sellingprice[]']")
            .map(function(){return $(this).val();}).get();

            var quantity = $("input[name='quantity[]']")
              .map(function(){return $(this).val();}).get();

            var total = $("input[name='total[]']")
              .map(function(){return $(this).val();}).get();
                $.ajax({
                    url: orderurl,
                    method: "POST",
                    data: {product_id,order_id,orderdtl_id,salestype,vat_percent,sellingprice,quantity,total,net_total,orderdate,customer_id,grand_total,customername,customeraddress,customervat,customervehicleno,customeremail,ref,discount_amount,vat_total,customer_paid,due,return_amount,due_date,comment,card_holder_name,card_number,partnoshow},

                    success: function (d) {
                        if (d.status == 303) {
                            console.log(d);
                            $(".ermsg").html(d.message);
                            pagetop();
                        }else if(d.status == 300){
                            console.log(d);
                            $(".ermsg").html(d.message);
                            pagetop();
                            window.setTimeout(function(){location.reload()},2000)
                            window.open(`https://www.greenstock.greentechnology.com.bd/invoice/print/${d.id}`, '_blank');
                            
                        }
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });
        });


        
        // change payment amount  
        $("body").delegate(".payamount","change",function(event){
            event.preventDefault();
            var row = $(this).parent().parent();
            var payamount = row.find('.payamount').val();

            var net_total =  $("#net_total").val();
            var payamount_total=0;
            $('.payamount').each(function(){
                payamount_total += ($(this).val()-0);
            })

            var dueamount = net_total - payamount_total;

			$('#customer_paid').val(payamount_total.toFixed(0));
            if (dueamount < 0) {
                    $('#due').val("0");
                    $('#return_amount').val(dueamount.toFixed(0));
                } else {
                    $('#due').val(dueamount.toFixed(0));
                    $('#return_amount').val("0");
                }
            net_total();    
        })
        //Change payment amount

        function net_total(){
			var dInput = $("#discount_amount").val();
			var customer_paid = $("#customer_paid").val();
            var grand_total=0;
            $('.total').each(function(){
                grand_total += ($(this).val()-0);
            })
            var vatamount = grand_total*.05;
            var amountwithvat = grand_total + grand_total*.05;
            var netamount = amountwithvat-dInput;
            var duamount = netamount-customer_paid;
            $('#grand_total').val(grand_total.toFixed(0));
            $('#net_vat_amount').val(vatamount.toFixed(0));
            $('#net_total').val(netamount.toFixed(0));
            $('#due').val(duamount.toFixed(0));
        }

        


                // customer destails 

        var urlcustomer = "{{URL::to('/getcustomer')}}";
            $("#customers").change(function(){
		            event.preventDefault();
                    var customer_id = $(this).val();
                    $.ajax({
                    url: urlcustomer,
                    method: "POST",
                    data: {customer_id:customer_id},

                    success: function (d) {
                        if (d.status == 303) {

                        }else if(d.status == 300){
                            console.log(d);
                            $("#customer_id").val(d.customer_id);
                            $("#showcustomername").val(d.customername);
                            $("#showcustomeraddress").val(d.address);
                            $("#showcustomervat").val(d.vat_number);
                            $("#showcustomerdue").val(d.showcustomerdue);
                            $("#showcustomeremail").val(d.customeremail);
                            $("#showcustomervehicleno").val(d.vehicleno);
                           
                        }
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });

            });

            // calculation start 
			$("#discount_amount, #vat_percent").keyup(function(){
				// var dInput = this.value;
				var grand_total = $("#grand_total").val();
				var dInput = $("#discount_amount").val();
				var vat_percent = $("#vat_percent").val();
				var payingAmount = $("#vat_percent").val();
			    var customer_paid = $("#customer_paid").val();

				var grand_total_with_discount = grand_total - dInput;
				var net_vat_amount = grand_total_with_discount * (vat_percent/100);
				var net_total = grand_total -  dInput + net_vat_amount;
                var dueAmountCal = net_total - customer_paid;

				$('#net_vat_amount').val(net_vat_amount.toFixed(0));
				$('#net_total').val(net_total.toFixed(0));
				$('#due').val(dueAmountCal.toFixed(0));
            });
            $("#customer_paid").keyup(function(){
				var paidAmount = this.value;
				var net_total = $("#net_total").val();
				var due = net_total - paidAmount;
                if (due < 0) {
                    $('#due').val("0");
                    $('#return_amount').val(due.toFixed(0));
                } else {
                    $('#due').val(due.toFixed(0));
                    $('#return_amount').val("0");
                }
            });
            //calculation end



            

            // customer load

            var urlcustomerload = "{{URL::to('/customer/active')}}";
            customer_load();
            function customer_load() {
                $.ajax({
                    url: urlcustomerload,
                    type: 'GET',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        $('#customers').empty();
                        $('#customers').append('<option selected value="" disabled>Select a Customer</option>');
                        $.each(response, function (i, customer) {
                            let namePhone = customer.name;
                            if (customer.phone) {
                                namePhone += ` (${customer.phone})`;
                            }
                            $('#customers').append($('<option>', {
                                value: customer.id,
                                text: namePhone
                            }));
                        });
                    },
                    error: function (err) {
                        console.log(err.responseText);
                        alert("Something Went wrong, Please check & Try again...");
                    }

                });
            }



            var customerurl = "{{URL::to('/customers')}}";
            
            $(document).on('click', '.save-btn', function () {
                let formData = $('#customer-form').serialize();
                
                $.ajax({
                    url: customerurl,
                    type: 'POST',
                    data: formData,
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        // console.log(response);
                        $('#exampleModal').modal('toggle');
                        $("#customers").val("").trigger('change');
                        $("#customer_id").val(response.id);
                        $("#showcustomername").val(response.name);
                        $("#showcustomeraddress").val(response.address);
                        $("#showcustomervat").val(response.vat_number);
                        $("#showcustomeremail").val(response.email);
                        $("#showcustomervehicleno").val(response.vehicleno);
                    },
                    error: function (err) {
                        console.log(err);
                        alert("Something Went Wrong, Please check again");
                    }
                });
            });


            var stockrequrl = "{{URL::to('/stock-request')}}";
            
            $(document).on('click', '.req-save-btn', function () {
                let formData = $('#stockReqForm').serialize();
                
                $.ajax({
                    url: stockrequrl,
                    type: 'POST',
                    data: formData,
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        console.log(response);
                        $('#reqStockModal').modal('toggle');
                        $(".stockreqermsg").html(response.message);
                        // $("#customers").val("").trigger('change');
                        // $("#customer_id").val(response.id);
                    },
                    error: function (err) {
                        console.log(err);
                        alert("Something Went Wrong, Please check again");
                    }
                });
            });



        
        

});
</script>
@endsection