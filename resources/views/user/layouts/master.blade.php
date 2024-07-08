<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    
    <title>{{config('app.name')}}</title>

    <link rel="icon" href="{{ asset('user/images/favi.png')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">

    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('user/css/app.css')}}">
    <!-- datatables -->
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet" />
</head>
<style type="text/css">
    .bootstrap-tagsinput .tag {
      margin-right: 2px;
      color: white !important;
      background-color: #5a5e64;
      padding: 0.2rem;
      border-radius: 5px;
    }
  </style>
<!-- oncontextmenu="return false;" -->

<body>

    <!-- main wrapper -->
    <section class="main ">
        <div class="content-container">
            <div class="header shadow-sm px-3">
                <nav class="navbar navbar-expand-lg navbar-light py-0 bg-light w-90 mx-auto">

                    <a class="navbar-brand" href="{{ route('home')}}">
                        <img src="{{ asset('user/images/logo.png')}}" class="img-fluid " width="40px">
                    </a>
                    <a class="navbar-brand" href="{{ route('home')}}">
                        @if (isset(Auth::user()->branch))
                            <p class="nav-link">{{ Auth::user()->branch->name}} Branch</p>
                        @endif
                    </a>
                    <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="navbar-collapse collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav ms-auto navCustom">
                           
                            @if (Auth::user()->type == 1)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('sales')}}">Create Sales</a>
                            </li>
                            @else

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home')}}">Create Sales</a>
                            </li>

                            @endif

                            {{-- @if(Auth::user()->type == '1' && in_array('3', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('3', json_decode(Auth::user()->role->permission))) --}}
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.allinvoices')}}">Sales</a>
                            </li>
                            {{-- @endif --}}

                            @if(Auth::user()->type == '1' && in_array('9', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('9', json_decode(Auth::user()->role->permission)))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.allquotation')}}">Quotation</a>
                            </li>
                            @endif

                            @if(Auth::user()->type == '1' && in_array('11', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('11', json_decode(Auth::user()->role->permission)))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.alldeliverynote')}}">Delivery Notes</a>
                            </li>
                            @endif

                            @if(Auth::user()->type == '1' && in_array('13', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('13', json_decode(Auth::user()->role->permission)))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.allreturninvoices')}}">Sales Return</a>
                            </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.stockrequest')}}">Stock Request</a>
                            </li>


                            @if(Auth::user()->type == '1' && in_array('1', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('1', json_decode(Auth::user()->role->permission)))
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</a>
                            </li>
                            @endif
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                    {{csrf_field()}}
                                </form>
                            </li>


                        </ul>
                    </div>

                </nav>
            </div>


            @yield('content')



        </div>
    </section>

    <!-- Modal -->
    <div class="modal modal-xl fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addProductModalLabel">
                        Add New Product
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gx-2">
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
                        <div class="col-lg-8 ">
                            <div class="box">
                                <div class="row">
                                    <p  class="modal-box-title" >Add New Product</p>
                                    
                                    <form action="{{ route('admin.storeproduct') }}" method="post" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                    <div class="row">
                                            <div class="col-lg-6 ">
                                                <div class="mb-3 row">
                                                    <label for="part_no" class="col-sm-3 col-form-label">Part No</label>
                                                    <div class="col-sm-8">
                                                        <input name="part_no" id="part_no" type="text" class="form-control" maxlength="50px" placeholder="Enter Part no" required="required" value="{{ old('part_no') }}"/>
                                                    </div>
                                                </div>

                                                
                                                <div class="mb-3 row">
                                                    <label for="product" class="col-sm-3 col-form-label">Product Name*</label>
                                                    <div class="col-sm-8">
                                                        <input name="product" id="product" type="text" class="form-control" maxlength="50px" placeholder="Enter Product" required="required" value="{{ old('product') }}"/>
                                                    </div>
                                                </div>

                                                
                                                <div class="mb-3 row">
                                                    <label for="" class="col-sm-3 col-form-label">Code</label>
                                                    <div class="col-sm-8">
                                                        <select name="pcategoryselect" id="pcategoryselect" required="required" class="form-control">
                                                        </select>
                                                    </div>
                                                </div>

                                                
                                                <div class="mb-3 row">
                                                    <label for="" class="col-sm-3 col-form-label">Brand</label>
                                                    <div class="col-sm-8">
                                                        <select name="pbrandselect" id="pbrandselect" class="form-control" required="required">
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="unit" class="col-sm-3 col-form-label">Unit</label>
                                                    <div class="col-sm-8">
                                                        <input name="unit" id="unit" type="text" class="form-control"
                                                        maxlength="50px" placeholder="pcs,ltr,kg etc"  value="{{ old('unit') }}"/>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="sell_price" class="col-sm-3 col-form-label">Sell price</label>
                                                    <div class="col-sm-8">
                                                        <input name="sell_price" id="sell_price" type="number" class="form-control" oninput="this.value=(parseInt(this.value)||0)" maxlength="50px" placeholder="Enter price" value="0" />
                                                    </div>
                                                </div>

                                                {{-- <div class="mb-3 row">
                                                    <label for="" class="col-sm-3 col-form-label">Alternative Product</label>
                                                    <div class="col-sm-8">
                                                        <select name="alternative[]" id="alternative" class="form-control select2" multiple>
                                                            @foreach (\App\Models\Product::all() as $item)
                                                            <option value="{{ $item->id }}">{{ $item->productname }} - {{ $item->part_no }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div> --}}

                                                <div class="mb-3 row">
                                                    <label for="" class="col-sm-3 col-form-label">Alternative Product</label>
                                                    <div class="col-sm-8">
                                                        <select name="alternative[]" id="alternative" class="form-control select2" multiple>
                                                            @foreach (\App\Models\Product::all() as $item)
                                                            <option value="{{ $item->id }}">{{ $item->productname }} - {{ $item->part_no }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                
                                            </div>
                                            
                                            <div class="col-lg-6 ">


                                                <div class="mb-3 row">
                                                    <label for="model" class="col-sm-3 col-form-label">Model</label>
                                                    <div class="col-sm-8">
                                                        <input name="model" id="model" type="text" class="form-control"
                                                        maxlength="50px" placeholder="Enter model" value="{{ old('model') }}"/>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="location" class="col-sm-3 col-form-label">Location</label>
                                                    <div class="col-sm-8">
                                                        <input name="location" id="location" type="text" class="form-control"
                                                        maxlength="50px" placeholder="" value="{{ old('location') }}"/>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="group_id" class="col-sm-3 col-form-label">Group</label>
                                                    <div class="col-sm-8">
                                                        <select name="group_id" id="group_id" class="form-control" required="required">
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="vat_percent" class="col-sm-3 col-form-label">Vat(%)</label>
                                                    <div class="col-sm-8">
                                                        <input name="vat_percent" id="vat_percent" type="number" class="form-control" maxlength="50px" placeholder=""  value="5" />
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="pro_desc" class="col-sm-3 col-form-label">Remarks</label>
                                                    <div class="col-sm-8">
                                                        <textarea name="productdesc" id="pro_desc" class="form-control"
                                                    rows="4"></textarea>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="replacement" class="col-sm-3 col-form-label">Replacement</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="replacement" id="replacement" class="form-control"  rows="3" value="" data-role="tagsinput" />
                                                    </div>
                                                </div>

                                                <button class="btn btn-success btn-md center-block"
                                                    type="submit"><i class="fa fa-plus-circle"></i> Add Product
                                                </button>

                                                
                                            </div>

                                        
                                        
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 ">
                            <div class="box">
                                <div class="row">
                                    <p  class="modal-box-title" >Add New Code</p>
                                    <div class="catermsg"></div>
                                    <div class="row">
                                        <div class="col-lg-12 ">
        
                                            <div class="form-group mx-1 flex-fill">
                                                <input type="text" class="form-control" id="categoryid" placeholder="ID">
                                            </div>
                                            <div class="form-group mx-1 flex-fill">
                                                <input type="text" class="form-control" id="category" placeholder="Type">
                                            </div>
                                            <button onclick="save_category()" type="submit" class="btn btn-primary mb-2" style="margin-top: 5px;">Save
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="row">
                                    <p  class="modal-box-title" >Add New Brand</p>
                                    <div class="brandermsg"></div>
                                    <div class="row">
                                        <div class="col-lg-12 ">
        
                                            <div class="form-group mx-1 flex-fill">
                                                <input type="text" class="form-control" id="brandid" placeholder="ID">
                                            </div>
                                            <div class="form-group mx-1 flex-fill">
                                                <input type="text" class="form-control" id="brand" placeholder="Brandname">
                                            </div>
                                            <button onclick="save_brand()" type="submit" class="btn btn-primary mb-2" style="margin-top: 5px;">Save
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="row">
                                    <p  class="modal-box-title" >Add New Group</p>
                                    <div class="grpermsg"></div>
                                    <div class="row">
                                        <div class="col-lg-12 ">
        
                                            <div class="form-group mx-1 flex-fill">
                                                <input type="text" class="form-control" id="groupid" placeholder="ID">
                                            </div>
                                            <div class="form-group mx-1 flex-fill">
                                                <input type="text" class="form-control" id="group" placeholder="Groupname">
                                            </div>
                                            <button onclick="save_group()" type="submit" class="btn btn-primary mb-2" style="margin-top: 5px;">Save
                                            </button>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>




                    </div>    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
   

    <script src="{{ asset('user/js/jquery-2.2.0.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="{{ asset('user/js/iconify.min.js')}}"></script>
    <script src="{{ asset('user/js/app.js')}}"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    
    

     {{-- tag input  --}}
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
     {{-- tag input  --}}

    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.6.2/jQuery.print.js" integrity="sha512-BaXrDZSVGt+DvByw0xuYdsGJgzhIXNgES0E9B+Pgfe13XlZQvmiCkQ9GXpjVeLWEGLxqHzhPjNSBs4osiuNZyg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        // page schroll top
        function pagetop() {
            window.scrollTo({
                top: 100,
                behavior: 'smooth',
            });
        }
    </script>
<script>
    function showSnakBar(msg = null) {
        var x = document.getElementById("snackbar")
        x.className = "show";
        if (msg) {
            x.innerText = msg
        }
        setTimeout(function () {
            x.className = x.className.replace("show", "");
        }, 3000);
    }
</script>


<script>
    $(document).ready(function () {
        $('.select2').select2();
        // $('.alternativep').select2();
        $(".alternativep").select2({
        maximumSelectionLength: 2
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
                            
                        $(".catermsg").html("<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Saved Successfully!</b></div>");
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
                            
                            $(".brandermsg").html("<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Saved Successfully!</b></div>");
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
                            
                            $(".grpermsg").html("<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Saved Successfully!</b></div>");
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


@yield('script')



  

    
</body>

</html>