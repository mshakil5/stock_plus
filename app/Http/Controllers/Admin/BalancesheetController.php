<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\Transaction;

class BalancesheetController extends Controller
{
    public function balanceSheet(Request $request)
    {
        $fixedAssetIds = ChartOfAccount::where('sub_account_head', 'Fixed Asset')
            ->pluck('id');

        $fixedAsset = Transaction::whereIn('chart_of_account_id', $fixedAssetIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        $yesterday = now()->subDay()->endOfDay();
        $today = now()->startOfDay();
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay(); 
        $branchId = auth()->user()->branch_id;

        $currentAssets = ChartOfAccount::where('sub_account_head', 'Current Asset')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yesterday) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('created_at', '<=', $yesterday);
            }], 'at_amount')
            ->get();

        $currentAssets->each(function ($asset) use ($todayStart, $todayEnd) {
            $asset->total_debit_today = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Purchase')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $currentAssets->each(function ($asset) use ($todayStart, $todayEnd) {
            $asset->total_credit_today = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->whereIn('transaction_type', ['Sold', 'Depreciation'])
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });           

        $fixedAssets = ChartOfAccount::where('sub_account_head', 'Fixed Asset')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yesterday) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('created_at', '<=', $yesterday);
            }], 'at_amount')
            ->get();

        $fixedAssets->each(function ($asset) use ($todayStart, $todayEnd) {
            $asset->total_debit_today = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Purchase')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $fixedAssets->each(function ($asset) use ($todayStart, $todayEnd) {
            $asset->total_credit_today = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->whereIn('transaction_type', ['Sold', 'Depreciation'])
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $shortTermLiabilities = ChartOfAccount::where('sub_account_head', 'Short Term Liabilities')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yesterday) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('created_at', '<=', $yesterday);
            }], 'at_amount')
            ->get();

        $shortTermLiabilities->each(function ($liability) use ($todayStart, $todayEnd) {
            $liability->total_debit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $shortTermLiabilities->each(function ($liability) use ($todayStart, $todayEnd) {
            $liability->total_credit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $longTermLiabilities = ChartOfAccount::where('sub_account_head', 'Long Term Liabilities')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yesterday) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('created_at', '<=', $yesterday);
            }], 'at_amount')
            ->get();

        $longTermLiabilities->each(function ($liability) use ($todayStart, $todayEnd) {
            $liability->total_debit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $longTermLiabilities->each(function ($liability) use ($todayStart, $todayEnd) {
            $liability->total_credit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $currentLiabilities = ChartOfAccount::where('sub_account_head', 'Current Liabilities')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yesterday) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('created_at', '<=', $yesterday);
            }], 'at_amount')
            ->get();

        $currentLiabilities->each(function ($liability) use ($todayStart, $todayEnd) {
            $liability->total_debit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $currentLiabilities->each(function ($liability) use ($todayStart, $todayEnd) {
            $liability->total_credit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $equityCapitals = ChartOfAccount::where('sub_account_head', 'Equity Capital')
            ->where('branch_id', $branchId)
            ->withSum(['transactions' => function ($query) use ($branchId, $yesterday) {
                $query->where('branch_id', $branchId)
                    ->whereDate('created_at', '<=', $yesterday);
            }], 'at_amount')
            ->get();

        $equityCapitals->each(function ($equity) use ($branchId, $todayStart, $todayEnd) {
            $equity->total_debit_today = $equity->transactions()
                ->where('branch_id', $branchId)
                ->where('transaction_type', 'Received')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $equityCapitals->each(function ($equity) use ($branchId, $todayStart, $todayEnd) {
            $equity->total_credit_today = $equity->transactions()
                ->where('branch_id', $branchId)
                ->where('transaction_type', 'Payment')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $retainedEarnings = ChartOfAccount::where('sub_account_head', 'Retained Earnings')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yesterday) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('created_at', '<=', $yesterday);
            }], 'at_amount')
            ->get();

        $retainedEarnings->each(function ($liability) use ($todayStart, $todayEnd) {
            $liability->total_debit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $retainedEarnings->each(function ($liability) use ($todayStart, $todayEnd) {
            $liability->total_credit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('at_amount');
        });

        $currentAssetIds = ChartOfAccount::where('sub_account_head', 'Current Asset')
            ->pluck('id');
    
        $currentBankAsset = Transaction::whereIn('chart_of_account_id', $currentAssetIds)
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');
        $currentCashAsset = Transaction::whereIn('chart_of_account_id', $currentAssetIds)
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');

        $accountReceiveableIds = ChartOfAccount::where('sub_account_head', 'Account Receivable')
            ->pluck('id');
        $accountReceiveable = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');

        $accountPayableIds = ChartOfAccount::where('sub_account_head', 'Account Payable')
            ->pluck('id');
        $accountPayable = Transaction::whereIn('chart_of_account_id', $accountPayableIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');

        $currentLiabilityIds = ChartOfAccount::where('sub_account_head', 'Current Liabilities')
            ->pluck('id');
        $currentLiability = Transaction::whereIn('chart_of_account_id', $currentLiabilityIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');

        $longTermLiabilityIds = ChartOfAccount::where('sub_account_head', 'Long Term Liabilities')
            ->pluck('id');
        // $longTermLiabilities = Transaction::whereIn('chart_of_account_id', $longTermLiabilityIds)
        //     ->where('status', 0)
        //     ->where('branch_id', auth()->user()->branch_id)
        //     ->get();

        $equityCapitalIds = ChartOfAccount::where('sub_account_head', 'Equity Capital')
            ->pluck('id');
        $equityCapital = Transaction::whereIn('chart_of_account_id', $equityCapitalIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');

        $retainedEarningIds = ChartOfAccount::where('sub_account_head', 'Retained Earnings')
            ->pluck('id');
        $retainedEarning = Transaction::whereIn('chart_of_account_id', $retainedEarningIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');
            // dd($equityCapital);

            $start_date = request()->input('startDate');
            $end_date = request()->input('endDate');

            //Cash Increment
            $CashIncomeIncrement = Transaction::where('table_type', 'Income')
            ->whereIn('transaction_type', ['Current', 'Advance'])
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $CashIncomeIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $CashIncomeIncrement = $CashIncomeIncrement->sum('at_amount');

            $CashAssetIncrement = Transaction::where('table_type', 'Assets')
            ->where('transaction_type', 'Sold')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $CashAssetIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $CashAssetIncrement = $CashAssetIncrement->sum('at_amount');

            $CashLiabilitiesIncrement = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $CashLiabilitiesIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $CashLiabilitiesIncrement = $CashLiabilitiesIncrement->sum('at_amount');

            $CashEquityIncrement = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $CashEquityIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $CashEquityIncrement = $CashEquityIncrement->sum('at_amount');

            $totalCashIncrements = $CashIncomeIncrement + $CashAssetIncrement + $CashLiabilitiesIncrement + $CashEquityIncrement;

            // dd($totalIncrements);

            //Bank Increment
            $BankIncomeIncrement = Transaction::where('table_type', 'Income')
            ->whereIn('transaction_type', ['Current', 'Advance'])
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $BankIncomeIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $BankIncomeIncrement = $BankIncomeIncrement->sum('at_amount');

            $BankAssetIncrement = Transaction::where('table_type', 'Assets')
            ->where('transaction_type', 'Sold')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $BankAssetIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $BankAssetIncrement = $BankAssetIncrement->sum('at_amount');

            $BankLiabilitiesIncrement = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $BankLiabilitiesIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $BankLiabilitiesIncrement = $BankLiabilitiesIncrement->sum('at_amount');

            $BankEquityIncrement = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $BankEquityIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $BankEquityIncrement = $BankEquityIncrement->sum('at_amount');

            $totalBankIncrements = $BankIncomeIncrement + $BankAssetIncrement + $BankLiabilitiesIncrement + $BankEquityIncrement;

            $totalIncrements = $totalCashIncrements + $totalBankIncrements;

            //Cash Decrement
            $expenseCashDecrement = Transaction::whereIn('table_type', ['Expenses', 'Cogs'])
            ->whereIn('transaction_type', ['Current', 'Prepaid'])
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $expenseCashDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $expenseCashDecrement = $expenseCashDecrement->sum('at_amount');

            $assetCashDecrement = Transaction::where('table_type', 'Assets')
            ->where('transaction_type', 'Purchase')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $assetCashDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $assetCashDecrement = $assetCashDecrement->sum('at_amount');

            $liabilitiesCashDecrement = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $liabilitiesCashDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $liabilitiesCashDecrement = $liabilitiesCashDecrement->sum('at_amount');

            $equityCashDecrement = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $equityCashDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $equityCashDecrement = $equityCashDecrement->sum('at_amount');

            $totalCashDecrements = $expenseCashDecrement + $assetCashDecrement + $liabilitiesCashDecrement + $equityCashDecrement;

            //Bank Decrement
            $expenseBankDecrement = Transaction::whereIn('table_type', ['Expenses', 'Cogs'])
            ->where('transaction_type', 'Due Adjust')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $expenseBankDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $expenseBankDecrement = $expenseBankDecrement->sum('at_amount');

            $assetBankDecrement = Transaction::where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Payment', 'Purchase'])
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $assetBankDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $assetBankDecrement = $assetBankDecrement->sum('at_amount');

            $liabilitiesBankDecrement = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $liabilitiesBankDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $liabilitiesBankDecrement = $liabilitiesBankDecrement->sum('at_amount');

            $equityBankDecrement = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $equityBankDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $equityBankDecrement = $equityBankDecrement->sum('at_amount');

            $totalBankDecrements = $expenseBankDecrement + $assetBankDecrement + $liabilitiesBankDecrement + $equityBankDecrement;

            $totalDecrements = $totalCashDecrements + $totalBankDecrements;

        return view('admin.balance_sheet.index', compact('currentAssetIds', 'currentBankAsset', 'currentCashAsset', 'accountReceiveable', 'accountPayable', 'currentLiability', 'longTermLiabilities', 'equityCapital', 'retainedEarning','currentAssets', 'fixedAssets', 'fixedAsset', 'shortTermLiabilities', 'currentLiabilities', 'equityCapitals', 'retainedEarnings'));
    }

    public function balanceSheetByDate(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $branchId = auth()->user()->branch_id;

        $fixedAssetIds = ChartOfAccount::where('sub_account_head', 'Fixed Asset')->pluck('id');
        $fixedAssets = Transaction::whereIn('chart_of_account_id', $fixedAssetIds)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->get();

        $currentAssetIds = ChartOfAccount::where('sub_account_head', 'Current Asset')->pluck('id');
        $currentBankAsset = Transaction::whereIn('chart_of_account_id', $currentAssetIds)
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('branch_id', $branchId)
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $currentCashAsset = Transaction::whereIn('chart_of_account_id', $currentAssetIds)
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('branch_id', $branchId)
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $accountReceiveableIds = ChartOfAccount::where('sub_account_head', 'Account Receivable')->pluck('id');
        $accountReceiveable = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        return view('admin.balance_sheet.index', compact('fixedAssets', 'currentBankAsset', 'currentCashAsset', 'accountReceiveable'));
    }
}
