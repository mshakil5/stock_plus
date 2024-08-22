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
                    <form class="col-md-12" method="POST" action="{{ route('admin.incomestatement') }}">
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
                        <h3>{{ Auth::user()->branch->name }} Branch</h3>
                    @endif
                    <h4>Income Statement</h4>
                </div>

            <div class="table-responsive">
                <table id="cashIncomingTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Particulars</th>
                            <th>Account Name</th>
                            <th>Amount</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4">
                                <strong>Turn Over Sales</strong>
                            </td>
                        </tr>
                        @foreach($incomes as $income)
                            <tr>
                                <td></td>
                                <td>{{ $income->chartOfAccount->account_name  ?? 'Sales'}}</td>        
                                <td>{{ number_format($income->at_amount, 2) }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            @php
                                $income = $incomes->sum('at_amount');
                            @endphp
                            <td colspan="3"><strong>Total Income</strong></td>
                            <td><strong>{{ number_format($income, 2) }}</strong></td>
                        </tr>
                        <tr>
                            @php
                                $income = $incomes->sum('at_amount');
                            @endphp
                            <td colspan="3"><strong>Gross Profit</strong></td>
                            <td><strong>{{ number_format($income, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <strong>Expenditure</strong>
                            </td>
                        </tr>
                        @foreach($expenses as $expense)
                            <tr>
                                <td></td>
                                <td>{{ $expense->chartOfAccount->account_name ?? 'Purchase' }}</td>        
                                <td>{{ number_format($expense->at_amount, 2) }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            @php
                                $expense = $expenses->sum('at_amount');
                            @endphp
                            <td colspan="3"><strong>Total Expenses</strong></td>
                            <td><strong>{{ number_format($expense, 2) }}</strong></td>
                        </tr>
                        <tr>
                            @php
                                $profitLossBeforeVat = $incomes->sum('amount') - $expenses->sum('amount');
                            @endphp
                            <td colspan="3"><strong>Profit/Lose before vat</strong></td>
                            <td><strong>{{ number_format($profitLossBeforeVat, 2) }}</strong></td>
                        </tr>
                        <tr>
                            @php
                                $taxProvision = $incomes->sum('tax_amount') - $expenses->sum('tax_amount');
                            @endphp
                            <td colspan="3"><strong>Tax Provision</strong></td>
                            <td><strong>{{ number_format($taxProvision, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            @php
                                $netProfit = $profitLossBeforeVat - $taxProvision;
                            @endphp
                            <td colspan="3"><strong>Net Profit</strong></td>
                            <td><strong>{{ number_format($netProfit, 2) }}</strong></td>
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
