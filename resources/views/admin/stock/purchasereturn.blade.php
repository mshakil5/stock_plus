@extends('admin.layouts.master')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <script>

    </script>
    <div class="row ">
        <div class="container-fluid">
            <div class="col-md-12">

                <div class="box box-default box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Purchase Return</h3>
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
                                  <label for="date">Return Date</label>
                                  <input type="date" class="form-control" id="date" name="date" value="{{date('Y-d-m')}}">
                                  <input type="hidden" class="form-control" id="purchase_id" name="purchase_id" value="{{ $purchase->id }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="supplier_id">Supplier</label>
                                    <select name="supplier_id" id="supplier_id" class="form-control select2" disabled readonly>
                                        <option value="">Select</option>
                                        @foreach (\App\Models\Vendor::all() as $vendor)
                                        <option value="{{ $vendor->id }}" @if ($purchase->vendor_id == $vendor->id) selected @endif>{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                
                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-4">
                                    <label for="invoiceno">Invoice No</label>
                                    <input type="number" class="form-control" id="invoiceno" name="invoiceno" value="{{ $purchase->invoiceno }}" readonly>
                                </div>

                                
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                  <label for="date">Transaction Type</label>
                                  <select name="type" id="type" class="form-control" readonly>
                                    <option value="">Select</option>
                                    <option value="Cash" @if ($purchase->purchase_type == "Cash") selected @endif>Cash</option>
                                    <option value="Credit" @if ($purchase->purchase_type == "Credit") selected @endif>Credit</option>
                                  </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="branch_id">Branch</label>
                                    <select name="branch_id" id="branch_id" class="form-control select2" disabled readonly>
                                    <option value="">Select</option>
                                        @foreach (\App\Models\Branch::all() as $branch)
                                        <option value="{{ $branch->id }}" @if ($purchase->branch_id == $branch->id) selected @endif>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                

                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-4">
                                    <label for="ref">Ref</label>
                                    <input type="text" class="form-control" id="ref" name="ref" value="{{ $purchase->ref }}" readonly>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="remarks">Remarks</label>
                                    <input type="text" class="form-control" id="remarks" name="remarks" value="{{ $purchase->remarks }}" readonly>
                                </div>

                                <div class="form-group col-md-8">
                                    <label for="reason">Reason</label>
                                    <input type="text" class="form-control" id="reason" name="reason">
                                </div>
                            </div>
                          </form>
                    </div>

                    
                </div>

            </div>
            <div class="col-md-4">
                

                <div  class="box box-default box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Product Purchase Item</h3>
                        <!-- /.box-tools -->
                    </div>

                    <table class="table table-hover"  id="purchaseDtlTBL">
                        <thead>
                            <tr>
                                <th class="text-center">Part No</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($purchase->purchasehistory as $key => $prdct)
                             @php
                                 $returnqty = \App\Models\PurchaseReturn::where('purchase_history_id',$prdct->id)->sum('returnqty');
                             @endphp
                                <tr>

                                    <td class="text-center">
                                        <input type="text" value="{{ $prdct->product->part_no }}" class="form-control" readonly>
                                        
                                    </td>
                                    <td class="text-center">
                                        <input type="text" value="{{ $prdct->product->productname }}" class="form-control" readonly>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" value="{{ $prdct->quantity - $returnqty }}" min="1" class="form-control" readonly >
                                        
                                    </td>
                                    <td class="text-center">

                                        <span class="btn btn-success btn-sm returnThis" id="returnThis" pid="{{ $prdct->product_id }}" part_no="{{ $prdct->product->part_no }}" pname="{{ $prdct->product->productname }}" phisid="{{ $prdct->id }}" unit_price="{{ $prdct->purchase_price}}" vat_percent="{{ $prdct->vat_percent}}" vat_amount="{{ $prdct->total_vat}}" total_amount="{{ $prdct->total_amount_per_unit}}" total_amount_with_vat="{{ $prdct->total_amount_with_vat}}" availableqty="{{ $prdct->quantity - $returnqty }}" > <i class='fa fa-arrow-right'></i> </span>
                                    
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>


                </div>
                <!-- /.box-body -->
                <!-- /.box -->
            </div>
            <div class="col-md-8">
                
                <div  class="box box-default box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Product Return Item</h3>
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

                        <tbody  id="returninner">
                            

                        </tbody>
                    </table>

                    <div class="box-header with-border">
                        <button class="btn btn-success btn-md center-block" id="purchasereturnBtn" type="submit"><i class="fa fa-plus-circle"></i> Submit </button>
                    </div>


                </div>
                <!-- /.box-body -->


            </div>



        </div>
    </div>



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


        // unit price calculation

        $("body").delegate(".rquantity","keyup",function(event){
            event.preventDefault();
            row = $(this).parent().parent();
            qty = row.find('.rquantity').val();
            avqty = row.find('.availableqty').val();
            availableqty = parseInt(avqty);
            if ( qty  > availableqty) {
                alert('Please Input lower quantity !!');
                row.find('.rquantity').val('1');
            }
        })

        // unit price calculation end

        // calculation onchange

        $("body").delegate(".rquantity","change",function(event){
            event.preventDefault();

            var row = $(this).parent().parent();
            qty = row.find('.rquantity').val();
            avqty = row.find('.availableqty').val();
            availableqty = parseInt(avqty);
            if ( qty  > availableqty) {
                alert('Please Input lower quantity !!');
                row.find('.rquantity').val('1');
            }
        })

        // calculation onchange end


        

        // return stock
        $("#purchaseDtlTBL").on('click','#returnThis', function(){

                product = $(this).attr('pid');
                var product_id = $("input[name='product_id[]']")
                             .map(function(){return $(this).val();}).get();

                product_id.push(product);
                seen = product_id.filter((s => v => s.has(v) || !s.add(v))(new Set));

                if (Array.isArray(seen) && seen.length) {
                    return;
                }

                availableqty = $(this).attr('availableqty');
                product_id = $(this).attr('pid');
                part_no = $(this).attr('part_no');
                productname = $(this).attr('pname');
                phisid = $(this).attr('phisid');
                unit_price = $(this).attr('unit_price');
                vat_percent = $(this).attr('vat_percent');
                vat_amount = $(this).attr('vat_amount');
                total_amount = $(this).attr('total_amount');
                total_amount_with_vat = $(this).attr('total_amount_with_vat');

                var markup = '<tr><td class="text-center"><input type="text" id="pert_no" name="pert_no[]" value="'+part_no+'" class="form-control" readonly></td><td class="text-center"><input type="text" id="productname" name="productname[]" value="'+productname+'" class="form-control" readonly><input type="hidden" id="product_id" name="product_id[]" value="'+product_id+'" class="form-control" readonly><input type="hidden" id="purchase_his_id" name="purchase_his_id[]" value="'+phisid+'" class="form-control" readonly></td><td class="text-center"><input type="number" id="quantity" name="quantity[]" value="1" min="1" class="form-control rquantity" ><input type="hidden" id="availableqty" name="availableqty[]" value="'+availableqty+'" class="form-control availableqty" readonly></td><td class="text-center"><input type="number" id="unit_price" name="unit_price[]" value="'+unit_price+'" min="1" class="form-control unit-price" readonly></td><td class="text-center"><input type="number" id="vat_percent" name="vat_percent[]" min="0" readonly  max="20" class="form-control uvatpercent" value="'+vat_percent+'"></td><td class="text-center"><input type="text" id="vat_amount" name="vat_amount[]" value="'+vat_amount+'" class="form-control totalvat" readonly></td><td class="text-center"><input type="text" id="total_amount" name="total_amount[]" value="'+total_amount+'" class="form-control total" readonly> </td><td class="text-center"><input type="text" id="total_amount_with_vat" value="'+total_amount_with_vat+'" name="total_amount_with_vat[]" class="form-control totalwithvat" readonly></td><td class="text-center"><div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div></td></tr>';
                $("table #returninner ").append(markup);
                
            });
        // return stock end



        // submit to purchase 
        var purchasereturnurl = "{{URL::to('/admin/purchase-return')}}";

            $("body").delegate("#purchasereturnBtn","click",function(event){
                event.preventDefault();

                
                var branch_id = $("#branch_id").val();
                var vendor_id = $("#supplier_id").val();
                var purchase_id = $("#purchase_id").val();
                var reason = $("#reason").val();
                var date = $("#date").val();
                
                var product_id = $("input[name='product_id[]']")
                    .map(function(){return $(this).val();}).get();
                    
                var quantity = $("input[name='quantity[]']")
                    .map(function(){return $(this).val();}).get();

                    
                var purchase_his_id = $("input[name='purchase_his_id[]']")
                    .map(function(){return $(this).val();}).get();


                $.ajax({
                    url: purchasereturnurl + '/' + purchase_id,
                    method: "POST",
                    data: {branch_id,date,reason,product_id,purchase_his_id,quantity,vendor_id},

                    success: function (d) {
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                            pagetop();
                        }else if(d.status == 300){
                            $(".ermsg").html(d.message);
                            pagetop();
                            window.setTimeout(function(){location.href = "{{ route('admin.product.purchasehistory')}}"},2000)
                            
                        }
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });

        });
        // submit to purchase end



    });  
    </script>

@endsection
