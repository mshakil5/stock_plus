

@extends('admin.layouts.master')
@section('content')
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
        <div class="col-md-8">

            <div class="box box-widget widget-user-2">
                <div class="widget-user-header">
                    <h3 class="widget-user-username">Product details</h3>
                    <h5 class="widget-user-desc table-responsive">Particular products information - (IBC stands for International Barcode)</h5>
                </div>
                <div class="box-body table-responsive">
                    
                </div>
            </div>

            <table  id="productsTBL" class="table table-hover table-responsive " width="100%">
                <thead>
                    <tr>
                        <th>Product(IBC.)</th>
                        <th>Brand</th>
                        <th>Sell price</th>
                        <th>Size</th>
                        <th>Category</th>
                        <th><i class=""></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($product as $item)
                    <tr>
                        <td>{{ $item->productname}}</td>
                        <td>{{ $item->brand->name}}</td>
                        <td>{{ $item->selling_price}}</td>
                        <td>{{ $item->size->name}}</td>
                        <td>{{ $item->category->name}}</td>
                        <td><button data-toggle='modal' data-target='#editproductModal' class='btn btn-sm btn-primary edit-btn' value='{{ $item->id}}'><i class='fa fa-pencil'></i> Edit</button></td>
                    </tr>
                    @endforeach
                    
            
                </tbody>
            
            </table>
 
        </div>
        <div class="col-md-4">
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
        </div>
    </div>

    <div class="modal fade" id="editproductModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Update product details </h4>
                </div>
                <div class="modal-body">
                    <table class="table">

                        <tbody>
                        <input type="hidden" id="productid" value="">
                        <tr>
                            <td></td>
                            <td>Product Name</td>
                            <td><input type="Text" id="productname"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><label class="control-label">Brand:</label></td>
                            <td><select class="form-control select2" id="branddropdown" style="width: 100%;" ;>
                                    <option>Select...</option>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><label class="control-label">Category</label></td>
                            <td>
                                <select class="form-control select2" id="categorydropdown" style="width: 100%;" ;>
                                    <option>Select...</option>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><label class="control-label">Size:</label></td>
                            <td>
                                <select class="form-control select2" id="sizedropdown" style="width: 100%;" ;>
                                    <option>Select...</option>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="hidden" id="totalqty" disabled></td>
                        </tr>

                        <tr>
                            <td></td>
                            <td><label class="control-label">Sell Price:</label></td>
                            <td><input type="number" id="sellprice"></td>
                        </tr>

                        </tbody>

                    </table>
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

    <script>
        $(document).ready(function () {
            $('.select2').select2();
            var t = $('#productsTBL').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url("/filter-all") }}',
                    data: function (d) {
                        d.category = $("#categorydropdown2").val();
                        d.brand = $("#branddropdown2").val();
                    }
                },
                deferRender: true,
                columns: [
                    // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {
                        data: 'productname', name: 'productname', "render": function (data, type, row, meta) {
                            let product = row.international_barcode == null ? data + `<a href='/add-product-international-barcode/` + row.productid + `'>(Add IBC)</a>` : `<a href='/get-product-by-ibm/` + row.international_barcode + `' target='_blank'>` + row.productname + "(" + row.international_barcode + ")" + `</a>`;
                            return product;
                        }
                    },
                    {data: 'brands.brandname', name: 'brandid'},
                    {data: 'productsellingprice', name: 'productsellingprice'},
                    {data: 'sizes.sizename', name: 'sizeid'},
                    {data: 'categories.categoryname', name: 'categoryid'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            category_load();

            function category_load() {
                $.ajax({
                    url: '/category-all',
                    type: 'GET',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        $('#categorydropdown,#categorydropdown2').empty();
                        $('#categorydropdown').append('<option selected disabled>Select a category</option>');
                        $('#categorydropdown2').append('<option selected value="">All category</option>');
                        $.each(response, function (i, category) {
                            $('#categorydropdown,#categorydropdown2').append($('<option>', {
                                value: category.categoryid,
                                text: category.categoryname
                            }));
                        });
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }

            brand_load();

            function brand_load() {
                $.ajax({
                    url: '/brand-all',
                    type: 'GET',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        $('#branddropdown,#branddropdown2').empty();
                        $('#branddropdown').append('<option selected disabled>Select a brand</option>');
                        $('#branddropdown2').append('<option selected value="">All brand</option>');
                        $.each(response, function (i, brand) {
                            $('#branddropdown,#branddropdown2').append($('<option>', {
                                value: brand.brandid,
                                text: brand.brandname
                            }));
                        });
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }

            size_load();

            function size_load() {
                $.ajax({
                    url: '/size-all',
                    type: 'GET',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        $('#sizedropdown').empty();
                        $('#sizedropdown').append('<option selected disabled>Select a size</option>');
                        $.each(response, function (i, size) {
                            $('#sizedropdown').append($('<option>', {
                                value: size.sizeid,
                                text: size.sizename
                            }));
                        });
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }

            $(document).on('keyup change', '#categorydropdown2,#branddropdown2', function () {
                t.draw();
            });
            $(document).on('click', '.edit-btn', function () {
                let id = $(this).val();
                $.ajax({
                    url: '/product-info/' + id,
                    type: 'GET',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        $("#productid").val(response.productid);
                        $("#productname").val(response.productname);
                        $("#branddropdown").val(response.brandid).trigger('change');
                        $("#categorydropdown").val(response.categoryid).trigger('change');
                        $("#sizedropdown").val(response.sizeid).trigger('change');
                        $("#totalqty").val(response.producttotalqty);
                        $("#sellprice").val(response.productsellingprice);
                    },
                    error: function (err) {
                        console.log(err);
                        alert("Something Went Wrong, Please check again");
                    }
                });
            });
            $(document).on('click', '.update-btn', function () {
                let productid = $("#productid").val();
                let productname = $("#productname").val();
                let producttotalqty = $("#totalqty").val();
                let productsellingprice = $("#sellprice").val();
                let brandid = $("#branddropdown").val();
                let categoryid = $("#categorydropdown").val();
                let sizeid = $("#sizedropdown").val();
                 var r = confirm("Are You Sure Want To Updated Head Office");
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
                if (!isInt(productsellingprice)) {
                    alert("Fractional Value is not possible!");
                    return;
                }
                if (productname == "") {
                    alert("Please Provide Product Name");
                    return;
                }
                if (brandid == "") {
                    alert("Please Provide Brand Name");
                    return;
                }
                if (sizeid == "") {
                    alert("Please Provide Size Name");
                    return;
                }
                if (categoryid == "") {
                    alert("Please Provide Category Name");
                    return;
                } else {


                    let data = {
                        productid: productid,
                        productname: productname,
                        sizeid: sizeid,
                        categoryid: categoryid,
                        brandid: brandid,
                        productsellingprice: productsellingprice,
                        producttotalqty: producttotalqty,
                        productname: productname,

                    };

                    $.ajax({
                        data: {data: data},
                        url: '/update-product-details',
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
    
    @section('script')

    
    @endsection