@extends('admin.layouts.master')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.css" integrity="sha512-3uVpgbpX33N/XhyD3eWlOgFVAraGn3AfpxywfOTEQeBDByJ/J7HkLvl4mJE1fvArGh4ye1EiPfSBnJo2fgfZmg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .bootstrap-tagsinput {
        width: 100% !important;
    }
    
</style>
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
                        <h3 class="box-title">Edit Product</h3>
                        <!-- /.box-tools -->
                    </div>
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

                        

                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-hover">
                                        
                                        <tr>
                                            <td><label class="control-label">Product Name*</label></td>
                                            <td colspan="2"><input name="product" id="product" type="text" class="form-control" maxlength="50px" placeholder="Enter Product" required="required" value="{{ $product->productname }}"/>
                                                <input type="hidden" id="productid" value="{{ $product->id }}">
                                                @if ($errors->has('product'))
                                                    <span class="text-danger">{{ $errors->first('product') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Part No</label></td>
                                            <td colspan="2"><input name="part_no" id="part_no" type="text" class="form-control"
                                                       maxlength="50px" placeholder="Enter Part no" required="required" value="{{ $product->part_no }}"/>
                                                @if ($errors->has('part_no'))
                                                    <span class="text-danger">{{ $errors->first('part_no') }}</span>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Code</label></td>
                                            <td colspan="2">
                                                <select name="pcategoryselect" id="pcategoryselect" class="form-control select2">
                                                    @foreach (\App\Models\Category::select('id','categoryid','name')->get() as $cat)
                                                    <option value="{{ $cat->id }}" @if ($cat->id == $product->category_id) selected @endif>{{ $cat->name }}-{{ $cat->categoryid}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Brand</label></td>
                                            
                                            <td colspan="2">
                                                <select name="pbrandselect" id="pbrandselect" class="form-control select2">
                                                    @foreach (\App\Models\Brand::select('id','brandid','name')->get() as $brand)
                                                    <option value="{{ $brand->id }}" @if ($brand->id == $product->brand_id) selected @endif>{{ $brand->name }}-{{ $brand->brandid}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Unit</label></td>
                                            <td colspan="2"><input name="unit" id="unit" type="text" class="form-control"
                                                       maxlength="50px" placeholder="pcs,ltr,kg etc" required="required" value="{{ $product->unit }}"/>
                                                @if ($errors->has('unit'))
                                                    <span class="text-danger">{{ $errors->first('unit') }}</span>
                                                @endif
                                            </td>
                                        </tr>

                                        
                                        
                                        <tr>
                                            <td><label class="control-label">Sell price*</label></td>
                                            <td colspan="2"><input name="sell_price" id="sell_price" type="number" class="form-control" oninput="this.value=(parseInt(this.value)||0)" value="{{ $product->selling_price }}" maxlength="50px" placeholder="Enter price" required="required" />
                                                @if ($errors->has('sell_price'))
                                                    <span class="text-danger">{{ $errors->first('sell_price') }}</span>
                                                @endif
                                            </td>
                                        </tr>


                                        <tr style="display: none">
                                            <td><label for="image">Image*</label></td>
                                            <td colspan="2">
                                                <input type="file" id="image" name="image">
                                                <small class="text-danger">Please upload barcode image only in png, jpg format.</small>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Alternative Product</label></td>
                                            <td>
                                                <select name="alternative[]" id="alternative" class="form-control select2" multiple>
                                                    
                                                    @foreach ($alternatives as $item)

                                                        @foreach ($product->alternativeproduct as $alt)
                                                            <option value="{{ $item->id }}" @if ($item->id == $alt->alternative_product_id) selected @endif>{{ $item->productname }} - {{ $item->part_no }}</option>
                                                        @endforeach
                                                        <option value="{{ $item->id }}">{{ $item->productname }} - {{ $item->part_no }}</option>
                                                    
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>

                                        <br/>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <table class="table table-hover">
                                        <tr>
                                            <td><label class="control-label">Model</label></td>
                                            <td><input name="model" id="model" type="text" class="form-control" maxlength="50px" placeholder="Enter model" required="required" value="{{ $product->model }}"/>
                                                @if ($errors->has('model'))
                                                    <span class="text-danger">{{ $errors->first('model') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Location</label></td>
                                            <td><input name="location" id="location" type="text" class="form-control" maxlength="50px" placeholder=""  required="required" value="{{ $product->location }}"/>
                                                @if ($errors->has('location'))
                                                    <span class="text-danger">{{ $errors->first('location') }}</span>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Group</label></td>
                                            <td>
                                                <select name="group_id" id="group_id" class="form-control select2">
                                                    @foreach (\App\Models\Group::select('id','groupid','name')->get() as $group)
                                                    <option value="{{ $group->id }}" @if ($group->id == $product->group_id) selected @endif>{{ $group->name }}-{{ $group->groupid}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Vat(%)</label></td>
                                            <td><input name="vat_percent" id="vat_percent" type="number" class="form-control" maxlength="50px" placeholder="" required="required" value="{{ $product->vat_percent }}" />
                                                @if ($errors->has('vat_percent'))
                                                    <span class="text-danger">{{ $errors->first('vat_percent') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        


                                        <tr>
                                            <td><label class="control-label">Remarks</label></td>
                                            <td>
                                                <textarea name="productdesc" id="pro_desc" class="form-control" rows="4">{{ $product->description }}
                                                    
                                                </textarea>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td><label class="control-label">Replacement</label></td>
                                            <td>
                                                <input type="text" name="replacement" id="replacement" class="form-control"  rows="3" value="{{ $product->replacement }}"  />
                                            </td>
                                        </tr>

                                        
                                        <br/>
                                    </table>
                                    <br>
                                        <button class="btn btn-success btn-md center-block  update-btn" type="submit"><i class="fa fa-plus-circle"></i> Update Product
                                        </button>
                                </div>


                                {{-- end  --}}
                            </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <!-- /.box -->
            </div>
            <div class="col-md-3">
                <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user-2">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header">

                        <h3 class="widget-user-username">Product Info</h3>

                    </div>
                    <div class="box-body">
                        <div class="info-inner">
                            <!-- <form class="form"> -->
                                <div class="catermsg"></div>
                            <div class="form-group mx-sm-3 mb-2">
                                <label for="category" class="">Code</label>
                                <br>

                                <input type="text" class="" id="categoryid" placeholder="ID">
                                <input type="text" class="" id="category" placeholder="Type">
                                <br>
                                <button onclick="save_category()" type="submit" class="btn btn-primary mb-2" style="margin-top: 5px;">Save
                                    </button>
                            </div>
                            <div class="brandermsg"></div>
                            <div class="form-group mx-sm-3 mb-2">
                                <label for="brand" class="">Brand</label>
                                <br>
                                <input type="text" class="" id="brandid" placeholder="ID">
                                <input type="text" class="" id="brand" placeholder="Brandname">
                                <br>
                                    <button onclick="save_brand()" type="submit" class="btn btn-primary mb-2" style="margin-top: 5px;">Save
                                    </button>
                            </div>
                            <div class="grpermsg"></div>
                            <div class="form-group mx-sm-3 mb-2">
                                <label for="group" class="">Group</label>
                                <br>
                                <input type="text" class="" id="groupid" placeholder="ID">
                                <input type="text" class="" id="group" placeholder="Groupname">
                                <br>
                                    <button onclick="save_group()" type="submit" class="btn btn-primary mb-2" style="margin-top: 5px;">Save
                                    </button>
                            </div>
                            
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>

    

    @endsection
    
@section('script')

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.js" integrity="sha512-cG3ZNAM4Uv2CO/rbBbA7v24d5COF/P5QgDE5HzfoM41uRK7wTIXtxy4UO9ZKE0bjUprMr92Lhv5O6CWdnIZZ/w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.js" integrity="sha512-P2Z/b+j031xZuS/nr8Re8dMwx6pNIexgJ7YqcFWKIqCdbjynk4kuX/GrqpQWEcI94PRCyfbUQrjRcWMi7btb0g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    $(document).ready(function() { 

        $('#replacement').tagsinput({
            typeahead: {
                source: ['Amsterdam', 'Washington', 'Sydney', 'Beijing', 'Cairo'],
                    afterSelect: function() {
                        this.$element[0].value = '';
                    }
            }
            });
        });
</script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
            var categoryTBL = $('.all-category').DataTable({
                'responsive': true,
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': true
            });
            var brandTBL = $('.all-brand').DataTable({
                'responsive': true,
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': true
            });


            // product update
            var updateurl = "{{URL::to('/admin/update-product-details')}}";
            $(document).on('click', '.update-btn', function () {

                let id = $("#productid").val();
                let productname = $("#product").val();
                let selling_price = $("#sell_price").val();
                let brand_id = $("#pbrandselect").val();
                let category_id = $("#pcategoryselect").val();
                let part_no = $("#part_no").val();


                let group = $("#group_id").val();
                let unit = $("#unit").val();
                let model = $("#model").val();
                let location = $("#location").val();
                let vat_percent = $("#vat_percent").val();
                let description = $("#pro_desc").val(); 
                let replacement = $("#replacement").val(); 
                let alternative = $("#alternative").val(); 
                // var alternative = $("input[name='alternative[]']")
                //     .map(function(){return $(this).val();}).get();

                var r = confirm("Are You Sure Want To Updated ?");
                if (r == false) {
                    return false;
                }
                if (productid == "") {
                    alert("Product is invalid");
                    return;
                }
                if (selling_price < 0) {
                    alert("Negetive Values are not ALLOWED");
                    return;
                }
                if (selling_price == "") {
                    alert("Please Provide Selling Price");
                    return;
                }
                if (brand_id == "") {
                    alert("Please Provide Brand");
                    return;
                }
                if (category_id == "") {
                    alert("Please Provide Code");
                    return;
                }
                if (productname == "") {
                    alert("Please Provide Product Name");
                    return;
                } else {


                    $.ajax({
                        data: {id: id,productname:productname,selling_price:selling_price,category_id:category_id,brand_id:brand_id,part_no:part_no,unit:unit,model:model,location:location,vat_percent:vat_percent,group:group,description:description,replacement:replacement,alternative:alternative,},
                        url: updateurl,
                        type: 'POST',
                        beforeSend: function (request) {
                            return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                        },
                        success: function (response) {
                            console.log(response);
                            showSnakBar();
                        },
                        error: function (err) {
                            console.log(err);
                            alert("Something Went Wrong, Please check again");
                        }
                    });
                }
            });
            // product update end

        });

        

  

        var categoryurl = "{{URL::to('/admin/category')}}";
        function save_category() {
            if ($("#category").val() == "") {
                alert("Please Provide Category Name");
            }
            if ($("#categoryid").val() == "") {
                alert("Please Provide Category ID");
            } else {
                
                 var categoryid = $("#categoryid").val();
                 var category = $("#category").val();
                $.ajax({
                    data: {
                        category:category,categoryid:categoryid
                    },
                    url: categoryurl,
                    type: 'POST',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        if (response.status == 303) {
                            $(".catermsg").html(response.message);
                        } else {
                            
                        showSnakBar('Saved Successfully');
                        $('#pcategoryselect').append('<option value="'+response.id+'">'+response.name+'-'+response.categoryid+'</option>');
                        $("#category").val("");
                        $("#categoryid").val("");
                        $("#pcategoryselect").val(response.id);
                        }
                    },
                    error: function (err) {
                        console.log(err);
                        alert("Something Went Wrong, Please check again");
                    }
                });
            }
        }

        var brandurl = "{{URL::to('/admin/brand')}}";
        function save_brand() {
            if ($("#brand").val() == "") {
                alert("Please Provide Brand Name");
            }
            if ($("#brandid").val() == "") {
                alert("Please Provide Brand ID");
            } else {
                
                var brand = $("#brand").val()
                var brandid = $("#brandid").val()
                $.ajax({
                    data: {
                        brand: brand, brandid:brandid
                    },
                    url: brandurl,
                    type: 'POST',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {

                        if (response.status == 303) {
                            $(".brandermsg").html(response.message);
                        } else {
                            
                            showSnakBar('Saved Successfully');
                            $('#pbrandselect').append('<option value="'+response.id+'">'+response.name+'-'+response.brandid+'</option>');
                            $("#brand").val("")
                            $("#brandid").val("")
                            $("#pbrandselect").val(response.id)
                        }

                        
                    },
                    error: function (err) {
                        console.log(err);
                        alert("Something Went Wrong, Please check again");
                    }
                });
            }
        }


        var groupurl = "{{URL::to('/admin/group')}}";
        function save_group() {
            if ($("#group").val() == "") {
                alert("Please Provide Group Name");
            }
            if ($("#groupid").val() == "") {
                alert("Please Provide Group ID");
            } else {
                
                var group = $("#group").val()
                var groupid = $("#groupid").val()
                $.ajax({
                    data: {
                        group: group, groupid:groupid
                    },
                    url: groupurl,
                    type: 'POST',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        

                        if (response.status == 303) {
                            $(".grpermsg").html(response.message);
                        } else {
                            
                            showSnakBar('Saved Successfully');
                            $('#group_id').append('<option value="'+response.id+'">'+response.name+'-'+response.groupid+'</option>');
                            $("#group").val("")
                            $("#groupid").val("")
                            $("#group_id").val(response.id)
                        }
                    },
                    error: function (err) {
                        console.log(err);
                        alert("Something Went Wrong, Please check again");
                    }
                });
            }
        }

        
        

        
    </script>

@endsection
