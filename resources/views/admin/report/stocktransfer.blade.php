
@extends('admin.layouts.master')
@section('content')

<h2 class="text-blue">
    <i class="fa fa-money text-green" aria-hidden="true"></i> Stock Transfer Report
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
    <form class="form-horizontal" role="form" method="POST" action="{{ route('stockTransferReport.search')}}">
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
                                <th>Date</th>
                                <th>Partno</th>
                                <th>Product Name</th>
                                <th>From Branch</th>
                                <th>To Branch</th>
                                <th style="text-align:center">Quantity</th>
                            @endslot
                            @slot('body')
                                @foreach ($data as $data)
                                @php
                                    $frmbranch = \App\Models\Branch::where('id', $data->from_branch_id)->first()->name;
                                    $tobranch = \App\Models\Branch::where('id', $data->to_branch_id )->first()->name;
                                @endphp
                                    <tr>
                                        <td>{{ $data->created_at }}</td>
                                        <td>{{ $data->product->part_no}}</td>
                                        <td>{{ $data->product->productname}}</td>
                                        <td>{{ $frmbranch }}</td>
                                        <td>{{ $tobranch }}</td>
                                        <td style="text-align:center">{{ $data->stocktransferqty}}</td>
                                    </tr>
                                @endforeach
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
    
   $('#purchaseTBL').on( 'page.dt', function () {
    
   }).dataTable({
        "order": [[ 3, "asc" ]],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print',
                className: 'btn btn-md',
                exportOptions: {
                    columns: [0, 1, 2, 3,4,5,6,7]
                },
                title: 'Purchase Report'
                    
            },
            {
                extend: 'csv',
                className: 'btn btn-md',
                exportOptions: {
                    columns: [0, 1, 2, 3,4,5,6,7]
                },
                title: 'Purchase Report'
            },
            {
                extend: 'pdf',
                className: 'btn btn-md',
                exportOptions: {
                    columns: [0, 1, 2, 3,4,5,6,7]
                },
                title: 'Purchase Report'
            },
        ]
   });
});
</script>
 
@endsection
    