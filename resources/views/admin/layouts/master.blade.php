<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{config('app.name')}}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('admin/js/everythingJS.js')}}"></script>
    <!-- Font Awesome -->
    <link rel="shortcut icon" href="#">
    <link rel="stylesheet" href="{{ asset('admin/css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('admin/css/ionicons.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin/css/AdminLTE.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin/css/all-skins.css')}}">
    <link rel="stylesheet" href="{{ asset('admin/css/select2.css')}}">
    <link rel="stylesheet" href="{{ asset('admin/css/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" href="{{ asset('admin/css/bootstrap-datepicker.css')}}">
    <link rel="stylesheet" href="{{ asset('admin/css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('admin/css/responsive.css')}}">
    <link rel="stylesheet" href="{{ asset('admin/css/daterangepicker.css')}}">
    <!-- DataTables -->
    <style src="{{asset('admin/css')}}/buttons.dataTables.min.css"></style>
    
    
    <script src="{{ asset('admin/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('admin/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{ asset('admin/js/gijgo.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('admin/css/gijgo.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Google Font -->

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <a href="#" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->

            <span class="logo-lg">
                        <img src="{{ asset('admin/demo.png')}}" width="32px" height="32px">{{config('app.name')}}
                    </span>
                    
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-fixed-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    {{-- @php
                        $branchid = Session::get('branch_id');
                        $newArrivalCount = App\StockTransfer::whereHas('TransferStatuses', function($query){
                        $query->where('status', '=', 0);
                        })->where('branchid', $branchid)
                        ->get();
                    @endphp
                    <li class="nav-item">
                        <a href="#" title="" class="" data-original-title="POS">
                            <strong><i class="fa fa-th-large"></i> &nbsp;POS</strong>
                        </a>
                    </li>
                    <li class="dropdown user user-menu notific">
                        <a href="#" title="stock notification" class="dropdown-toggle" data-toggle="dropdown"> <i
                                    class="ion ion-android-notifications"></i>@if(count($newArrivalCount) > 0) <span
                                    class="noti-count"><span
                                        class="num">{{ $newArrivalCount->count() }}</span></span>@endif</a>
                        <ul class="dropdown-menu">
                            @if(count($newArrivalCount) > 0)
                                <input type="hidden" id="hasValue" value="1">
                                <li class="notified-items"><a href="#">You
                                        have {{ $newArrivalCount->count() }} new arrivals.</a></li>
                                <audio id="notification" src="{{ asset('notification.mp3') }}" preload="auto"></audio>
                            @else
                                <li class="notified-items">You have no new notification!</li>
                            @endif
                        </ul>
                    </li> --}}
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i
                                    class="fa fa-user-circle-o"></i>
                                    {{Auth::user()->branch->name}}
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header" style="height: auto">
                                <img src="{{ asset('admin/demo.png')}}" class="img-circle"
                                     alt="User Image">
                            </li>
                            <li>
                                <p style="padding-left:15px;">
                                    Hello Name. Hope You Have Saved Your Work before Signing Out.
                                </p>
                            </li>
                            <!-- Menu Body -->
                        {{--<li class="user-body">--}}
                        {{--<!-- /.row -->--}}
                        {{--</li>--}}
                        <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{{ route('super_admin')}}" class="btn btn-success btn-flat dropdown-item"><i
                                                class="fa fa-user-circle-o"></i> Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a class="btn btn-danger btn-flat dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                        <i class="fa fa-sign-out"></i> {{ __('Sign out') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        {{csrf_field()}}
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class=" main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <h4 style="color:white;font-size:11px" class="text-center">{{Auth::user()->branch->name}}</h4>
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{ asset('admin/demo.png')}}" class="img-circle"
                         alt="User Image">
                </div>
                <div class="pull-left info">
                    <p>Name :{{Auth::user()->name}} </p>
                    <small>{{Auth::user()->role->name}}</small>
                </div>
            </div>
            <ul class="sidebar-menu" data-widget="tree">
                <li class="{{ (request()->is('admin/home')) ? 'active' : '' }}">
                    <a href="{{URL::to('/home')}}">
                        <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                    </a>
                </li>
                
                
                
                
                
                    <li class="treeview {{ (request()->is('admin/add-product')) ? 'active' : '' }}{{ (request()->is('admin/manage-product')) ? 'active' : '' }}{{ (request()->is('admin/product-category')) ? 'active' : '' }}{{ (request()->is('admin/product-brand')) ? 'active' : '' }}{{ (request()->is('admin/product-group')) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-files-o"></i>
                            <span> Products</span>
                            <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Auth::user()->type == '1' && in_array('1', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('1', json_decode(Auth::user()->role->permission)))
                                <li class="{{ (request()->is('admin/add-product')) ? 'active' : '' }}"><a href="{{ route('admin.addproduct')}}"><i class="fa fa-clone"></i> Add New Product</a> </li>
                            @endif

                            @if(Auth::user()->type == '1' && in_array('20', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('20', json_decode(Auth::user()->role->permission)))
                                <li class="{{ (request()->is('admin/manage-product')) ? 'active' : '' }}"><a href="{{ route('admin.manage_product')}}"><i class="fa fa-leaf"></i> Manage product</a> </li>
                            @endif

                                <li class="{{ (request()->is('admin/product-category')) ? 'active' : '' }}"><a href="{{ route('view_product_category')}}"><i class="fa fa-credit-card"></i>Code</a></li>
                                <li class="{{ (request()->is('admin/product-brand')) ? 'active' : '' }}"><a href="{{ route('view_product_brand')}}"><i class="fa fa-credit-card"></i>Brand</a></li>
                                <li class="{{ (request()->is('admin/product-group')) ? 'active' : '' }}"><a href="{{ route('view_product_group')}}"><i class="fa fa-credit-card"></i>Group</a> </li>
                            
                                {{-- <li><a href="{{ route('view_product_size')}}"><i class="fa fa-credit-card"></i>Sizes</a>
                                </li> --}}
                        </ul>
                    </li>


                    <li class="treeview {{ (request()->is('admin/add-stock')) ? 'active' : '' }} {{ (request()->is('admin/manage-stock')) ? 'active' : '' }} {{ (request()->is('admin/product-purchase-history')) ? 'active' : '' }} {{ (request()->is('admin/stock-transfer-request')) ? 'active' : '' }} {{ (request()->is('admin/stock-transfer-history')) ? 'active' : '' }} {{ (request()->is('admin/stock-return-history')) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-clipboard"></i>
                            <span>Stocks</span>
                            <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Auth::user()->type == '1' && in_array('5', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('5', json_decode(Auth::user()->role->permission)))
                            <li class="{{ (request()->is('admin/add-stock')) ? 'active' : '' }}"><a href="{{ route('admin.addstock')}}"><i class="fa fa-plus"></i>Purchase </a></li>
                            @endif
                            @if(Auth::user()->type == '1' && in_array('21', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('21', json_decode(Auth::user()->role->permission)))
                            <li class="{{ (request()->is('admin/manage-stock')) ? 'active' : '' }}"><a href="{{ route('admin.managestock')}}"><i class="fa fa-truck"></i> Stock List</a></li>
                            @endif

                            @if(Auth::user()->type == '1' && in_array('5', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('5', json_decode(Auth::user()->role->permission)))
                            <li class="{{ (request()->is('admin/product-purchase-history')) ? 'active' : '' }}"><a href="{{ route('admin.product.purchasehistory')}}"><i class="fa fa-history"></i>Purchase History</a></li>
                            @endif

                            @if(Auth::user()->type == '1' && in_array('7', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('7', json_decode(Auth::user()->role->permission)))
                            <li class="{{ (request()->is('admin/stock-transfer-request')) ? 'active' : '' }}"><a href="{{ route('admin.stock.transferrequest')}}"><i class="fa fa-history"></i>Stock Transfer Request</a></li>
                            @endif

                            @if(Auth::user()->type == '1' && in_array('18', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('18', json_decode(Auth::user()->role->permission)))
                            <li class="{{ (request()->is('admin/stock-transfer-history')) ? 'active' : '' }}"><a href="{{ route('admin.stock.transferhistory')}}"><i class="fa fa-history"></i>Transferred History</a></li>
                            @endif

                            @if(Auth::user()->type == '1' && in_array('19', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('19', json_decode(Auth::user()->role->permission)))
                            <li class="{{ (request()->is('admin/stock-return-history')) ? 'active' : '' }}"><a href="{{ route('admin.stockReturnHistory')}}"><i class="fa fa-undo"></i>Returned History</a></li>
                            @endif
                        </ul>
                    </li>

                    @if(Auth::user()->type == '1' && in_array('14', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('14', json_decode(Auth::user()->role->permission)))
                    <li class="{{ (request()->is('admin/vendor/add')) ? 'active' : '' }}">
                        <a href="{{ route('admin.addvendor')}}">
                            <i class="fa fa-users"></i>
                            <span>Supplier</span>
                            <span class="pull-right-container"> </span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->type == '1' && in_array('15', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('15', json_decode(Auth::user()->role->permission)))
                    <li class="{{ (request()->is('admin/customers')) ? 'active' : '' }}">
                        <a href="{{ route('admin.addcustomer')}}">
                            <i class="fa fa-users"></i>
                            <span>Customer</span>
                            <span class="pull-right-container"> </span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->type == '1' && in_array('16', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('16', json_decode(Auth::user()->role->permission)))
                    <li class="{{ (request()->is('admin/branch')) ? 'active' : '' }}">
                        <a href="{{ route('view_branch')}}">
                            <i class="fa fa-users"></i>
                            <span>Branch</span>
                            <span class="pull-right-container"> </span>
                        </a>
                    </li>
                    @endif


                
                
                    @if(Auth::user()->type == '1' && in_array('17', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('17', json_decode(Auth::user()->role->permission)))
                    <li class="treeview {{ (request()->is('admin/all-sellsinvoice')) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-user"></i> <span>Sales</span><span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                                <li><a href="{{ route('sales')}}"><i class="fa fa-adjust"></i> Sales</a></li>
                                <li class="{{ (request()->is('admin/all-sellsinvoice')) ? 'active' : '' }}"><a href="{{ route('admin.allsellinvoice')}}"><i class="fa fa-adjust"></i> Manage Sales</a></li>
                        </ul>
                    </li>
                    @endif

                    @if(Auth::user()->type == '1' && in_array('22', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('22', json_decode(Auth::user()->role->permission)))
                    <li class="{{ (request()->is('admin/payment-method')) ? 'active' : '' }}">
                        <a href="{{ route('view_payment_method')}}">
                            <i class="fa fa-users"></i>
                            <span>Payment Method</span>
                            <span class="pull-right-container"> </span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->type == '1' && in_array('23', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('23', json_decode(Auth::user()->role->permission)))
                    <li class="{{ (request()->is('admin/getreport-title')) ? 'active' : '' }}{{ (request()->is('admin/sales-report')) ? 'active' : '' }}{{ (request()->is('admin/sales-return-report')) ? 'active' : '' }}{{ (request()->is('admin/quotation-report')) ? 'active' : '' }}{{ (request()->is('admin/delivery-note-report')) ? 'active' : '' }}{{ (request()->is('admin/purchase-report')) ? 'active' : '' }}{{ (request()->is('admin/purchase-return-report')) ? 'active' : '' }}{{ (request()->is('admin/stock-transfer-report')) ? 'active' : '' }}">
                        <a href="{{ route('report')}}">
                            <i class="fa fa-users"></i>
                            <span>Report</span>
                            <span class="pull-right-container"> </span>
                        </a>
                    </li>
                    @endif


                    @if(Auth::user()->type == '1' && in_array('8', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('8', json_decode(Auth::user()->role->permission)))
                    <li class="treeview {{ (request()->is('admin/role*')) ? 'active' : '' }}{{ (request()->is('admin/manage-user')) ? 'active' : '' }}{{ (request()->is('admin/create-user')) ? 'active' : '' }}{{ (request()->is('admin/manage-admin')) ? 'active' : '' }}{{ (request()->is('admin/create-admin')) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>System Users</span>
                            <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ (request()->is('admin/create-user')) ? 'active' : '' }}"><a href="{{ route('create_user')}}"><i class="fa fa-plus"></i> Add New User</a>
                            </li>
                            <li class="{{ (request()->is('admin/manage-user')) ? 'active' : '' }}"><a href="{{ route('manage_user')}}"><i class="fa fa-adjust"></i>Manage User</a>
                            </li>
                            <li class="{{ (request()->is('admin/create-admin')) ? 'active' : '' }}"><a href="{{ route('create_admin')}}"><i class="fa fa-plus"></i> Add New Admin</a>
                            
                            <li class="{{ (request()->is('admin/manage-admin')) ? 'active' : '' }}"><a href="{{ route('manage_admin')}}"><i class="fa fa-adjust"></i>Manage Admin</a>
                            </li>
                            <li class="{{ (request()->is('admin/role*')) ? 'active' : '' }}"><a href="{{ route('admin.role')}}"><i class="fa fa-adjust"></i>Manage Role</a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    <li class="{{ (request()->is('admin/switch_branch')) ? 'active' : '' }}">
                        <a href="{{ route('switch_branch')}}">
                            <i class="fa fa-users"></i>
                            <span>Swich Branch</span>
                            <span class="pull-right-container"> </span>
                        </a>
                    </li>


                    {{-- <li>
                        <a href="">
                            <i class="fa fa-cogs"></i>
                            <span>Settings</span>
                        </a>
                    </li> --}}
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header pageheader">
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active"></li>
            </ol>
        </section>
        <!-- Main content -->
        <div id="snackbar">Data updated successfully.</div>
        <section class="content">
            {{-- <div class="spinner">
                 
                <img class="mx-auto d-block" src="{{asset('loader.gif')}}">
            </div> --}}
            
            
                
            <br>
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer fixed-bottom">
        <div class="container-fluid">
            <div class="offset-md-3 col-md-9">
                <div class="pull-right hidden-xs">
                    <b>Version:</b> 1.1.0
                </div>
                <strong>Copyright &copy; </strong>Next Link Ltd All rights reserved.
            </div>
        </div>
    </footer>
    <!-- Add the sidebar's background. This must be placed
    immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->


<script src="{{asset('admin/js')}}/dataTables.buttons.min.js"></script>
<script src="{{asset('admin/js')}}/buttons.print.min.js"></script>
<script src="{{asset('admin/js')}}/pdfmake.min.js"></script>
<script src="{{asset('admin/js')}}/vfs_fonts.js"></script>
<script src="{{asset('admin/js')}}/buttons.html5.min.js"></script>

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
    // page schroll top
    function pagetop() {
        window.scrollTo({
            top: 100,
            behavior: 'smooth',
        });
    }
</script>
@yield('script')
</body>
</html>