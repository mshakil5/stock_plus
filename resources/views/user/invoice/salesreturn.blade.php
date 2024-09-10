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
                                            <td width="100px">
                                                <span 
                                                    class="btn btn-success btn-sm returnThisProduct" 
                                                    id="returnThisProduct" 
                                                    ordid="{{$salesdetail->id}}" 
                                                    product_id="{{$salesdetail->product_id}}" 
                                                    product_name="{{$salesdetail->product->productname}}" 
                                                    unit_price="{{$salesdetail->sellingprice}}" 
                                                    total_price="{{$salesdetail->sellingprice}}"
                                                    quantity="{{$salesdetail->quantity}}" 
                                                    purchase_history_id="{{$salesdetail->purchase_history_id}}" 
                                                    style="margin-bottom: 5px; display: inline-block;">
                                                    <i class="bi bi-arrow-bar-right"></i> Return To Stock
                                                </span>
                                                <br>
                                                <span 
                                                    class="btn btn-danger btn-sm returnToDamage" 
                                                    id="returnToDamage" 
                                                    ordid="{{$salesdetail->id}}" 
                                                    product_id="{{$salesdetail->product_id}}" 
                                                    product_name="{{$salesdetail->product->productname}}" 
                                                    unit_price="{{$salesdetail->sellingprice}}" 
                                                    total_price="{{$salesdetail->sellingprice}}"
                                                    quantity="{{$salesdetail->quantity}}" 
                                                    style="display: inline-block;">
                                                    <i class="bi bi-x-circle"></i> Return to Damage
                                                </span>
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
                    <div id="returnbox" style="display: none">
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


                    <div id="damagebox"  style="display: none">
                        <div class="row">
                            <p class="poppins-bold txt-primary">Number of quantity you want to send to damaged</p>
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
                                        <tbody id="damageinner">  
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
                                            <td colspan="2" style="text-align: left"></td>
                                            <td colspan="2" style="text-align: center">
                                                <button class="btn btn-theme mt-2" id="damageReturnBtn" type="button">submit</button>
                                            </td>
                                        </tr> 
                                        
                                    </tbody>
                                </table>
                            </div> 
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

    $(document).ready(function() {

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
  
        $("#productsTBL").on('click','.returnThisProduct', function(event) {
            event.preventDefault();
            var product = $(this).attr('product_id');
            var product_name = $(this).attr('product_name');
            var unit_price = parseFloat($(this).attr('unit_price'));
            var quantity = parseInt($(this).attr('quantity'));
            var purchase_history_id = $(this).attr('purchase_history_id');
            var total_price = unit_price * quantity;

            var product_id = $("input[name='return_product_id[]']")
                .map(function() { return $(this).val(); }).get();

            product_id.push(product);
            var seen = [...new Set(product_id)];
            if (product_id.length !== seen.length) {
                return;
            }

            var markup = 
                '<tr class="item-row pdetails" style="position:relative;">' +
                    '<td><input name="productname[]" type="text" value="' + product_name + '" class="form-control" readonly></td>' +
                    '<td><input type="number" class="form-control quantity" name="quantity[]" min="1" value="' + quantity + '" placeholder="Type quantity">' +
                    '<input type="hidden" class="form-control oldquantity" name="oldquantity[]" value="' + quantity + '"></td>' +
                    '<td><input name="sellingprice[]" type="text" value="' + unit_price.toFixed(2) + '" class="form-control uamount" readonly>' +
                    '<input type="hidden" name="return_product_id[]" value="' + product + '"></td>' +
                    '<td><input name="total[]" type="text" value="' + total_price.toFixed(2) + '" class="form-control total" readonly></td>' +
                    '<td><input type="hidden" name="purchase_history_id[]" value="' + purchase_history_id + '"></td>' +
                    '<td width="30px"><div class="removeRowBtn" style="color: white; user-select:none; padding: 5px; background: red; width: 25px; display: flex; align-items: center; justify-content: center; border-radius: 4px; cursor: pointer;">X</div></td>' +
                '</tr>';

            $("table #returninner").append(markup);

            updateTotals();
        });
           
        $("#productsTBL").on('click','.returnToDamage', function(){
            event.preventDefault();
            orderdetailid = $(this).attr('ordid');
            var product = $(this).attr('product_id');
            var product_name = $(this).attr('product_name');
            var unit_price = parseFloat($(this).attr('unit_price'));
            var quantity = parseInt($(this).attr('quantity'));
            var total_price = unit_price * quantity;

            var product_id = $("input[name='damage_product_id[]']")
                .map(function(){return $(this).val();}).get();
            product_id.push(product);
            seen = product_id.filter((s => v => s.has(v) || !s.add(v))(new Set));

            
            if (Array.isArray(seen) && seen.length) {
                return;
            }

            var markup = '<tr class="item-row pdetails" style="position:realative;">' +
                '<td><input name="productname[]" type="text" value="' + product_name + '" class="form-control" readonly></td>' +
                '<td><input type="number" class="form-control quantity" name="quantity[]" min="1" value="' + quantity + '" placeholder="Type quantity">' +
                '<input type="hidden" class="form-control oldquantity" name="oldquantity[]" value="' + quantity + '"></td>' +
                '<td><input name="sellingprice[]" type="text" value="' + unit_price.toFixed(2) + '" class="form-control uamount" readonly>' +
                '<input type="hidden" name="damage_product_id[]" value="' + product + '"></td>' +
                '<td><input name="total[]" type="text" value="' + total_price.toFixed(2) + '" class="form-control total" readonly></td>' +
                '<td width="30px"><div class="removeRowBtn" style="color: white; user-select:none; padding: 5px; background: red; width: 25px; display: flex; align-items: center; justify-content: center; border-radius: 4px; cursor: pointer;">X</div></td>' +
                '</tr>';

            $("table #damageinner").append(markup);
    
        }); 

        $(document).on('click', '.removeRowBtn', function() {
            var row = $(this).closest('tr');
            row.remove();
            updateTotals();
        });
                   
        // change quantity start  
        $("body").delegate(".quantity", "input", function(event) {
            event.preventDefault();
            var row = $(this).closest('.item-row');
            var price = parseFloat(row.find('.uamount').val());
            var qty = parseInt($(this).val());
            var oldquantity = parseInt(row.find('.oldquantity').val());

            if (isNaN(price)) price = 0;
            if (isNaN(qty) || qty < 1) {
                $(this).val(1);
            }
            if (qty > oldquantity) {
                alert('Invalid Quantity !!');
                $(this).val(oldquantity);
            }

            var total = price * $(this).val();
            row.find('.total').val(total.toFixed(2));

            updateTotals();
        });

        function updateTotals() {
            var grand_total = 0;

            $('.total').each(function() {
                grand_total += parseFloat($(this).val()) || 0;
            });

            $('#net_total').val(grand_total.toFixed(2));
        }

        // sales return start
        var returnurl = "{{URL::to('/sales-return')}}";

        $("body").delegate("#returnOrderBtn","click",function(event){
            event.preventDefault();

            var returndate = $("#returndate").val();
            var customer_id = $("#customer_id").val();
            var reason = $("#reason").val();
            var net_total = $("#net_total").val();
            var order_id = $("#order_id").val();

            var product_id = $("input[name='return_product_id[]']")
              .map(function(){return $(this).val();}).get();

            var quantity = $("input[name='quantity[]']")
              .map(function(){return $(this).val();}).get();

            var total = $("input[name='total[]']")
              .map(function(){return $(this).val();}).get();

            var purchase_history_id = $("input[name='purchase_history_id[]']")
                .map(function(){return $(this).val();}).get();

                $.ajax({
                    url: returnurl,
                    method: "POST",
                    data: {product_id,order_id,quantity,total,net_total,returndate,customer_id,reason,purchase_history_id},

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
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });

        });
        // sales return end

        // Damage Return start
        var damageurl = "{{URL::to('/damage-return')}}";

        $("body").delegate("#damageReturnBtn","click",function(event){
            event.preventDefault();

            var customer_id = $("#customer_id").val();

            var product_id = $("input[name='damage_product_id[]']")
              .map(function(){return $(this).val();}).get();

            var quantity = $("input[name='quantity[]']")
              .map(function(){return $(this).val();}).get();

            $.ajax({
                url: damageurl,
                method: "POST",
                data: {product_id,quantity,customer_id},

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
        // Damage return end

        //Open Close
        $("#productsTBL").on('click','#returnToDamage', function(){
            $('#damagebox').show();
            $('#returnbox').hide();
            $('#returninner').empty();
        });
        
        $("#productsTBL").on('click','#returnThisProduct', function(){
            $('#damagebox').hide();
            $('#returnbox').show();
            $('#damageinner').empty();
        });

    });
</script>
@endsection