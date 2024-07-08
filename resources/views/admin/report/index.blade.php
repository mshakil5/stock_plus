
@extends('admin.layouts.master')
@section('content')
<style>
    .px-2 {
    margin-top: 2px !important;
    margin-bottom: 2px !important;
    }
</style>
<h2 class="text-blue">
    <small class="text-aqua">All Information</small>
</h2>




<div class="row">

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
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            
                            <a href="{{ route('salesReport')}}" class="btn btn-success btn-sm center-block px-2">Sales </a>
                            <a href="{{ route('salesReturnReport')}}" class="btn btn-success btn-sm center-block px-2">Sales Return </a>
                            <a href="{{ route('quotationReport')}}" class="btn btn-success btn-sm center-block px-2">Quotation </a>
                            <a href="{{ route('deliveryNoteReport')}}" class="btn btn-success btn-sm center-block px-2">Delivery Note </a>
                            <a href="{{ route('purchaseReport')}}" class="btn btn-success btn-sm center-block px-2">Purchase </a>
                            <a href="{{ route('purchaseReturnReport')}}" class="btn btn-success btn-sm center-block px-2">Purchase Return </a>
                            <a href="{{ route('stockTransferReport')}}" class="btn btn-success btn-sm center-block px-2">Stock Transfer </a>
                            <a href="{{ route('profitLossReport')}}" class="btn btn-success btn-sm center-block px-2">Profit and loss Statement </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>




    
</div>


@endsection
    
@section('script')


 
@endsection
    