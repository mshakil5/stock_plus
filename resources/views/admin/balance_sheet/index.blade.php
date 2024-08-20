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
                @php
                $company = \App\Models\CompanyDetails::select('company_name')->first();
                @endphp
                <h2>{{ $company->company_name }}</h2>
                @if (isset(Auth::user()->branch))
                    <h3>{{ Auth::user()->branch->name }} Branch</h3>
                @endif
                <h4>Balance Sheet</h4>
            </div>

            <div class="table-responsive">
                <table id="cashIncomingTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <!-- <th>Particulars</th>
                            <th>Amount</th>
                            <th>Amount</th>
                            <th>Amount</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Asset Start -->
                        <tr>
                            <td><strong>Asset</strong></td>
                            <td><strong>Main Asset Type</strong></td>
                            <td><strong>Sub Asset Type</strong></td>
                            <td><strong>Opening Balance</strong></td>
                            <td><strong>Debit Movement</strong></td>
                            <td><strong>Credit Movement</strong></td>
                            <td><strong>Closing Balance</strong></td>
                        </tr>
                        <tr>
                            <td colspan="8"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><strong>Current Asset</strong></td>
                            <td></td>
                            <td><strong>Total Till Yesterday</strong></td>
                            <td><strong>Total Of All Debit Txn Today</strong></td>
                            <td><strong>Total Of All Credit Txn Today</strong></td>
                            <td><strong>CB= OB+DMV+CMV </strong></td>
                        </tr>

                        <!-- current assets  -->
                        @foreach($currentAssets as $currentAsset)
                        <tr>
                            <td colspan="2"></td>
                            <td>{{ $currentAsset->account_name }}</td>
                            <td colspan="4"></td>
                        </tr>
                        @endforeach

                        <tr>
                            <td></td>
                            <td><strong>Fixed Asset</strong></td>
                            <td colspan="5"></td>
                        </tr>

                        <!-- fixed assets  -->
                        @foreach($fixedAssets as $fixedAsset)
                        <tr>
                            <td colspan="2"></td>
                            <td>{{ $fixedAsset->account_name }}</td>
                            <td colspan="4"></td>
                        </tr>
                        @endforeach
    
                        <tr>
                            <td>
                                <strong>Total Asset</strong>
                            </td>
                            <td colspan="7"></td>
                        </tr>
                        <!-- Asset End -->

                        <!-- Liabilities Start -->
                         <tr>
                            <td colspan="8"></td>
                         </tr>
                        <tr>
                            <td><strong>Liability</strong></td>
                            <td colspan="7"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><strong>Short Term Liability</strong></td>
                            <td colspan="6"></td>
                        </tr>

                        <!-- short term liability  -->
                        @foreach($shortTermLiabilities as $shortTermLiability)
                        <tr>
                            <td colspan="2"></td>
                            <td>{{ $shortTermLiability->account_name }}</td>
                            <td colspan="4"></td>
                        </tr>
                        @endforeach
        
                        <tr>
                            <td></td>
                            <td><strong>Long Term Liability</strong></td>
                            <td colspan="4"></td>
                        </tr>

                        <!-- long term liability  -->
                        @foreach($longTermLiabilities as $longTermLiability)
                        <tr>
                            <td colspan="2"></td>
                            <td>{{ $longTermLiability->account_name }}</td>
                            <td colspan="4"></td>
                        </tr>
                        @endforeach

                        <tr>
                            <td></td>
                            <td><strong>Current Liability</strong></td>
                            <td colspan="4"></td>
                        </tr>

                        <!-- current liability  -->
                        @foreach($currentLiabilities as $currentLiability)
                        <tr>
                            <td colspan="2"></td>
                            <td>{{ $currentLiability->account_name }}</td>
                            <td colspan="4"></td>
                        </tr>
                        @endforeach
                        
                        <tr>
                            <td>
                                <strong>Total Liability</strong>
                            </td>
                            <td colspan="7"></td>
                        </tr>
                        <tr>
                            <td colspan="8"></td>
                        </tr>
                        <!-- Liabilities End -->

                        <!-- Equity Start -->
                        <tr>
                            <td><strong>Equity</strong></td>
                            <td colspan="7"></td>
                        </tr>

                        <tr>
                            <td></td>
                            <td><strong>Equity Capitals</strong></td>
                            <td colspan="4"></td>
                        </tr>

                        <!-- Equity Capitals  -->
                        @foreach($equityCapitals as $equityCapital)
                        <tr>
                            <td colspan="2"></td>
                            <td>{{ $equityCapital->account_name }}</td>
                            <td colspan="4"></td>
                        </tr>
                        @endforeach

                        <tr>
                            <td></td>
                            <td><strong>Retained Earnings</strong></td>
                            <td colspan="4"></td>
                        </tr>

                        <!-- Retained Earnings  -->
                        @foreach($retainedEarnings as $retainedEarning)
                        <tr>
                            <td colspan="2"></td>
                            <td>{{ $retainedEarning->account_name }}</td>
                            <td colspan="4"></td>
                        </tr>
                        @endforeach
                        

                        <tr>
                            <td><strong>Total Equity</strong></td>
                            <td colspan="7"></td>
                        </tr>
                        <!-- Equity End -->

                        <!-- Total Liability and Equity -->
                        <tr>
                            <td>Total Liability and Equity</td> 
                            <td colspan="3"></td>
                            <td>A=L+E</td>
                            <td></td>
                            <td>A=L+E</td>
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
