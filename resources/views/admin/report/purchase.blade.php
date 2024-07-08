
@extends('admin.layouts.master')
@section('content')

<h2 class="text-blue">
    <i class="fa fa-money text-green" aria-hidden="true"></i> Purchase Report
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
    <form class="form-horizontal" role="form" method="POST" action="{{ route('purchaseReport.search')}}">
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
        
        <div class="col-md-2">
            <label class="label label-primary">Purchase Type </label>
            <select class="form-control select2" name="purchase_type">
                <option value="">All</option>
                <option value="Cash">Cash</option>
                <option value="Credit">Credit</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="label label-primary">Supplier </label>
            <select class="form-control select2" name="vendor_id">
                @php
                    $vendors = \App\Models\Vendor::where('status','1')->get();
                @endphp
                <option value="">Select Supplier..</option>
                @foreach ($vendors as $vendor)
                    <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
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
                                    purchaseTBL
                            @endslot
                            @slot('head')
                                <th>Partno</th>
                                <th>Invoice Id</th>
                                <th>Ref</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th style="text-align:center">Quantity</th>
                                <th>Rate</th>
                                <th>Total</th>
                            @endslot
                            @slot('body')
                                @foreach ($purchase as $data)
                                    @foreach ($data->purchasehistory as $item)
                                    <tr>
                                        <td>{{ $item->product->part_no}}</td>
                                        <td>{{ $data->invoiceno}}</td>
                                        <td>{{ $data->ref}}</td>
                                        <td>{{ $data->date}}</td>
                                        <td>{{ $data->vendor->name}}</td>
                                        <td style="text-align:center">{{ $item->quantity}}</td>
                                        <td>{{ $item->purchase_price}}</td>
                                        <td style="text-align:right">{{ $item->total_amount_with_vat}}</td>
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


{{-- 
    <div class="col-md-5">
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
                    <table class="table table-responsive">
                        <tbody>
                            
                            <tr>
                                <td>Total Purchase:</td>
                                <td class="">{{$cashpurchase + $creditpurchase}}</td>

                            </tr>
                            <tr>
                                <td>Total Cash Purchase:</td>
                                <td class="">{{$cashpurchase}}</td>

                            </tr>
                            <tr>
                                <td>Total Credit Purchase:</td>
                                <td class="">{{$creditpurchase}}</td>

                            </tr>
                        </tbody>
                    </table>
            </div>
        </div>

    </div>
 --}}



    
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
    
   $('#purchaseTBL').on( 'page.dt', function () {
    
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
                title: 'Purchase Report'
                    
            },
            {
                extend: 'csv',
                footer: true,
                className: 'btn btn-md',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                },
                title: 'Purchase Report'
            },
            {
                extend: 'pdf',
                footer: true,
                className: 'btn btn-md',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                },
                title: 'Purchase Report'
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
    