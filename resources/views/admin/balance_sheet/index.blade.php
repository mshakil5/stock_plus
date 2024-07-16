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
                    <form class="col-md-12" method="POST" action="{{ route('admin.balancesheet') }}">
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
                    <h2>Company Name</h2>
                    @if (isset(Auth::user()->branch))
                        <h3>{{ Auth::user()->branch->name }} Branch</h3>
                    @endif
                    <h4>Balance Sheet</h4>
                </div>

            <div class="table-responsive">
                <table id="cashIncomingTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Particulars</th>
                            <th>Amount</th>
                            <th>Amount</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4">
                            <strong>(1)Fixed Asset</strong>
                            </td>
                        </tr>
                        @foreach($fixedAssets as $fixedAsset)
                            <tr>
                                <td>{{ $fixedAsset->chartOfAccount->account_name }}</td>   
                                <td></td>     
                                <td>{{ number_format($fixedAsset->at_amount, 2) }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            @php
                                $totalFixedAsset = $fixedAssets->sum('at_amount');
                            @endphp
                            <td colspan="2"><strong>Net Fixed Asset</strong></td>
                            <td></td>
                            <td><strong>{{ number_format($totalFixedAsset, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                        </tr>

                        <tr>
                            <td colspan="4">
                            <strong>(2)Current Asset</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Cash in Hand</td>     
                            <td>{{ number_format($currentCashAsset, 2) }}</td>
                            <td></td> 
                            <td></td>
                        </tr>
                        <tr>
                            <td>Cash in Bank</td>     
                            <td>{{ number_format($currentBankAsset, 2) }}</td>
                            <td></td> 
                            <td></td>
                        </tr>
                        <tr>
                            <td>Account Receivable</td>     
                            <td>{{ number_format($accountReceiveable, 2) }}</td>
                            <td></td> 
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            @php
                                $totalCurrentAsset = $currentCashAsset + $currentBankAsset + $accountReceiveable;
                            @endphp
                            <td colspan="3"><strong>Total Current Asset</strong></td>    
                            <td><strong>{{ number_format($totalCurrentAsset, 2) }}</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
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
