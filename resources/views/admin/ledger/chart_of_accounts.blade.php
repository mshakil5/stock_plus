@extends('admin.layouts.master')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div id="alert-container"></div>
        @component('components.widget')
            @slot('title')
                Chart of Accounts
            @endslot
            @slot('description')

            @endslot
            @slot('body')
                <table id="chartOfAccountsTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Assets</th>
                            <th>Expenses</th>
                            <th>Income</th>
                            <th>Liabilities</th>
                            <th>Equity</th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr>
                                <td>
                                    @foreach($chartOfAccounts as $asset)
                                        @if($asset->account_head == 'Assets')   
                                            <a href="{{ url('/admin/ledger/asset-details/' . $asset->id) }}">{{ $asset->account_name }}</a>
                                            <hr>
                                        @endif  
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($chartOfAccounts as $expense)
                                        @if($expense->account_head == 'Expenses')   
                                            <a href="{{ url('/admin/ledger/expense-details/' . $expense->id) }}">{{ $expense->account_name }}</a>
                                            <hr>
                                        @endif  
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($chartOfAccounts as $income)
                                        @if($income->account_head == 'Income')   
                                            <a href="{{ url('/admin/ledger/income-details/' . $income->id) }}">{{ $income->account_name }}</a>
                                            <hr>
                                        @endif  
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($chartOfAccounts as $liability)
                                        @if($liability->account_head == 'Liabilities')   
                                            <a href="{{ url('/admin/ledger/liability-details/' . $liability->id) }}">{{ $liability->account_name }}</a>
                                            <hr>
                                        @endif  
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($chartOfAccounts as $equity)
                                        @if($equity->account_head == 'Equity')   
                                            <a href="{{ url('/admin/ledger/equity-details/' . $equity->id) }}">{{ $equity->account_name }}</a>
                                            <hr>
                                        @endif  
                                    @endforeach
                                </td>
                            </tr>
                    </tbody>
                </table>
            @endslot
        @endcomponent
    </div>
</div>

<style>
    table {
        width: 100%;
        border-collapse: collapse;
        overflow-x: auto;
    }

    tbody tr {
        background-color: #f9f9f9;
        border-bottom: 1px solid #eee;
    }

    td {
        padding: 5px;
        border-radius: 5px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
        transition: background-color 0.3s ease;
    }

    td a {
        color: #333;
        text-decoration: none;
        display: block;
        padding: 8px;
        border-radius: 3px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    td a:hover {
        background-color: #f0f0f0;
    }

    hr {
        border-top: 1px solid transparent;
        height: 1px;
        background-color: transparent;
        margin: 5px 0;
    }

</style>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#chartOfAccountsTable').DataTable();
    });
</script>
@endsection