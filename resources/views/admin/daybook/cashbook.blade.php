@extends('admin.layouts.master')

@section('content')

<style>
    .company-name-container {
        margin-top: -30px;
    }
</style>

<div class="page-header"><a href="{{ url()->previous() }}" class="btn btn-primary">Back</a></div>
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
                    <h2>Company Name</h2>
                
                    @if (isset(Auth::user()->branch))
                        <h3>{{ Auth::user()->branch->name}} Branch</h3>
                    @endif

                    <h4>Cashbook</h4>

                </div>

                <div class="table-responsive">
                    <table id="assetTransactionsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Account</th>
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

                            @foreach($cashbooks as $cashbook)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($cashbook->date)->format('d-m-Y') }}</td>
                                    <td>{{ $cashbook->chartOfAccount->account_name }}</td>
                                    <td>{{ $cashbook->ref }}</td>
                                    @if(in_array($cashbook->transaction_type, ['Current', 'Received', 'Sold', 'Advance']))
                                    <td>{{ $cashbook->at_amount }}</td>
                                    <td></td>
                                    @elseif(in_array($cashbook->transaction_type, ['Purchase', 'Payment', 'Prepaid']))
                                    <td></td>
                                    <td>{{ $cashbook->at_amount }}</td>
                                    @else   
                                    <td></td>
                                    <td></td> 
                                    @endif
                                    <td>{{ $balance }}</td>
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
            "columnDefs": [
                { "orderable": false, "targets": "_all" }
            ]
        });
    });
</script>
@endsection
