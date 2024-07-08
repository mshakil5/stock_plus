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
                        <h3 class="box-title">Add new Products</h3>
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

                        <form action="{{ route('admin.storeproduct') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-hover">
                                        
                                        <tr>
                                            <td><label class="control-label">Part No</label></td>
                                            <td colspan="2"><input name="part_no" id="part_no" type="text" class="form-control"
                                                       maxlength="50px" placeholder="Enter Part no" required="required" value="{{ old('part_no') }}"/>
                                                @if ($errors->has('part_no'))
                                                    <span class="text-danger">{{ $errors->first('part_no') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Product Name*</label></td>
                                            <td colspan="2"><input name="product" id="product" type="text" class="form-control" maxlength="50px" placeholder="Enter Product" required="required" value="{{ old('product') }}"/>
                                                @if ($errors->has('product'))
                                                    <span class="text-danger">{{ $errors->first('product') }}</span>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Code</label></td>
                                            <td colspan="2">
                                                <select name="pcategoryselect" id="pcategoryselect" required="required" class="form-control select2">
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Brand</label></td>
                                            
                                            <td colspan="2">
                                                <select name="pbrandselect" id="pbrandselect" required="required" class="form-control select2">
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Unit</label></td>
                                            <td colspan="2"><input name="unit" id="unit" type="text" class="form-control"
                                                       maxlength="50px" placeholder="pcs,ltr,kg etc"  value="{{ old('unit') }}"/>
                                                @if ($errors->has('unit'))
                                                    <span class="text-danger">{{ $errors->first('unit') }}</span>
                                                @endif
                                            </td>
                                        </tr>

                                        
                                        
                                        <tr>
                                            <td><label class="control-label">Sell price*</label></td>
                                            <td colspan="2"><input name="sell_price" id="sell_price" type="number"
                                                       class="form-control"
                                                       oninput="this.value=(parseInt(this.value)||0)" maxlength="50px"
                                                       placeholder="Enter price" value="0" />
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
                                                    @foreach ($product as $item)
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
                                            <td><input name="model" id="model" type="text" class="form-control"
                                                       maxlength="50px" placeholder="Enter model" value="{{ old('model') }}"/>
                                                @if ($errors->has('model'))
                                                    <span class="text-danger">{{ $errors->first('model') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Location</label></td>
                                            <td><input name="location" id="location" type="text" class="form-control"
                                                       maxlength="50px" placeholder="" value="{{ old('location') }}"/>
                                                @if ($errors->has('location'))
                                                    <span class="text-danger">{{ $errors->first('location') }}</span>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Group</label></td>
                                            <td>
                                                <select name="group_id" id="group_id"  required="required" class="form-control select2">
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Vat(%)</label></td>
                                            <td><input name="vat_percent" id="vat_percent" type="number" class="form-control" maxlength="50px" placeholder=""  value="5" />
                                                @if ($errors->has('vat_percent'))
                                                    <span class="text-danger">{{ $errors->first('vat_percent') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        


                                        <tr>
                                            <td><label class="control-label">Remarks</label></td>
                                            <td>
                                                <textarea name="productdesc" id="pro_desc" class="form-control"
                                                  rows="4"></textarea>
                                            </td>
                                        </tr>

                                        <tr style="display: none">
                                            <td><label class="control-label">Substitute Product</label></td>
                                            <td>
                                                <select name="substitute[]" id="substitute" class="form-control select2" multiple>
                                                    @foreach ($product as $item)
                                                    <option value="{{ $item->id }}">{{ $item->productname }} - {{ $item->part_no }}</option>
                                                    @endforeach
                                                </select>
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
                                    <br>
                                        <button class="btn btn-success btn-md center-block"
                                                type="submit"><i class="fa fa-plus-circle"></i> Add Product
                                        </button>
                                </div>


                                {{-- end  --}}
                            </div>
                        </form>
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

        });
        var getcaturl = "{{URL::to('/admin/category-all')}}";
        var getbrdurl = "{{URL::to('/admin/brand-all')}}";
        var getgroupurl = "{{URL::to('/admin/group-all')}}";
        category_load();
        brand_load();
        group_load();

        
        function category_load() {
            $.ajax({
                url: getcaturl,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    
                    $('#pcategoryselect').append('<option value="">Select</option>');
                    $.each(response, function(){
                        if (this.status == 0) {
                            
                        } else {
                            $('<option/>', {
                                'value': this.id,
                                'text': this.name +' - '+ this.categoryid
                            }).appendTo('#pcategoryselect');
                        }
                    });

                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }

        
        function brand_load() {
            $.ajax({
                url: getbrdurl,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    $('#pbrandselect').append('<option value="">Select</option>');
                    $.each(response, function(){
                        if (this.status == 0) {
                            
                        } else {
                            $('<option/>', {
                                'value': this.id,
                                'text': this.name +' - '+ this.brandid
                            }).appendTo('#pbrandselect');
                        }
                        
                    });
                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }

        function group_load() {
            $.ajax({
                url: getgroupurl,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    $('#group_id').append('<option value="">Select</option>');
                    $.each(response, function(){
                        if (this.status == 0) {
                            
                        } else {
                            $('<option/>', {
                                'value': this.id,
                                'text': this.name +' - '+ this.groupid
                            }).appendTo('#group_id');
                        }
                    });
                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }

        
        

        let helpers =
            {
                buildDropdown: function (result, table, emptyMessage) {
                    // Remove current options
                    table.html('');
                    // Check result isnt empty
                    if (result != '') {
                        // Loop through each of the results and append the option to the table
                        $.each(result, function (k, v) {
                            if (v.status == 1 && emptyMessage == "Select Category")
                                table.append('<option value="' + v.categoryid + '">' + v.categoryname + '</option>');
                            else if (v.status == 1 && emptyMessage == "Select Brand")
                                table.append('<option value="' + v.brandid + '">' + v.brandname + '</option>');
                        });
                    }
                }
            }

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
                        // category_load();
                        // brand_load();
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
                            // category_load();
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
                            // category_load();
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
