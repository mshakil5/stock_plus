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
                            <input type="date" id="startDate" name="start_date" class="form-control" value="{{ request('start_date') }}" required>
                        </div>
                        <div class="form-group col-md-5 d-flex align-items-center">
                            <label for="endDate" class="mr-2 mb-0">End Date</label>
                            <input type="date" id="endDate" name="end_date" class="form-control" value="{{ request('end_date') }}" required>
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
                            <th></th>
                            <th>Particulars</th>
                            <th>Account Name</th>
                            <th>Amount</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>A</strong>
                            </td>
                            <td>
                                <strong>Sales Revenue</strong>
                            </td>
                            <td colspan="3"></td>
                        </tr>
                        
                        <tr>
                            <td colspan="5"></td>
                        </tr>

                        <tr>
                            <td colspan="2"></td>
                            <td>Sales</td>
                            <td>{{ number_format($salesSum, 2) }}</td>
                            <td></td>
                        </tr>  

                        <tr>
                            <td colspan="2"></td>
                            <td>Sales Return</td>
                            <td>{{ number_format($salesReturn, 2) }}</td>
                            <td></td>
                        </tr>  

                        <tr>
                            <td colspan="2"></td>
                            <td>Discount</td>
                            <td>{{ number_format($salesDiscount, 2) }}</td>
                            <td></td>
                        </tr>
                        
                        <tr>
                            <td></td>
                            <td><strong>Net Sales</strong></td>
                            <td colspan="2"></td>
                            <td>
                                @php
                                    $netSales = $salesSum - $salesReturn - $salesDiscount;
                                @endphp
                                {{ number_format($netSales, 2) }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5"></td>
                        </tr>

                        <tr>
                            <td>
                                <strong>B</strong>
                            </td>
                            <td>
                                <strong>Cost of Goods Sold</strong>
                            </td>
                            <td colspan="3"></td>
                        </tr>

                        <tr>
                            <td colspan="2"></td>
                            <td>Opening Stock</td>
                            <td>
                                @if(request('end_date'))
                                    {{ number_format($totalOpeningStock, 2) }}
                                @else
                                    0.00
                                @endif
                            </td>
                            <td></td>
                        </tr>
                        
                        <tr>
                            <td colspan="2"></td>
                            <td>Purchase</td>
                            <td>{{ number_format($purchaseSum, 2) }}</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="2"></td>
                            <td>Closing Stock</td>
                            <td>{{ number_format($totalClosingStock, 2) }}</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>
                                <strong>AB</strong>
                            </td>
                            <td>
                                <strong>Gross Profit</strong>
                            </td>
                            <td>
                                A - B
                            </td>
                            <td></td>
                            <td>
                                @php
                                  $grossProfit =  $netSales - $purchaseSum
                                @endphp
                                {{ number_format($grossProfit, 2) }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5"></td>
                        </tr>

                        <tr>
                            <td>
                            </td>
                            <td>
                                <strong>Operating Income</strong>
                            </td>
                            <td colspan="3"></td>
                        </tr>

                        @foreach ($operatingIncomes as $operatingIncome)
                        <tr>
                            <td colspan="2">
                            
                            </td>
                            <td>
                                {{ $operatingIncome->chartOfAccount->account_name }}
                            </td>
                            <td colspan="2">{{ number_format($operatingIncome->total_amount, 2) }}</td>
                        </tr>
                        @endforeach

                        <tr>
                            <td colspan="5"></td>
                        </tr>

                        <tr>
                            <td>
                                <strong>C</strong>
                            </td>
                            <td>
                                <strong>Operating Expenses</strong>
                            </td>
                            <td colspan="3"></td>
                        </tr>

                        @foreach($operatingExpenses as $operatingExpense)
                        <tr>
                            <td colspan="2"></td>
                            <td>{{ $operatingExpense->chartOfAccount->account_name }}</td>                           
                            <td>{{ number_format($operatingExpense->total_amount, 2) }}</td>
                            <td></td>
                        </tr>
                        @endforeach

                        <tr>
                            <td colspan="5"></td>
                        </tr>

                        <tr>
                            <td>
                                <strong></strong>
                            </td>
                            <td>
                                <strong>OverHead Expenses</strong>
                            </td>
                            <td colspan="3"></td>
                        </tr>

                        @foreach($overHeadExpenses as $overHeadExpense)
                        <tr>
                            <td colspan="2"></td>
                            <td>{{ $overHeadExpense->chartOfAccount->account_name }}</td>                           
                            <td>{{ number_format($overHeadExpense->total_amount, 2) }}</td>
                            <td></td>
                        </tr>
                        @endforeach

                        <tr>
                            <td colspan="5"></td>
                        </tr>

                        <tr>
                            <td>
                                <strong>D</strong>
                            </td>
                            <td>
                                <strong>Administrative Expenses</strong>
                            </td>
                            <td colspan="3"></td>
                        </tr>

                        <tr>
                            <td colspan="2"></td>
                            <td>Depreciation Expense</td>                      
                            <td>{{ number_format($fixedAssetDepriciation, 2) }}</td>
                            <td></td>
                        </tr>

                        @foreach($administrativeExpenses as $administrativeExpense)
                        <tr>
                            <td colspan="2"></td>
                            <td>{{ $administrativeExpense->chartOfAccount->account_name }}</td>                      
                            <td>{{ number_format($administrativeExpense->total_amount, 2) }}</td>
                            <td></td>
                        </tr>
                        @endforeach

                        <tr>
                            <td colspan="5"></td>
                        </tr>

                        <tr>
                            <td>
                                <strong>E</strong>
                            </td>
                            <td>
                                <strong>Profit before tax</strong>
                            </td>
                            <td>AB - C -D</td>
                            <td></td>
                            <td>
                                @php
                                  $totalOperatingExpenses = $operatingExpenses->sum('total_amount');
                                  $totalAdministrativeExpenses = $administrativeExpenses->sum('total_amount');
                                  $totalOverHeadExpenses = $overHeadExpenses->sum('total_amount');
                                  $profitBeforeTax =  $grossProfit +$operatingIncomeSums - $totalOperatingExpenses +$purchaseReturn - $totalAdministrativeExpenses - $operatingIncomeRefundSum - $totalOverHeadExpenses - $fixedAssetDepriciation
                                @endphp
                                {{ number_format($profitBeforeTax, 2) }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5"></td>
                        </tr>

                        <tr>
                            <td>
                                <strong>F</strong>
                            </td>
                            <td>
                                <strong>Tax and VAT</strong>
                            </td>
                            <td colspan="2"></td>
                            <td>
                                {{ number_format($taxAndVat, 2) }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5"></td>
                        </tr>

                        <tr>
                            <td>
                                <strong>G</strong>
                            </td>
                            <td>
                                <strong>Net Profit</strong>
                            </td>
                            <td>E - F</td>
                            <td></td>
                            <td>
                                @php
                                  $netProfit =  $profitBeforeTax - $taxAndVat
                                @endphp
                                {{ number_format($netProfit, 2) }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5"></td>
                        </tr>

                        <tr>
                            <td>
                                <strong>H</strong>
                            </td>
                            <td>
                                <strong>Dvidend</strong>
                            </td>
                            <td colspan="3"></td>
                        </tr>

                        <tr>
                            <td colspan="5"></td>
                        </tr>

                        <tr>
                            <td>
                                <strong>I</strong>
                            </td>
                            <td>
                                <!-- <strong>Net Profit transferred to BS as Retained Earnings</strong> -->
                                <strong>Net Profit</strong>
                            </td>
                            <td>
                                G - H
                            </td>
                            <td colspan="2"></td>
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

@endsection
