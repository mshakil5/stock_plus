@extends('admin.layouts.master')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-navicon"></i> Todays Sales Amount</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            
                            <h5 class="btn btn-success btn-sm center-block px-2">Sales : {{ \App\Models\Order::where('sales_status','=','1')->where('orderdate',date('Y-m-d'))->sum('net_total')}} </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-navicon"></i> Today Cash Sales Amount</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            
                            <h5 class="btn btn-success btn-sm center-block px-2"> {{ \App\Models\Order::where('sales_status','=','1')->where('orderdate',date('Y-m-d'))->sum('net_total') - \App\Models\Order::where('sales_status','=','1')->where('orderdate',date('Y-m-d'))->sum('due')}}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-navicon"></i> Today Credit Sales Amount</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            
                            <h5 class="btn btn-success btn-sm center-block px-2"> {{ \App\Models\Order::where('sales_status','=','1')->where('orderdate',date('Y-m-d'))->sum('due')}}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-navicon"></i> Total Product</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            
                            <h5 class="btn btn-success btn-sm center-block px-2"> {{ \App\Models\Product::count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-navicon"></i> Todays Quotation Create</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            
                            <h5 class="btn btn-success btn-sm center-block px-2"> {{ \App\Models\Order::where('quotation','=','1')->where('orderdate',date('Y-m-d'))->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-navicon"></i> Todays Delivery Note Create</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            
                            <h5 class="btn btn-success btn-sm center-block px-2"> {{ \App\Models\Order::where('delivery_note','=','1')->where('orderdate',date('Y-m-d'))->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-navicon"></i> Today Purchase Amount</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            
                            <h5 class="btn btn-success btn-sm center-block px-2"> {{ \App\Models\Purchase::where('date',date('Y-m-d'))->sum('net_amount') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-navicon"></i> Total Supplier</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            <h5 class="btn btn-success btn-sm center-block px-2">
                                {{ \App\Models\Vendor::count() }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-money"></i> Today's Expense</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            <h5 class="btn btn-success btn-sm center-block px-2">
                                {{ \App\Models\Transaction::where('table_type', 'Expenses')
                                    ->whereDate('created_at', \Carbon\Carbon::today())
                                    ->sum('at_amount') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-money"></i> Today's Income</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            <h5 class="btn btn-success btn-sm center-block px-2">
                                {{ \App\Models\Transaction::where('table_type', 'Income')
                                    ->whereDate('created_at', \Carbon\Carbon::today())
                                    ->sum('at_amount') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-balance-scale"></i> Today's Liability</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            <h5 class="btn btn-success btn-sm center-block px-2">
                                {{ \App\Models\Transaction::where('table_type', 'Liability')
                                    ->whereDate('created_at', \Carbon\Carbon::today())
                                    ->sum('at_amount') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-users"></i> Total Customers</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            <h5 class="btn btn-success btn-sm center-block px-2">
                                {{ \App\Models\Customer::count() }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
