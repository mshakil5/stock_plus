@extends('admin.layouts.master')
@section('content')


@if (session('message'))
<div class="alert alert-success">
    {{ session('message') }}
</div>
@endif
<div class="alert alert-warning">
<h5>Here You will add product to stock which products are exisiting in the system. </h5>
</div>

{{-- @submit.prevent="saveTranserProduct()" --}}
<div>
{{-- <form action="{{ route('stock-re-entry') }}" method="POST"> --}}
    {{-- {{ csrf_field() }} --}}

    <div class="container">
        
    <div class="row">

        <div class="col-md-6">
            <div class="box box-solid box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Stock Re-Entry</h3>
                </div>

                <div class="box-body">
                    
                    <div class="panel-group" id="accordion">

                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse2"
                                   style="color:white;">
                                    <h4 class="panel-title">
                                        Select Products
                                        <i class="fa fa-arrow-circle-down pull-right"></i>
                                    </h4>
                                </a>
                            </div>
                            <div id="collapse2" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <div class="table-responsive re-product">

                                        @component('components.widget')
                                        @slot('title')
                                        @endslot
                                        @slot('description')
                                        @endslot
                                        @slot('body')
                                            @component('components.table')
                                                @slot('tableID')
                                                    productsTBL
                                                @endslot
                                                @slot('head')
                                                    <th>P.ID</th>
                                                    <th>Product</th>
                                                    <th>Part No</th>
                                                    <th>Code</th>
                                                    <th>Sell Price</th>
                                                    <th>Action</th>
                                                @endslot
                                            @endcomponent
                                        @endslot
                                    @endcomponent

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="box box-widget widget-user-2">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <p><strong class="text-danger"></strong></p>
                        
                    </div>
                    <div id="ermsg" class="ermsg"></div>
                </div>
                <div class="box-body">

                    {{-- <form id="stockform"> --}}

                    <div class="box-content">
                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">Product Name</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="productname" id="productname" readonly="readonly" value="">
                                <input type="hidden" name="product_id" id="product_id" value="">
                                <input type="hidden" name="history_id" id="history_id" value="">
                            </div>
                        </div>

                        {{-- <div class="form-group row">
                            <label for="" class="form-label col-sm-6">Open Stock</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="open_stock" id="open_stock">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">In Hand</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="in_hand" id="in_hand">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">On Sales Order</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="on_sales_order" id="on_sales_order">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">On Purchase Order</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="on_purchase_order" id="on_purchase_order">
                            </div>
                        </div> --}}

                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">Type</label>
                            <div class="col-sm-6">
                                
                                <select name="type" id="type" class="form-control">
                                    <option value="">Select...</option>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">Branch</label>
                            <div class="col-sm-6">
                                
                                <select name="branch" id="branch" class="form-control">
                                    <option value="">Select...</option>
                                    @foreach ($branches as $branch)
                                    <option value="{{$branch->id}}">{{$branch->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">Supplier/Vendor</label>
                            <div class="col-sm-6">
                                <select name="vendor" id="vendor" class="form-control">
                                    <option value="">Select...</option>
                                    @foreach ($vendors as $vendor)
                                    <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">Quantity</label>
                            <div class="col-sm-6">
                                <input type="number" name="quantity" id="quantity" class="form-control" placeholder="0" >
                                <small class="error"></small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">Purchase Price per unit</label>
                            <div class="col-sm-6">
                                <input type="number" name="purchasePrice" id="purchasePrice" class="form-control allownumericwithoutdecimal" >
                                <small class="error">  </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">Selling Price</label>
                            <div class="col-sm-6">
                                <input type="number" name="sellingprice" id="sellingprice" class="form-control" readonly="readonly">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">Total Price</label>
                            <div class="col-sm-6">
                                <input type="number" name="totalPrice" id="totalPrice" class="form-control" readonly="readonly">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">Last Purchase Price</label>
                            <div class="col-sm-6">
                                <input type="number" name="last_purchase_price" id="last_purchase_price" class="form-control" readonly="readonly">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="" class="form-label col-sm-6">Expire Date</label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" id="exp_date" name="exp_date">
                                <small class="error"></small>
                            </div>
                        </div>
                        
                            <button id="addStock" type="submit" class="btn btn-primary btn-sm">Re-Stock Product </button>
                            <button id="updateStock" type="submit" class="btn btn-primary btn-sm">Update </button>
                            
                    </div>
                {{-- </form> --}}
                </div>
            </div>
            <!-- /.widget-user -->
        </div>
        <div class="col-md-1">
        </div>
    </div>
    </div>

    {{-- row div end  --}}

    {{-- purchase history show here  --}}
    <div class="container">
        
        <div class="row">
    
            <div class="col-md-11">
                <div class="box box-widget widget-user-2">
                    
                    <div class="box-body">
                        <div class="alert alert-success">
                            <h5>Old Purchase Products</h5>
                        </div>
                        <table id="hist_table" class="histry table table-striped">
                            <thead>
                            <tr>
                                <th><i class="icon-sort"></i>Date</th>
                                <th><i class="icon-sort"></i>Products</th>
                                <th><i class="icon-sort"></i>Branch</th>
                                <th class="text-center"><i class="icon-sort"></i>Qty.</th>
                                <th class="text-center"><i class="icon-sort"></i>Price</th>
                                <th class="text-center"><i class="icon-sort"></i>Expire Date</th>
                                <th class="text-center"><i class="icon-sort"></i>Action</th>
                            </tr>
                            </thead>
                            <tbody>
            
                            </tbody>
                            
                        </table>
                    </div>
                </div>
                <!-- /.widget-user -->
            </div>
            <div class="col-md-1">
            </div>
        </div>
        </div>
    {{-- purchase history show end --}}
</div>





{{-- <link rel="stylesheet" href="{{ asset('css/vue-select.css') }}"> --}}

<script>
$(function () {
    $('.select2').select2();
    // $('#productsTBL').DataTable({});
    $('#hist_table').DataTable({
        language: {
            "zeroRecords": " "
        },
    });

});
</script>


<script>
$(".allownumericwithoutdecimal").on("keypress keyup blur", function (event) {
    $(this).val($(this).val().replace(/[^\d].+/, ""));
    if ((event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});
</script>


@endsection
    
@section('script')

{{-- <script src="{{ asset('admin/js/jquery-3.3.1.min.js')}}"></script> --}}
<script>






    $(document).ready(function () {

        $("#updateStock").hide(100);

        var filterstockurl = "{{URL::to('/admin/filter-stock-all')}}";
            $('.select2').select2();
            var t = $('#productsTBL').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: filterstockurl,
                    data: function (d) {
                        console.log(d);
                        
                    }
                },
                deferRender: true,
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'productname', name: 'productname'},
                    {data: 'part_no', name: 'part_no'},
                    {data: 'category.name', name: 'category.name'},
                    {data: 'selling_price', name: 'selling_price'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });





        var historyurl = "{{URL::to('/admin/stock-history')}}";
        $("#productsTBL").on('click','#addThisStock', function(){
                // $("#stockform").trigger("reset");
                // alert("btn work");
            $("#updateStock").hide(100);
            $("#addStock").show(100);
                productid = $(this).attr('pid');
                productname = $(this).attr('pname');
                document.getElementById("productname").value = productname;
                document.getElementById("product_id").value = productid;
                // console.log(codeid);


                info_url = historyurl + '/'+ productid;

                var table = $(".histry tbody");

                $.get(info_url,{},function(d){
                    table.empty();
                    $.each(d, function (a, b) {
                        // table.empty();
                        table.append("<tr><td class='text-left'>" + b.created_at + "</td>" +
                            "<td class='text-success text-left'>" + b.productname + "</td>" +
                            "<td class='text-success text-left'>" + b.branchname + "</td>" +
                            "<td class='text-success text-center'>" + b.quantity + "</td>" +
                            "<td class='text-center'>" + b.purchase_price + "</a></td>" +
                            "<td class='text-center'>" + b.exp_date + "</td>" +
                            "<td class='text-center'> <span class='btn btn-success btn-sm' id='editBtn' histryid='"+ b.id +"' pid='"+ b.product_id +"' qty='"+ b.quantity +"' price='"+ b.purchase_price +"' tcost='"+ b.total_cost +"' expdate='"+ b.exp_date +"'  branch='"+ b.branch_id +"' pname='"+ b.productname +"'> <i class='fa fa-pencil'></i> </span> </td>" +
                            "</tr>");
                    });
                });
            }); 


        // update stock

        $("#hist_table").on('click','#editBtn', function(){
            
            pagetop();
            $("#updateStock").show(100);
            $("#addStock").hide(100);
                producthistryid = $(this).attr('histryid');
                productid = $(this).attr('pid');
                productname = $(this).attr('pname');
                productqty = $(this).attr('qty');
                productprice = $(this).attr('price');
                totalcost = $(this).attr('tcost');
                expdate = $(this).attr('expdate');
                branch = $(this).attr('branch');

                console.log(branch);
                document.getElementById("productname").value = productname;
                document.getElementById("product_id").value = productid;
                document.getElementById("history_id").value = producthistryid;
                document.getElementById("branch").value = branch;
                document.getElementById("quantity").value = productqty;
                // document.getElementById("purchasePrice").value = productprice;
                document.getElementById("totalPrice").value = totalcost;
                document.getElementById("exp_date").value = expdate;
                
            }); 
            
            
        // calculation start 
        
        $("#purchasePrice").keyup(function(){
            //  alert("btn work");
                var total=0;
                var quantity = Number($("#quantity").val());
                var purchasePrice = Number($("#purchasePrice").val());
                var sellingprice = purchasePrice + purchasePrice/100 * 5 ;
                
                var total_amount = quantity * purchasePrice;
                $('#totalPrice').val(total_amount.toFixed(2));
                $('#sellingprice').val(sellingprice.toFixed(2));
            });
            //calculation end



            //header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //
            var url = "{{URL::to('/admin/add-stock')}}";
            // console.log(url);
            $("#addStock").click(function(){
            //   alert("#addBtn");


                    var form_data = new FormData();
                    form_data.append("productname", $("#productname").val());
                    form_data.append("type", $("#type").val());
                    form_data.append("product_id", $("#product_id").val());
                    form_data.append("branch", $("#branch").val());
                    form_data.append("vendor", $("#vendor").val());
                    form_data.append("quantity", $("#quantity").val());
                    form_data.append("purchasePrice", $("#purchasePrice").val());
                    form_data.append("sellingprice", $("#sellingprice").val());
                    form_data.append("totalPrice", $("#totalPrice").val());
                    form_data.append("exp_date", $("#exp_date").val());

                    $.ajax({
                      url: url,
                      method: "POST",
                      contentType: false,
                      processData: false,
                      data:form_data,
                      success: function (d) {
                          if (d.status == 303) {
                              $(".ermsg").html(d.message);
                          }else if(d.status == 300){
                              $(".ermsg").html(d.message);
                                window.setTimeout(function(){location.reload()},2000)
                          }
                      },
                      error: function (d) {
                          console.log(d);
                      }
                  });
                
            });


            var upsurl = "{{URL::to('/admin/update-stock')}}";
            $("#updateStock").click(function(){

                    var form_data = new FormData();
                    form_data.append("history_id", $("#history_id").val());
                    form_data.append("productname", $("#productname").val());
                    form_data.append("product_id", $("#product_id").val());
                    form_data.append("branch", $("#branch").val());
                    form_data.append("vendor", $("#vendor").val());
                    form_data.append("quantity", $("#quantity").val());
                    form_data.append("purchasePrice", $("#purchasePrice").val());
                    form_data.append("totalPrice", $("#totalPrice").val());
                    form_data.append("exp_date", $("#exp_date").val());

                    $.ajax({
                      url: upsurl,
                      method: "POST",
                      contentType: false,
                      processData: false,
                      data:form_data,
                      success: function (d) {
                          if (d.status == 303) {
                              $(".ermsg").html(d.message);
                          }else if(d.status == 300){
                              $(".ermsg").html(d.message);
                                window.setTimeout(function(){location.reload()},2000)
                          }
                      },
                      error: function (d) {
                          console.log(d);
                      }
                  });
                
            });

    });
</script>
@endsection