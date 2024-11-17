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
                    @php
                    $company = \App\Models\CompanyDetails::select('company_name')->first();
                    @endphp
                    <h2>{{ $company->company_name }}</h2>
                
                    @if (isset(Auth::user()->branch))
                        <h3>{{ Auth::user()->branch->name}} Branch</h3>
                    @endif

                    @if (!empty($assets->first()->chartOfAccount))
                        <h4>{{ $assets->first()->chartOfAccount->account_name }} Ledger</h4>
                    @else
                        <h4>Account Name Not Found</h4>
                    @endif
                </div>

                <div class="table-responsive">
                    <table id="assetTransactionsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Transaction Inputter</th>
                                <th>Description</th>
                                <th>Payment Type</th>
                                <th>Ref</th>
                                <th>Transaction Type</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $balance = $totalAsset;
                            @endphp

                            @foreach($assets as $index => $asset)
                                <tr>
                                    <td>{{ $asset->tran_id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($asset->date)->format('d-m-Y') }}</td>
                                    <td>{{ $asset->creator->name ?? 'Unknown' }}</td>
                                    <td>{{ $asset->description }}</td>
                                    <td>{{ $asset->payment_type }}</td>
                                    <td>{{ $asset->ref }}</td>
                                    <td>{{ $asset->transaction_type }}</td>
                                    @if(in_array($asset->transaction_type,  ['Refund']))
                                    <td>{{ $asset->at_amount }}</td>
                                    <td></td>
                                    <td>{{ $balance }}</td>
                                    @php
                                        $balance = $balance - $asset->at_amount;
                                    @endphp
                                    @elseif(in_array($asset->transaction_type,  ['Current', 'Advance Adjust']))
                                    <td></td>
                                    <td>{{ $asset->at_amount }}</td>
                                    <td>{{ $balance }}</td>
                                    @php
                                        $balance = $balance + $asset->at_amount;
                                    @endphp
                                    @else
                                    <td></td>
                                    <td></td>
                                    <td></td>
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
            "columnDefs": [
                { "orderable": false, "targets": "_all" }
            ]
        });
    });
</script>
@endsection
