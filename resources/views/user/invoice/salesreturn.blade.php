@extends('user.layouts.master')
@section('content')


<div class="inner ">
    <div class="content w-90 mx-auto">
        <div class="row gx-2">
            <div class="col-lg-6 ">
                <div class="box">
                    <div class="row">
                        <p class="poppins-bold txt-primary">Customer Information</p>
                        <div class="ermsg"></div>
                        <div class="row">
                            <div class="col-lg-6 ">
                                <div class="form-group mb-3 mx-1 flex-fill">
                                    <label for="">Customer Name</label>
                                    <input type="text" id="showcustomername" class="form-control " value="{{ $invoices->customer->name }}" readonly>
                                    <input type="hidden" id="customer_id"  name="customer_id" value="{{ $invoices->customer_id }}" readonly>
                                    <input type="hidden" id="order_id"  name="order_id" value="{{ $invoices->id }}" readonly>
                                </div>
                                <div class="form-group mb-3 mx-1 flex-fill">
                                    <label for="">Customer Address</label>
                                    <input type="text" id="showcustomeraddress" class="form-control " value="{{ $invoices->customer->address }}" readonly>
                                </div>
                                <div class="form-group mb-3 mx-1 flex-fill">
                                    <label for="">Vehicle No</label>
                                    <input type="text" id="showcustomervehicleno" class="form-control " value="{{ $invoices->customer->vehicleno }}" readonly>
                                </div>
                                
                                <div class="form-group mb-3 mx-1 flex-fill">
                                    <label for="">Reference</label>
                                    <input type="text" id="ref" class="form-control " value="{{$invoices->ref }}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6 ">
                                <div class="form-group mb-3 mx-1 flex-fill">
                                    <label for="">Return Date</label>
                                    <input type="date" id="returndate" name="returndate" value="{{date('Y-m-d')}}" class="form-control ">
                                </div>
                                <div class="form-group mb-3 mx-1 flex-fill">
                                    <label for="">Reason For Return</label>
                                    <textarea  id="reason" name="reason" class="form-control " cols="30" rows="20"></textarea>
                                </div>
                            </div> 
                        </div>
                    </div>


                    <div class="row">
                        <div class="box">
                            <table class="table table-striped table-hover" id="productsTBL">
                                <thead>
                                    <tr>
                                        <td>Product</td>
                                        <td>Part No</td>
                                        <td>Qty</td>
                                        <td>Unit Price</td>
                                        <td>Total</td> 
                                        <td>Action</td>
                                    </tr>
                                </thead>                                   
                                <tbody>
                                    @foreach ($invoices->orderdetails  as $salesdetail)
                                        <tr>
                                            <td>
                                                <input type="text" value="{{$salesdetail->product->productname}}" class="form-control" readonly>
                                            </td>
                                            <td>
                                                <input type="text" value="{{$salesdetail->product->part_no}}" class="form-control" readonly>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" min="1" value="{{$salesdetail->quantity}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" value="{{$salesdetail->sellingprice}}" class="form-control" readonly>
                                            </td>
                                            <td>
                                                <input type="text" value="{{$salesdetail->total_amount}}" class="form-control" readonly>
                                            </td>
                                            <td width="50px">
                                                <span class="btn btn-success btn-sm" id="returnThisProduct" ordid="{{$salesdetail->id}}" product_id="{{$salesdetail->product->id}}"> <i class="bi bi-arrow-bar-right"></i> return  </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                        
                                </tbody>
                            </table>
                        </div> 
                    </div>
                </div>
            </div>


            <div class="col-lg-6 ">
                <div class="box">
                    <div class="row">
                        <p class="poppins-bold txt-primary">Number of quantity you want to return</p>
                        <div class="row">
                            <div class="box">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <td>Product</td>
                                            <td>Qty</td>
                                            <td>Unit Price</td>
                                            <td>Total</td> 
                                            <td>Action</td>
                                        </tr>
                                    </thead>                                   
                                    <tbody id="returninner">  
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="box">
                            <table class="table table-striped table-hover" style="width: 100%">                       
                                <tbody> 
                                    <tr>
                                        <td colspan="4" style="width: 50%"></td>
                                        <td colspan="2" style="text-align: left">Total Return Amount</td>
                                        <td colspan="2" style="text-align: right"><input type="number" id="net_total" name="net_total" class="form-control" readonly></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="width: 50%"></td>
                                        <td colspan="2" style="text-align: left"></td>
                                        <td colspan="2" style="text-align: center">
                                            <button class="btn btn-theme mt-2" id="returnOrderBtn" type="button">submit</button>
                                        </td>
                                    </tr> 
                                      
                                </tbody>
                            </table>
                        </div> 
                    </div>



                </div>
            </div>
        </div>
        
        <div class="row mx-auto"> 
            <div class="box">
                <p class="box-title">Copyright © Next Link Limited. All rights reserved.</p>
            </div> 
        </div>
    </div>
</div>
    
 

@endsection
    
@section('script')

<script type="text/javascript">

        function removeRow(event) {
            event.target.parentElement.parentElement.remove();
            net_total();  
        }

    $(document).ready(function() {
        // header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        // 

        
        var salesproducturl = "{{URL::to('/get-product-details')}}";
        $("#productsTBL").on('click','#returnThisProduct', function(){
                    event.preventDefault();
                    orderdetailid = $(this).attr('ordid');
                    var product = $(this).attr('product_id');
                    var product_id = $("input[name='product_id[]']")
                        .map(function(){return $(this).val();}).get();
                    product_id.push(product);
                    seen = product_id.filter((s => v => s.has(v) || !s.add(v))(new Set));

                    if (Array.isArray(seen) && seen.length) {
                        return;
                    }
                    $.ajax({
                    url: salesproducturl,
                    method: "POST",
                    data: {orderdetailid:orderdetailid},

                    success: function (d) {
                        if (d.status == 303) {

                        }else if(d.status == 300){
                            console.log(d);
                                var markup = '<tr class="item-row pdetails" style="position:realative;"><td><input name="productname[]" type="text" value="'+d.productname+'" class="form-control" readonly></td><td><input type="number" class="form-control quantity" name="quantity[]" min="1" value="1" placeholder="Type quantity"><input type="hidden" class="form-control oldquantity" name="oldquantity[]" min="1" value="'+d.quantity+'"></td><td><input name="sellingprice[]" type="text" value="'+d.selling_price_with_vat+'" class="form-control uamount" readonly><input type="hidden" name="product_id[]" value="'+d.product_id+'"></td><td><input name="total[]" type="text" value="'+d.selling_price_with_vat+'" class="form-control total" readonly></td><td width="30px"><div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 25px;    display: flex;    align-items: center; margin-right:2px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div></td></tr>';
                                $("table #returninner ").append(markup);

                                net_total();
                        }
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });
                
            }); 
            
            
            // change quantity start  
        $("body").delegate(".quantity","change",function(event){
            event.preventDefault();
            var row = $(this).parent().parent();
            var price = row.find('.uamount').val();
            var vatamount = row.find('.uvatamount').val();
            // var update_id = row.find('.price').attr("update_id");
            var qty = row.find('.quantity').val();
            var oldquantity = row.find('.oldquantity').val();
            availableqty = parseInt(oldquantity);
            if ( qty  > availableqty) {
                alert('Please Input lower quantity !!');
                row.find('.quantity').val('1');
            }
            
                if (isNaN(qty)) {
                    qty = 1;
                }
                if (qty < 1) {
                    qty = 1;
                }
            var total = price * qty;
            var totalvat = vatamount * qty;
            row.find('.total').val(total.toFixed(2));

            var grand_total=0;
            var vat_total=0;
            $('.total').each(function(){
                grand_total += ($(this).val()-0);
            })
            $('.vatamount').each(function(){
                vat_total += ($(this).val()-0);
            })
            $('#net_vat_amount').val(vat_total.toFixed(2));
            $('#grand_total').val(grand_total.toFixed(2));
            $('#net_total').val(grand_total.toFixed(2));
            // $('#ttm').html("<input type='hidden' class='ttm' name='ttm' value="+grand_total+">"); 
            net_total();           
        })
        //Change Quantity end here 
        
        function net_total(){
            var grand_total=0;
            $('.total').each(function(){
                grand_total += ($(this).val()-0);
            })
            $('#net_total').val(grand_total.toFixed(2));
        }



        // sales return start
        var returnurl = "{{URL::to('/sales-return')}}";

        // $("#addvoucher").click(function(){

            $("body").delegate("#returnOrderBtn","click",function(event){
                event.preventDefault();

            var returndate = $("#returndate").val();
            var customer_id = $("#customer_id").val();
            var reason = $("#reason").val();
            var net_total = $("#net_total").val();
            var order_id = $("#order_id").val();

            var product_id = $("input[name='product_id[]']")
              .map(function(){return $(this).val();}).get();

            var vat_percent = $("input[name='vat_percent[]']")
            .map(function(){return $(this).val();}).get();

            var vat_amount = $("input[name='vat_amount[]']")
            .map(function(){return $(this).val();}).get();

            var sellingprice = $("input[name='sellingprice[]']")
            .map(function(){return $(this).val();}).get();

            var quantity = $("input[name='quantity[]']")
              .map(function(){return $(this).val();}).get();

            var total = $("input[name='total[]']")
              .map(function(){return $(this).val();}).get();


                $.ajax({
                    url: returnurl,
                    method: "POST",
                    data: {product_id,order_id,vat_percent,vat_amount,sellingprice,quantity,total,net_total,returndate,customer_id,reason},

                    success: function (d) {
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                            pagetop();
                        }else if(d.status == 300){
                            $(".ermsg").html(d.message);
                            pagetop();
                            window.setTimeout(function(){location.reload()},2000)
                            
                        }
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });

        });
        // sales return end













    });
</script>
@endsection