
@extends('admin.layouts.master')
@section('content')

<h2 class="text-blue">
    <i class="fa fa-money text-green" aria-hidden="true"></i> Delivery Note Report
    <small class="text-aqua">All Information</small>
</h2>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<hr class="alert-info">
@if (session('message'))
    <div class="alert alert-danger">
        {{ session('message') }}
    </div>
@endif
<?php
echo Session::put('message', '');
?>

<div class="row well">
    <form class="form-horizontal" role="form" method="POST" action="{{ route('deliveryNoteReport.search')}}">
        {{ csrf_field() }}
        <div class="col-md-2">
            <label class="label label-primary">From </label>
            <input type="date" class="form-control" name="fromdate" required>
        </div>
        <div class="col-md-2">
            <label class="label label-primary">To </label>
            <input type="date" class="form-control" name="todate" value="{{date('Y-m-d')}}" required>
        </div>
        <div class="col-md-2">
            <label class="label label-primary">Branch </label>
            <select class="form-control select2" name="branch_id">
                @php
                    $branchNames = \App\Models\Branch::where('status','1')->get();
                @endphp
                <option value="">Select Branch..</option>
                @foreach ($branchNames as $branchName)
                    <option value="{{$branchName->id}}">{{$branchName->name}}</option>
                @endforeach
            </select>
        </div>
        
        {{-- <div class="col-md-1">
            <label class="label label-primary">Sales Type </label>
            <select class="form-control" name="salestype">
                <option value="">All</option>
                <option value="Cash">Cash</option>
                <option value="Credit">Credit</option>
            </select>
        </div> --}}

        <div class="col-md-2">
            <label class="label label-primary">Customer </label>
            <select class="form-control select2" name="customer_id">
                @php
                    $customers = \App\Models\Customer::where('status','1')->get();
                @endphp
                <option value="">Select Customer..</option>
                @foreach ($customers as $customer)
                    <option value="{{$customer->id}}">{{$customer->name}}</option>
                @endforeach
            </select>
        </div>

        {{-- <div class="col-md-2">
            <label class="label label-primary">Product </label>
            <select class="form-control" name="product_id">
                @php
                    $products = \App\Models\Product::all();
                @endphp
                <option value="">Select Product..</option>
                @foreach ($products as $product)
                    <option value="{{$product->id}}">{{$product->productname}}-{{$product->part_no}}</option>
                @endforeach
            </select>
        </div> --}}

        <div class="col-md-1">
            <br>
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
        </div>
    </form>
</div>
<div class="row">

    <div class="col-md-10">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-navicon"></i> Summery</div>
            <p class="label label-info pull-right">
                Month: <span class="label label-warning">
                    @if (isset($month))
                        @php
                            $today = Carbon\Carbon::create()->day(1)->month($month);
                            echo $today->format('F');
                        @endphp
                    @else
                        @php
                            $today = Carbon\Carbon::now();;
                            echo $today->format('F');

                        @endphp
                    @endif
                    </span>
            </p>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        
                        
                        @component('components.table')
                            @slot('tableID')
                                quotationTBL
                            @endslot
                            @slot('head')
                                <th>Part No</th>
                                <th>Description</th>
                                <th>Customer</th>
                                <th>Invoice No</th>
                                <th>Date</th>
                                <th style="text-align:center">Quantity</th>
                                <th>Price</th>
                                <th>Amount</th>
                            @endslot
                            @slot('body')
                                @foreach ($sales as  $sale)
                                    @foreach ($sale->orderdetails as $item)
                                        <tr>
                                            <td>{{ $item->product->part_no}}</td>
                                            <td>{{ $item->product->productname}}</td>
                                            <td>{{ $sale->customer->name}}</td>
                                            <td>{{ $item->invoiceno }}</td>
                                            <td>{{ $sale->orderdate }}</td>
                                            <td style="text-align:center">{{ $item->quantity}}</td>
                                            <td>{{ $item->sellingprice}}</td>
                                            <td style="text-align:right">{{ $item->total_amount}}</td>
                                        </tr>
                                    @endforeach
                                @endforeach

                            @endslot
                            
                            @slot('footer')
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align:right">Total:</th>
                                    <th style="text-align:right"></th>
                                </tr>
                            @endslot

                        @endcomponent


                    </div>
                </div>
            </div>
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
<script src="//cdn.datatables.net/plug-ins/1.13.1/api/sum().js"></script>
<script>
$(document).ready(function () {
    
   $('#quotationTBL').on( 'page.dt', function () {
    
   }).dataTable({
        order:[],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print',
                footer: true,
                className: 'btn btn-md',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                },
                title: 'Delivery Note Report'
                    
            },
            {
                extend: 'csv',
                footer: true,
                className: 'btn btn-md',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                },
                title: 'Delivery Note Report'
            },
            {
                extend: 'pdf',
                footer: true,
                className: 'btn btn-md',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                },
                title: 'Delivery Note Report'
            },
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
 
            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
 
            // Total over all pages
            total = api
                .column(7)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
 
            // Total over this page
            pageTotal = api
                .column(7, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
 
            // Update footer
            $(api.column(7).footer()).html(total);
            // $(api.column(7).footer()).html('$' + pageTotal + ' ( $' + total + ' total)');
        },
   });
});
</script>
@endsection
    