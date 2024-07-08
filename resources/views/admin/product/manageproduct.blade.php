

@extends('admin.layouts.master')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.css" integrity="sha512-3uVpgbpX33N/XhyD3eWlOgFVAraGn3AfpxywfOTEQeBDByJ/J7HkLvl4mJE1fvArGh4ye1EiPfSBnJo2fgfZmg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .bootstrap-tagsinput {
        width: 100% !important;
    }
    
</style>


    <?php
    $user_id = Session::get('categoryEmployId');
    $branch_id = Session::get('branch_id');
    ?>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

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
    <div class="row">
        <div class="col-md-12">
            @component('components.widget')
                @slot('title')
                    Product details
                @endslot
                @slot('description')
                    Particular products information
                @endslot
                @slot('body')
                    @component('components.table')
                        @slot('tableID')
                            productsTBL
                        @endslot
                        @slot('head')
                            <th>Product</th>
                            <th>Part No</th>
                            <th>Code</th>
                            <th>Brand</th>
                            <th>Sell price</th>
                            <th>Model</th>
                            <th>Location</th>
                            <th><i class=""></i> Action</th>
                        @endslot
                    @endcomponent
                @endslot
            @endcomponent
        </div>
        {{-- <div class="col-md-4">
            <div class="box-inner">
                <div class="alert alert-success"><strong>Filter Data: </strong></div>
                <div class="box-content">
                    <label for="brand"> Select Category</label>
                    <select class="custom-select select2" id="categorydropdown2">
                    </select>

                </div>
                <div class="box-content">
                    <label for="brand"> Select Brand</label>
                    <select class="custom-select select2" id="branddropdown2">
                    </select>
                </div>
            </div>
        </div> --}}
    </div>

    <div class="modal fade bd-example-modal-lg" id="editproductModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Update product details </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-hover">
                                <input type="hidden" id="productid" value="">
                                
                                <tr>
                                    <td><label class="control-label">Part No</label></td>
                                    <td colspan="2"><input name="part_no" id="part_no" type="text" class="form-control" maxlength="50px" required="required"/>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td><label class="control-label">Product Name*</label></td>
                                    <td colspan="2"><input name="productname" id="productname" type="text" class="form-control" maxlength="50px" required="required" /> 
                                    </td>
                                </tr>


                                <tr>
                                    <td><label class="control-label">Code</label></td>
                                    <td colspan="2">
                                        <select name="category_id" id="category_id" class="form-control select2">
                                            <option value="">Select</option>
                                            @foreach (\App\Models\Category::all() as $cat)
                                            <option value="{{$cat->id}}">{{ $cat->name }}-{{ $cat->categoryid}}</option>
                                                
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td><label class="control-label">Brand</label></td>
                                    </td>
                                    <td colspan="2">
                                        <select name="brand_id" id="brand_id" class="form-control select2">
                                            <option value="">Select</option>
                                            @foreach (\App\Models\Brand::all() as $cat)
                                            <option value="{{$cat->id}}">{{ $cat->name }}-{{ $cat->brandid}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="control-label">Unit</label></td>
                                    <td colspan="2"><input name="unit" id="unit" type="text" class="form-control" maxlength="50px" required="required"/>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td><label class="control-label">Sell price*</label></td>
                                    <td colspan="2"><input name="sell_price" id="sell_price" type="number" class="form-control" oninput="this.value=(parseInt(this.value)||0)" maxlength="50px"  required="required" />
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="control-label">Alternative Product</label></td>
                                    <td colspan="2">
                                        <select name="alternative[]" id="alternative" class="form-control select2" multiple>
                                            @foreach (\App\Models\Product::all() as $item)
                                            <option value="{{ $item->id }}">{{ $item->productname }} - {{ $item->part_no }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr style="display: none">
                                    <td><label for="international_barcode_image">Image*</label></td>
                                    <td colspan="2">
                                        <input type="file" id="international_barcode_image" name="international_barcode_image"> 
                                        <small class="text-danger">Please upload barcode image only in png, jpg format.</small>
                                    </td>
                                </tr>

                                <br/>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-hover">
                                <tr>
                                    <td><label class="control-label">Model</label></td>
                                    <td><input name="model" id="model" type="text" class="form-control" maxlength="50px" required="required" />
                                         
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="control-label">Group</label></td>
                                    <td>
                                        <select name="group" id="group" class="form-control select2">
                                            <option value="">Select</option>
                                            @foreach (\App\Models\Group::all() as $group)
                                            <option value="{{$group->id}}">{{ $group->name }}-{{ $group->groupid}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td><label class="control-label">Location</label></td>
                                    <td><input name="location" id="location" type="text" class="form-control" maxlength="50px" required="required" />
                                    </td>
                                </tr>

                                <tr>
                                    <td><label class="control-label">Vat Percent</label></td>
                                    <td><input name="vat" id="vat" type="number" class="form-control" maxlength="50px"  required="required" />
                                    </td>
                                </tr>

                                <tr>
                                    <td><label class="control-label">Remarks</label></td>
                                    <td>
                                        <textarea name="productdesc" id="pro_desc" class="form-control"
                                          rows="4"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="control-label">Replacement</label></td>
                                    <td>
                                        <input type="text" name="replacement" id="replacement" class="form-control"  rows="3" value=""  />
                                    </td>
                                </tr>
                                <br/>
                            </table>
                            
                        </div>


                        {{-- end  --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-primary update-btn" data-dismiss="modal">
                        Save
                    </button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
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
        var filterurl = "{{URL::to('/admin/filter-all')}}";
            $('.select2').select2();
            var t = $('#productsTBL').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: filterurl,
                    data: function (d) {
                        // console.log(d);
                        d.category = $("#categorydropdown2").val();
                        d.brand = $("#branddropdown2").val();
                    }
                },
                deferRender: true,
                columns: [
                    // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    
                    {data: 'productname', name: 'productname'},
                    {data: 'part_no', name: 'part_no'},
                    {data: 'category.name', name: 'category.name'},
                    {data: 'brand.name', name: 'brand.name'},
                    {data: 'selling_price', name: 'selling_price'},
                    {data: 'model', name: 'model'},
                    {data: 'location', name: 'location'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            

            $(document).on('keyup change', '#categorydropdown2,#branddropdown2', function () {
                t.draw();
            });
            var editurl = "{{URL::to('/admin/product-info')}}";
            $(document).on('click', '.edit-btn', function () {
                let id = $(this).val();
                $.ajax({
                    url: editurl + '/' + id,
                    type: 'GET',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        console.log(response);
                        $("#productid").val(response.id);
                        $("#productname").val(response.productname);
                        $("#part_no").val(response.part_no);
                        $("#unit").val(response.unit);
                        $("#model").val(response.model);
                        $("#location").val(response.location);
                        $("#category_id").val(response.category_id).trigger('change');
                        $("#brand_id").val(response.brand_id).trigger('change');
                        $("#group").val(response.group_id).trigger('change');
                        $("#vat").val(response.vat_percent);
                        $("#pro_desc").html(response.description);
                        $("#sell_price").val(response.selling_price);
                        $("#alternative").val(response.alternativeproduct.alternative_product_id);
                        
                    },
                    error: function (err) {
                        console.log(err);
                        alert("Something Went Wrong, Please check again");
                    }
                });
            });

            
            var updateurl = "{{URL::to('/admin/update-product-details')}}";
            $(document).on('click', '.update-btn', function () {

                let productid = $("#productid").val();
                let productname = $("#productname").val();
                let productsellingprice = $("#sell_price").val();
                let brand_id = $("#brand_id").val();
                let category_id = $("#category_id").val();
                let part_no = $("#part_no").val();
                let group = $("#group").val();
                let unit = $("#unit").val();
                let model = $("#model").val();
                let location = $("#location").val();
                let vat_percent = $("#vat_percent").val();
                let description = $("#pro_desc").val(); 

                var r = confirm("Are You Sure Want To Updated ?");
                if (r == false) {
                    return false;
                }
                if (productid == "") {
                    alert("Product is invalid");
                    return;
                }
                if (productsellingprice < 0) {
                    alert("Negetive Values are not ALLOWED");
                    return;
                }
                if (productsellingprice == "") {
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


                    let data = {
                        id: productid,
                        productname: productname,
                        selling_price: productsellingprice,
                        category_id: category_id,
                        brand_id: brand_id,
                        part_no: part_no,
                        unit: unit,
                        model: model,
                        location: location,
                        vat_percent: vat_percent,
                        group: group,
                        description: description,

                    };

                    $.ajax({
                        data: {data: data},
                        url: updateurl,
                        type: 'POST',
                        beforeSend: function (request) {
                            return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                        },
                        success: function (response) {
                            t.draw();
                            showSnakBar();
                        },
                        error: function (err) {
                            console.log(err);
                            alert("Something Went Wrong, Please check again");
                        }
                    });
                }
            });
        });
    </script>
    @endsection