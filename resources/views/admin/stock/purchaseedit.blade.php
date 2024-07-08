@extends('admin.layouts.master')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <script>

    </script>
    <div class="row ">
        <div class="container-fluid">
            <div class="col-md-12">
                
            </div>
            <div class="col-md-9">
                <div class="box box-default box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Purchase Edit</h3>
                        <!-- /.box-tools -->
                    </div>
                    <div class="ermsg"></div>
                    <div>
                        @if (Session::has('success'))
                            <div class="alert alert-success">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                <p>{{ Session::get('success') }}</p>
                            </div>
                            {{ Session::forget('success') }}
                        @endif
                        @if (Session::has('warning'))
                            <div class="alert alert-warning">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                <p>{{ Session::get('warning') }}</p>
                            </div>
                            {{ Session::forget('warning') }}
                        @endif
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body ir-table">

                        <form>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                  <label for="date">Entry Date</label>
                                  <input type="date" class="form-control" id="date" name="date" value="{{ $purchase->date }}">
                                  <input type="hidden" class="form-control" id="purchase_id" name="purchase_id" value="{{ $purchase->id }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="supplier_id">Supplier</label>
                                    <select name="supplier_id" id="supplier_id" class="form-control select2">
                                        <option value="">Select</option>
                                        @foreach (\App\Models\Vendor::where('status','1')->get() as $vendor)
                                        <option value="{{ $vendor->id }}" @if ($purchase->vendor_id == $vendor->id) selected @endif>{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="">New</label>
                                    <a class="btn btn-primary btn-sm btn-return" data-toggle="modal" data-target="#newSupplierModal">
                                            <i class='fa fa-plus'></i> Add
                                    </a>
                                </div>

                                
                            </div>

                            <div class="form-row">
                                
                                <div class="form-group col-md-4">
                                    <label for="previous_due">Supplier Previous Due</label>
                                    <input type="text" class="form-control" id="previous_due" name="previous_due">
                                </div> 

                                <div class="form-group col-md-4">
                                    <label for="invoiceno">Invoice No</label>
                                    <input type="number" class="form-control" id="invoiceno" name="invoiceno" value="{{ $purchase->invoiceno }}">
                                </div>

                                
                                <div class="form-group col-md-4">
                                    <label for="vat_reg">VAT Reg#</label>
                                    <input type="text" class="form-control" id="vat_reg" name="vat_reg" value="{{ $purchase->vat_reg }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                  <label for="date">Transaction Type</label>
                                  <select name="type" id="type" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Cash" @if ($purchase->purchase_type == "Cash") selected @endif>Cash</option>
                                    <option value="Credit" @if ($purchase->purchase_type == "Credit") selected @endif>Credit</option>
                                  </select>
                                </div>

                                <div class="form-group col-md-8">
                                    <label for="product">Product</label>
                                    <select name="product" id="product" class="form-control select2">
                                        <option value="">Select</option>
                                            @foreach (\App\Models\Product::select('id','productname','part_no')->get() as $product)
                                            <option value="{{ $product->id }}">{{ $product->productname }}-{{ $product->part_no }}</option>
                                            @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="branch_id">Branch</label>
                                    <select name="branch_id" id="branch_id" class="form-control select2">
                                    <option value="">Select</option>
                                        @foreach (\App\Models\Branch::where('status','1')->get() as $branch)
                                        <option value="{{ $branch->id }}" @if ($purchase->branch_id == $branch->id) selected @endif>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                

                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-4">
                                    <label for="ref">Ref</label>
                                    <input type="text" class="form-control" id="ref" name="ref" value="{{ $purchase->ref }}">
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="remarks">Remarks</label>
                                    <input type="text" class="form-control" id="remarks" name="remarks" value="{{ $purchase->remarks }}">
                                </div>
                            </div>
                          </form>
                    </div>

                    
                </div>

                <div  class="box box-default box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Product List</h3>
                        <!-- /.box-tools -->
                    </div>

                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">Part No</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Unit Price</th>
                                <th class="text-center">Vat%</th>
                                <th class="text-center">Vat Amount</th>
                                <th class="text-center">Total Price</th>
                                <th class="text-center">Total Price With Vat</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody  id="inner">
                            @foreach ($purchase->purchasehistory as $key => $prdct)
                                <tr>

                                    <td class="text-center">
                                        <input type="text" id="pert_no" name="pert_no[]" value="{{ $prdct->product->part_no }}" class="form-control" readonly>
                                        
                                    </td>
                                    <td class="text-center">
                                        <input type="text" id="productname" name="productname[]" value="{{ $prdct->product->productname }}" class="form-control" readonly>
                                        <input type="hidden" id="product_id" name="product_id[]" value="{{ $prdct->product->id }}" class="form-control" readonly>
                                        <input type="hidden" id="purchase_his_id" name="purchase_his_id[]" value="{{ $prdct->id }}" class="form-control" readonly>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" id="quantity" name="quantity[]" value="{{ $prdct->quantity }}" min="1" class="form-control quantity" >
                                        
                                    </td>
                                    <td class="text-center">
                                        <input type="number" id="unit_price" name="unit_price[]" value="{{ $prdct->purchase_price}}" min="1" class="form-control unit-price">
                                        
                                    </td>
                                    <td class="text-center">
                                        <input type="number" id="vat_percent" name="vat_percent[]" min="0"  max="20" style="width:60px;" class="form-control uvatpercent" value="{{ $prdct->vat_percent}}">
                                    </td>
                                    <td class="text-center">
                                        <input type="text" id="vat_amount" name="vat_amount[]" value="{{ $prdct->total_vat }}" class="form-control totalvat" readonly>
                                        
                                    </td>
                                    <td class="text-center">
                                        <input type="text" id="total_amount" name="total_amount[]" value="{{ $prdct->total_amount_per_unit}}" class="form-control total" readonly>
                                        
                                    </td>
                                    <td class="text-center">
                                        <input type="text" id="total_amount_with_vat" value="{{ $prdct->total_amount_with_vat}}" name="total_amount_with_vat[]" class="form-control totalwithvat" readonly>
                                        
                                    </td>
                                    <td class="text-center">
                                        {{-- <div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div> --}}
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>


                </div>
                <!-- /.box-body -->
                <!-- /.box -->
            </div>
            <div class="col-md-3">
                <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user-2">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header">
                        <h3 class="widget-user-username"></h3>
                    </div>
                    <div class="box-body">

                        <div class="form-group row">
                            <label for="total_currency" class="col-sm-6 col-form-label">Currency Total</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" id="total_currency" name="total_currency">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="grand_total" class="col-sm-6 col-form-label">Item Total Amount</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" id="grand_total" name="grand_total"  value="{{ $purchase->total_amount }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="discount" class="col-sm-6 col-form-label">Discount</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" id="discount" name="discount" value="{{ $purchase->discount }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="total_vat_amount" class="col-sm-6 col-form-label">Vat Amount</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" id="total_vat_amount" name="total_vat_amount" value="{{ $purchase->total_vat_amount }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="net_amount" class="col-sm-6 col-form-label">Net Amount</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" id="net_amount" name="net_amount" value="{{ $purchase->net_amount }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="paid_amount" class="col-sm-6 col-form-label">Paid Amount</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" id="paid_amount" name="paid_amount" value="{{ $purchase->paid_amount }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="due_amount" class="col-sm-6 col-form-label">Due Amount</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" id="due_amount" name="due_amount" min="0" value="{{ $purchase->due_amount }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <button class="btn btn-success btn-md center-block" id="purchaseupBtn" type="submit"><i class="fa fa-plus-circle"></i> Update </button>
                            </div>
                        </div>


                            
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>

    <!-- /. supplier modal start here -->
    <!-- Modal -->
    <div class="modal fade transfer-modal" id="newSupplierModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header alert alert-success" style="text-align: left;">
                    <div>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">#Add New Supplier</h4>
                    </div>
                </div>
                <div class="modal-body transferProduct">

                    <form action="{{ route('admin.savevendor')}}" method="POST">
                        {{csrf_field()}}
                    
                        <div class="col-sm-12">

                            <div class="form-group col-sm-6">
                                <div class="row">
                                    <div class="col-sm-4 text-left">
                                        <label for="code">Supplier ID: </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="code" id="code" style="width: 100%;" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <div class="row">
                                    <div class="col-sm-4 text-left">
                                        <label for="name">Supplier Name: </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="name" id="name" style="width: 100%;" required>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="form-group col-sm-6">
                                <div class="row">
                                    <div class="col-sm-4 text-left">
                                        <label for="email">Email: </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" name="email" id="email" style="width: 100%;" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <div class="row">
                                    <div class="col-sm-4 text-left">
                                        <label for="phone">Phone: </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="phone" id="phone" style="width: 100%;" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <div class="row form-group">
                                    <div class="col-sm-4 text-left">
                                        <label for="vat_reg">Vat Reg: </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="vat_reg" id="vat_reg" style="width: 100%;" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 text-left">
                                        <label for="address">Address: </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <textarea  class="form-control" name="address" id="address" cols="30" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <div class="row">
                                    <div class="col-sm-4 text-left">
                                        <label for="company">Company Information: </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <textarea  class="form-control" name="company" id="company" cols="30" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>


                        </div>
                    
                    
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
    <!-- /. supplier modal end here -->
    

    @endsection
    
@section('script')
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>

    <script type="text/javascript">
        function removeRow(event) {
            event.target.parentElement.parentElement.remove();
            }
    </script>

    <script>
    $(document).ready(function () {

            
            // header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            // 

            var urlbr = "{{URL::to('/admin/getproduct')}}";
                $("#product").change(function(){
                        event.preventDefault();
                        var product = $(this).val();

                        var product_id = $("input[name='product_id[]']")
                             .map(function(){return $(this).val();}).get();

                        product_id.push(product);
                        seen = product_id.filter((s => v => s.has(v) || !s.add(v))(new Set));

                        if (Array.isArray(seen) && seen.length) {
                            $(".ermsg").html("<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Duplicate product found..!</b></div>");
                            return;
                        }

                        $.ajax({
                        url: urlbr,
                        method: "POST",
                        data: {product:product},

                        success: function (d) {
                            if (d.status == 303) {

                            }else if(d.status == 300){
                                // console.log(d);
                                    
                                    var markup = '<tr><td class="text-center"><input type="text" id="pert_no" name="pert_no[]" value="'+d.part_no+'" class="form-control" readonly></td><td class="text-center"><input type="text" id="productname" name="productname[]" value="'+d.productname+'" class="form-control" readonly><input type="hidden" id="product_id" name="product_id[]" value="'+d.product_id+'" class="form-control" readonly></td><td class="text-center"><input type="number" id="quantity" name="quantity[]" value="1" min="1" class="form-control quantity" ></td><td class="text-center"><input type="number" id="unit_price" name="unit_price[]"  min="1" value="" class="form-control unit-price"></td><td class="text-center"><input type="number" id="vat_percent" name="vat_percent[]" min="0"  max="20" style="width:60px;" class="form-control uvatpercent" value="5"></td><td class="text-center"><input type="text" id="vat_amount" name="vat_amount[]" value="" class="form-control totalvat" readonly></td><td class="text-center"><input type="text" id="total_amount" name="total_amount[]" value="" class="form-control total" readonly></td><td class="text-center"><input type="text" id="total_amount_with_vat" value="" name="total_amount_with_vat[]" class="form-control totalwithvat" readonly></td><td class="text-center"><div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div></td></tr>';

                                    $("table #inner ").append(markup);
                                    
    
                            }
                        },
                        error: function (d) {
                            console.log(d);
                        }
                    });

                });





        


        // unit price calculation

        $("body").delegate(".unit-price, .uvatpercent, .quantity","keyup",function(event){
            event.preventDefault();
            var row = $(this).parent().parent();
            var price = row.find('.unit-price').val();
            var vatpercent = row.find('.uvatpercent').val();
            var qty = row.find('.quantity').val();

                if (isNaN(qty)) {
                    qty = 1;
                }
                if (qty < 1) {
                    qty = 1;
                }
            
            var vatamount = price * vatpercent/100;
            var total = price * qty;
            var totalvat = vatamount * qty;
            var totalwithvat = (price * qty) + totalvat;
            row.find('.totalvat').val(totalvat.toFixed(2));
            row.find('.total').val(total.toFixed(2));
            row.find('.totalwithvat').val(totalwithvat.toFixed(2));

            var grand_total=0;
            var vat_total=0;
            var total_with_vat=0;
            $('.total').each(function(){
                grand_total += ($(this).val()-0);
            })
            $('.totalvat').each(function(){
                vat_total += ($(this).val()-0);
            })
            $('.totalwithvat').each(function(){
                total_with_vat += ($(this).val()-0);
            })
            $('#total_vat_amount').val(vat_total.toFixed(2));
            $('#grand_total').val(grand_total.toFixed(2));
            $('#net_amount').val(total_with_vat.toFixed(2));
            $('#due_amount').val(total_with_vat.toFixed(2));
                 
        })
        // unit price calculation end

        // calculation onchange
        $("body").delegate(".unit-price, .uvatpercent, .quantity","change",function(event){
            event.preventDefault();

            var row = $(this).parent().parent();
            var price = row.find('.unit-price').val();
            var vatpercent = row.find('.uvatpercent').val();
            var qty = row.find('.quantity').val();

                if (isNaN(qty)) {
                    qty = 1;
                }
                if (qty < 1) {
                    qty = 1;
                }
            
            var vatamount = price * vatpercent/100;
            var total = price * qty;
            var totalvat = vatamount * qty;
            var totalwithvat = (price * qty) + totalvat;
            row.find('.totalvat').val(totalvat.toFixed(2));
            row.find('.total').val(total.toFixed(2));
            row.find('.totalwithvat').val(totalwithvat.toFixed(2));

            var grand_total=0;
            var vat_total=0;
            var total_with_vat=0;
            $('.total').each(function(){
                grand_total += ($(this).val()-0);
            })
            $('.totalvat').each(function(){
                vat_total += ($(this).val()-0);
            })
            $('.totalwithvat').each(function(){
                total_with_vat += ($(this).val()-0);
            })
            $('#total_vat_amount').val(vat_total.toFixed(2));
            $('#grand_total').val(grand_total.toFixed(2));
            $('#net_amount').val(total_with_vat.toFixed(2));
            $('#due_amount').val(total_with_vat.toFixed(2));

        })
        // calculation onchange end


        // submit to purchase 
        var purchaseupurl = "{{URL::to('/admin/update-purchase')}}";

            $("body").delegate("#purchaseupBtn","click",function(event){
                event.preventDefault();

                var purchase_id = $("#purchase_id").val();
                var invoiceno = $("#invoiceno").val();
                var date = $("#date").val();
                var vendor_id = $("#supplier_id").val();
                var ref = $("#ref").val();
                var purchase_type = $("#type").val();
                var branch_id = $("#branch_id").val();
                var vat_reg = $("#vat_reg").val();
                var remarks = $("#remarks").val();
                var total_currency = $("#total_currency").val();
                var total_amount = $("#grand_total").val();
                var discount = $("#discount").val();
                var total_vat_amount = $("#total_vat_amount").val();
                var net_amount = $("#net_amount").val();
                var paid_amount = $("#paid_amount").val();
                var due_amount = $("#due_amount").val();

                var product_id = $("input[name='product_id[]']")
                    .map(function(){return $(this).val();}).get();

                var purchase_his_id = $("input[name='purchase_his_id[]']")
                    .map(function(){return $(this).val();}).get();

                var vat_percent = $("input[name='vat_percent[]']")
                    .map(function(){return $(this).val();}).get();

                var quantity = $("input[name='quantity[]']")
                    .map(function(){return $(this).val();}).get();

                var unit_price = $("input[name='unit_price[]']")
                    .map(function(){return $(this).val();}).get();


                $.ajax({
                    url: purchaseupurl,
                    method: "POST",
                    data: {purchase_id,date,invoiceno,vendor_id,ref,purchase_type,branch_id,vat_reg,remarks,total_amount,discount,total_vat_amount,net_amount,paid_amount,due_amount,product_id,purchase_his_id,vat_percent,quantity,unit_price},

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
        // submit to purchase end

        // discount calculation
        $("#discount").keyup(function(){
                dInput = this.value;
                grand_total = parseFloat($("#grand_total").val());
                total_vat_amount = parseFloat($("#total_vat_amount").val());
                net_total = grand_total + total_vat_amount - dInput;

            $('#net_amount').val(net_total.toFixed(2));
        });
        // discount calculation end

        // due calculation
        $("#paid_amount").keyup(function(){
                paidInput = this.value;
                net_amount = parseFloat($("#net_amount").val());
                
                due_amount = net_amount  - paidInput;
                if (paidInput > net_amount) {
                    $('#due_amount').val('');
                } else {
                    $('#due_amount').val(due_amount.toFixed(2));
                }

            
        });
        // due calculation end

    });  
    </script>

@endsection
