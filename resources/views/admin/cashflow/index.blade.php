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
                    <h2>Company Name</h2>
                    @if (isset(Auth::user()->branch))
                        <h3>{{ Auth::user()->branch->name }} Branch</h3>
                    @endif
                    <h4>Cash Flow</h4>
                </div>

            <div class="table-responsive">
                <table id="cashIncomingTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Particulars</th>
                            <th>Account Name</th>
                            <th>Amounts</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>(1) Opening Balance</strong>
                            </td>
                            <td></td>
                            <td></td>
                            <td><strong>18000.00</strong></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <strong>Cash Incoming</strong>
                            </td>
                        </tr>
                        @foreach($incomes as $income)
                            <tr>
                                <td></td>
                                <td>{{ $income->chartOfAccount->account_name }}</td>        
                                <td>{{ number_format($income->at_amount, 2) }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td>Asset Sold</td>        
                            <td>{{ number_format($assetSold, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Liabilities Received</td>        
                            <td>{{ number_format($liabilityReceived, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Equity Received</td>        
                            <td>{{ number_format($equityReceived, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            @php
                                $totalCashIncoming = $incomes->sum('at_amount') + $assetSold + $liabilityReceived + $equityReceived;
                            @endphp
                            <td colspan="3"><strong>(2) Total Cash Incoming</strong></td>
                            <td><strong>{{ number_format($totalCashIncoming, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <strong>Cash Outgoing</strong>
                            </td>
                        </tr>
                        @foreach($expenses as $expense)
                            <tr>
                                <td></td>
                                <td>{{ $expense->chartOfAccount->account_name }}</td>        
                                <td>{{ number_format($expense->at_amount, 2) }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td>Asset Purchase</td>        
                            <td>{{ number_format($assetPurchase, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Liabilities Payment</td>        
                            <td>{{ number_format($liabilityPayment, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Equity Payment</td>        
                            <td>{{ number_format($equityPayment, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            @php
                                $totalCashOutGoing = $expenses->sum('at_amount') + $assetPurchase + $liabilityPayment + $equityPayment;
                            @endphp
                            <td colspan="3"><strong>(3) Total Cash Outgoing</strong></td>
                            <td><strong>{{ number_format($totalCashOutGoing, 2) }}</strong></td>
                        </tr>

                        <tr>
                            @php
                                $closingBalance = $totalCashIncoming - $totalCashOutGoing;
                            @endphp
                            <td colspan="3"><strong>Closing Balance (3-4)</strong></td>
                            <td><strong>{{ number_format($closingBalance, 2) }}</strong></td>
                        </tr>

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
        $('#cashIncomingTable').DataTable({
            pageLength: 25,
        });
    });
</script>
@endsection
