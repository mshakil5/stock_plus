@extends('admin.layouts.master')

@section('content')

<style>
    .company-name-container {
        margin-top: -30px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div id="alert-container"></div>
        @component('components.widget')
            @slot('title')
            @endslot
            @slot('description')
                
            @endslot
            @slot('body')
                <div class="text-center mb-4 company-name-container">
                    <form class="col-md-12" method="POST" action="{{ route('admin.cashbook') }}">
                        @csrf
                        <div class="form-group col-md-5 d-flex align-items-center">
                            <label for="startDate" class="mr-2 mb-0">Start Date</label>
                            <input type="date" id="startDate" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="form-group col-md-5 d-flex align-items-center">
                            <label for="endDate" class="mr-2 mb-0">End Date</label>
                            <input type="date" id="endDate" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" id="searchButton" class="btn btn-primary btn-block">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg> Search
                            </button>
                        </div>
                    </form>
                    @php
                    $company = \App\Models\CompanyDetails::select('company_name')->first();
                    @endphp
                    <h2>{{ $company->company_name }}</h2>
                
                    @if (isset(Auth::user()->branch))
                        <h3>{{ Auth::user()->branch->name}} Branch</h3>
                    @endif

                    <h4>Cashbook</h4>

                </div>

                <div class="table-responsive">
                    <table id="assetTransactionsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Ref</th>                            
                                <th>Debit</th>                            
                                <th>Credit</th>                            
                                <th>Balance</th>                            
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $balance = $totalAmount;
                            @endphp

                            @foreach($cashbooks as $key => $cashbook)
                                <tr>
                                    <td> {{ $key + 1 }} </td>
                                    <td>{{ \Carbon\Carbon::parse($cashbook->date)->format('d-m-Y') }}</td>
                                    <td>
                                        {{ $cashbook->chart_of_account_id ? $cashbook->chartOfAccount->account_name : $cashbook->description }}
                                    </td>
                                    <td>{{ $cashbook->ref }}</td>
                                    @if(in_array($cashbook->transaction_type, ['Current', 'Received', 'Sold', 'Advance']))
                                    <td>{{ $cashbook->at_amount }}</td>
                                    <td></td>
                                    <td>{{ $balance }}</td>
                                    @php
                                        $balance = $balance - $cashbook->at_amount;
                                    @endphp
                                    @elseif(in_array($cashbook->transaction_type, ['Purchase', 'Payment', 'Prepaid']))
                                    <td></td>
                                    <td>{{ $cashbook->at_amount }}</td>
                                    <td>{{ $balance }}</td>
                                    @php
                                        $balance = $balance + $cashbook->at_amount;
                                    @endphp

                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endslot
        @endcomponent
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#assetTransactionsTable').DataTable({
            pageLength: 25,
        });
    });
</script>
@endsection
