<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\PurchaseHistoryLog;
use Illuminate\Support\Facades\DB;
use App\Models\SalesReturn;
use Illuminate\Support\Carbon;
use App\Models\PurchaseHistory;

class FinancialStatementController extends Controller
{
    public function balanceSheet(Request $request)
    {
        //Net Profit
        $netProfit = $this->calculateNetProfit($request);
        //Today Profit
        $todayProfit = $this->calculateTodayProfit();
        //Today Loss
        $todayLoss = $this->calculateTodayLoss();
        //Net Profit Till Yesterday
        $netProfitTillYesterday = $this->calculateNetProfitTillYesterday();

        //All Fixed Asset
        $fixedAssetIds = ChartOfAccount::where('sub_account_head', 'Fixed Asset')
            ->pluck('id');

        $fixedAsset = Transaction::whereIn('chart_of_account_id', $fixedAssetIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        // $yesterday = date('Y-d-m', strtotime('-1 day'));
        $yesterday = Carbon::yesterday();
        $today = date('Y-m-d');
        $branchId = auth()->user()->branch_id;

        //Current Asset yesterday to today
        $currentAssets = ChartOfAccount::where('sub_account_head', 'Current Asset')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yesterday) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('date', '<=', $yesterday);
            }], 'at_amount')
            ->where('status', 0)
            ->get();

        $currentAssets->each(function ($asset) use ($today) {
            $asset->total_debit_today = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Purchase')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        $currentAssets->each(function ($asset) use ($today) {
            $asset->total_credit_today = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->whereIn('transaction_type', ['Sold', 'Depreciation'])
                ->whereDate('date', $today)
                ->sum('at_amount');
        });   

        //Fixed Asset yesterday to today
        $fixedAssets = ChartOfAccount::where('sub_account_head', 'Fixed Asset')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yesterday) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('date', '<=', $yesterday);
            }], 'at_amount')
            ->get();

        $fixedAssets->each(function ($asset) use ($yesterday) {
            $asset->total_debit_yesterday = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Purchase')
                ->whereDate('date', '<=',  $yesterday)
                ->sum('at_amount');
        });

        $fixedAssets->each(function ($asset) use ($yesterday) {
            $asset->total_credit_yesterday = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->whereIn('transaction_type', ['Sold', 'Depreciation'])
                ->whereDate('date', '<=',  $yesterday)
                ->sum('at_amount');
        });

        $fixedAssets->each(function ($asset) use ($today) {
            $asset->total_debit_today = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Purchase')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        $fixedAssets->each(function ($asset) use ($today) {
            $asset->total_credit_today = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->whereIn('transaction_type', ['Sold', 'Depreciation'])
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        // dd($fixedAssets);

        //Short Term Liabilities yesterday to today
        $shortTermLiabilities = ChartOfAccount::where('sub_account_head', 'Short Term Liabilities')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yesterday) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('date', '<=', $yesterday);
            }], 'at_amount')
            ->get();

        $shortTermLiabilities->each(function ($liability) use ($today) {
            $liability->total_debit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        $shortTermLiabilities->each(function ($liability) use ($today) {
            $liability->total_credit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        //Long Term Liabilities yesterday to today
        $longTermLiabilities = ChartOfAccount::where('sub_account_head', 'Long Term Liabilities')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yesterday) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('date', '<=', $yesterday);
            }], 'at_amount')
            ->get();

        $longTermLiabilities->each(function ($liability) use ($today) {
            $liability->total_debit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        $longTermLiabilities->each(function ($liability) use ($today) {
            $liability->total_credit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        //Current Liabilities yesterday to today
        $currentLiabilities = ChartOfAccount::where('sub_account_head', 'Current Liabilities')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yesterday) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('date', '<=', $yesterday);
            }], 'at_amount')
            ->get();

        $currentLiabilities->each(function ($liability) use ($today) {
            $liability->total_debit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        $currentLiabilities->each(function ($liability) use ($today) {
            $liability->total_credit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        //Equity Capital yesterday to today
        $equityCapitals = ChartOfAccount::where('sub_account_head', 'Equity Capital')
            ->where('branch_id', $branchId)
            ->withSum(['transactions' => function ($query) use ($branchId, $today) {
                $query->where('branch_id', $branchId)
                    ->where('transaction_type', 'Payment')
                    ->whereDate('date', '!=', $today);
            }], 'at_amount')
            ->get();

        $equityCapitals->each(function ($equity) use ($branchId, $yesterday) {
            $equity->total_previous_payment = $equity->transactions()
                ->where('branch_id', $branchId)
                ->where('transaction_type', 'Payment')
                ->whereDate('date','<=', $yesterday)
                ->sum('at_amount');
        });

        $equityCapitals->each(function ($equity) use ($branchId, $yesterday) {
            $equity->total_previous_receive = $equity->transactions()
                ->where('branch_id', $branchId)
                ->where('transaction_type', 'Received')
                ->whereDate('date','<=', $yesterday)
                ->sum('at_amount');
        });

        $equityCapitals->each(function ($equity) use ($branchId, $today) {
            $equity->total_debit_today = $equity->transactions()
                ->where('branch_id', $branchId)
                ->where('transaction_type', 'Received')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        $equityCapitals->each(function ($equity) use ($branchId, $today) {
            $equity->total_credit_today = $equity->transactions()
                ->where('branch_id', $branchId)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        // dd($equityCapitals);
        

        //Retained Earnings yesterday to today
        $retainedEarnings = ChartOfAccount::where('sub_account_head', 'Retained Earnings')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yesterday) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('date', '<=', $yesterday);
            }], 'at_amount')
            ->get();

        $retainedEarnings->each(function ($liability) use ($today) {
            $liability->total_debit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        $retainedEarnings->each(function ($liability) use ($today) {
            $liability->total_credit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        //All current assets
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

        //Account Receivables date to date
        $accountReceiveableIds = ChartOfAccount::where('sub_account_head', 'Account Receivable')
            ->where('branch_id', auth()->user()->branch_id)
            ->pluck('id');

        $accountReceiveables = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
                    ->where('status', 0)
                    ->where('branch_id', auth()->user()->branch_id);

        if (request()->has('startDate') && request()->has('endDate')) {
            $accountReceiveables->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
        }

        $orderDues = Order::where('branch_id', auth()->user()->branch_id);

        if (request()->has('startDate') && request()->has('endDate')) {
            $orderDues->whereBetween('orderdate', [request()->input('startDate'), request()->input('endDate')]);
        }

        $accountReceiveable = $accountReceiveables->sum('at_amount') + $orderDues->sum('due');

        //Yesterday's account receivable
        $yest = Carbon::yesterday()->format('Y-m-d');

        $yesAccountReceiveables = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
                            ->where('status', 0)
                            ->where('branch_id', auth()->user()->branch_id)
                            ->whereDate('date', $yest);

        $yesOrderDues = Order::where('branch_id', auth()->user()->branch_id)
                        ->whereDate('orderdate', $yest);

        $yesAccountReceiveable = $yesAccountReceiveables->sum('at_amount') + $yesOrderDues->sum('due');

        //Account Payable date to date
        $accountPayableIds = ChartOfAccount::where('sub_account_head', 'Account Payable')
            ->where('branch_id', auth()->user()->branch_id)
            ->pluck('id');
        $accountPayables = Transaction::whereIn('chart_of_account_id', $accountPayableIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id);

        if (request()->has('startDate') && request()->has('endDate')) {
            $accountPayables->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
        }

        //Inventory date to date
        $inventory = PurchaseHistoryLog::when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
            $query->whereBetween('log_date', [$request->input('startDate'), $request->input('endDate')]);
        })
        ->sum('total_amount');

        $yester = Carbon::yesterday()->format('Y-m-d');

        $yesInventory = PurchaseHistoryLog::whereDate('log_date', $yester)->sum('total_amount');

        $purchaseDues = Purchase::where('branch_id', auth()->user()->branch_id);

        if (request()->has('startDate') && request()->has('endDate')) {
            $purchaseDues->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
        }

        //Total account payable
        $accountPayable = $accountPayables->sum('at_amount') + $purchaseDues->sum('due_amount');

        //All current liabilities
        $currentLiabilityIds = ChartOfAccount::where('sub_account_head', 'Current Liabilities')
            ->pluck('id');
        $currentLiability = Transaction::whereIn('chart_of_account_id', $currentLiabilityIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');

        //All long term liabilities
        $longTermLiabilityIds = ChartOfAccount::where('sub_account_head', 'Long Term Liabilities')
            ->pluck('id');
        $longTermLiability = Transaction::whereIn('chart_of_account_id', $longTermLiabilityIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        //All equity capital
        $equityCapitalIds = ChartOfAccount::where('sub_account_head', 'Equity Capital')
            ->pluck('id');
        $equityCapital = Transaction::whereIn('chart_of_account_id', $equityCapitalIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');

        //All retained earnings
        $retainedEarningIds = ChartOfAccount::where('sub_account_head', 'Retained Earnings')
            ->pluck('id');
        $retainedEarning = Transaction::whereIn('chart_of_account_id', $retainedEarningIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');

            //Cash Increment
            //Cash Income Increment date to date
            $CashIncomeIncrement = Transaction::where('table_type', 'Income')
                ->whereIn('transaction_type', ['Current', 'Advance'])
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $CashIncomeIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $CashIncomeIncrement = $CashIncomeIncrement->sum('at_amount');

            //Cash Expense Increment date to date
            $CashExpenseIncrement = Transaction::where('table_type', 'Expenses')
                ->whereIn('transaction_type', ['Current', 'Prepaid'])
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)->sum('at_amount');

            
            //Cash Asset Increment date to date
            $CashAssetIncrement = Transaction::where('table_type', 'Assets')
                ->where('transaction_type', 'Sold')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)->sum('at_amount');

            


            //Cash Liabilities Increment date to date
            $CashLiabilitiesIncrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Received')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $CashLiabilitiesIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $CashLiabilitiesIncrement = $CashLiabilitiesIncrement->sum('at_amount');

            //Cash Equity Increment date to date
            $CashEquityIncrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Received')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $CashEquityIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $CashEquityIncrement = $CashEquityIncrement->sum('at_amount');

            //Total Cash Increment
            $totalCashIncrements = $CashIncomeIncrement + $CashAssetIncrement + $CashLiabilitiesIncrement + $CashEquityIncrement;

            //Bank Increment
            //Bank Income Increment date to date
            $BankIncomeIncrement = Transaction::where('table_type', 'Income')
                ->whereIn('transaction_type', ['Current', 'Advance'])
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $BankIncomeIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $BankIncomeIncrement = $BankIncomeIncrement->sum('at_amount');

            //Bank Expense Increment date to date
            $BankExpenseIncrement = Transaction::where('table_type', 'Expenses')
                ->whereIn('transaction_type', ['Current', 'Prepaid'])
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $BankExpenseIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $BankExpenseIncrement = $BankExpenseIncrement->sum('at_amount');

            //Bank Asset Increment date to date
            $BankAssetIncrement = Transaction::where('table_type', 'Assets')
                ->where('transaction_type', 'Sold')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $BankAssetIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $BankAssetIncrement = $BankAssetIncrement->sum('at_amount');

            //Bank Liabilities Increment date to date
            $BankLiabilitiesIncrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Received')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $BankLiabilitiesIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $BankLiabilitiesIncrement = $BankLiabilitiesIncrement->sum('at_amount');

            //Bank Equity Increment date to date
            $BankEquityIncrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Received')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $BankEquityIncrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $BankEquityIncrement = $BankEquityIncrement->sum('at_amount');

            //Total Bank Increment
            $totalBankIncrements = $BankIncomeIncrement + $BankAssetIncrement + $BankLiabilitiesIncrement + $BankEquityIncrement + $BankExpenseIncrement;

            //Cash Decrement

            //Cash Expense Decrement date to date
            $expenseCashDecrement = Transaction::where('table_type', 'Expenses')
                ->whereIn('transaction_type', ['Current', 'Due Adjust'])
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

            //Cash Asset Decrement date to date
            $assetCashDecrement = Transaction::where('table_type', 'Assets')
                ->whereIn('transaction_type', ['Payment', 'Purchase'])
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

            //Cash Liabilities Decrement date to date
            $liabilitiesCashDecrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $liabilitiesCashDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $liabilitiesCashDecrement = $liabilitiesCashDecrement->sum('at_amount');

            //Cash Equity Decrement date to date
            $equityCashDecrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $equityCashDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $equityCashDecrement = $equityCashDecrement->sum('at_amount');

            //Cash Income Decrement date to date
            $incomeCashDecrement = Transaction::where('table_type', 'Income')
                ->where('transaction_type', 'Refund')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $incomeCashDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $incomeCashDecrement = $incomeCashDecrement->sum('at_amount');

            //Total Cash Decrement
            $totalCashDecrements = $expenseCashDecrement + $assetCashDecrement + $liabilitiesCashDecrement + $equityCashDecrement + $incomeCashDecrement;

            //Bank Decrement

            //Bank Expense Decrement date to date
            $expenseBankDecrement = Transaction::where('table_type', 'Expenses')
                ->where('transaction_type', 'Due Adjust')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $expenseBankDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $expenseBankDecrement = $expenseBankDecrement->sum('at_amount');

            //Bank Asset Decrement date to date
            $assetBankDecrement = Transaction::where('table_type', 'Assets')
                ->whereIn('transaction_type', ['Payment', 'Purchase'])
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $assetBankDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $assetBankDecrement = $assetBankDecrement->sum('at_amount');

            //Bank Liabilities Decrement date to date
            $liabilitiesBankDecrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $liabilitiesBankDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $liabilitiesBankDecrement = $liabilitiesBankDecrement->sum('at_amount');

            //Bank Equity Decrement date to date
            $equityBankDecrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $equityBankDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $equityBankDecrement = $equityBankDecrement->sum('at_amount');

            //Bank Income Decrement date to date
            $IncomeBankDecrement = Transaction::where('table_type', 'Income')
                ->where('transaction_type', 'Refund')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id);

            if (request()->has('startDate') && request()->has('endDate')) {
                $IncomeBankDecrement->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
            }

            $IncomeBankDecrement = $IncomeBankDecrement->sum('at_amount');

            //Total Bank Decrement
            $totalBankDecrements = $expenseBankDecrement + $assetBankDecrement + $liabilitiesBankDecrement + $equityBankDecrement + $IncomeBankDecrement;

            //Cash in Hand and Bank
            $cashInHand = $totalCashIncrements - $totalCashDecrements;
            $cashInBank = $totalBankIncrements - $totalBankDecrements;
            
        // dd($totalCashIncrements);

            //Total till yesterday Cash in Hand, Cash at Bank start 
            $yest = Carbon::yesterday();

            //Till Yesterday Cash Increment

            $yestCashIncomeIncrement = Transaction::where('table_type', 'Income')
                ->whereIn('transaction_type', ['Current', 'Advance'])
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', '<=', $yest)
                ->sum('at_amount');

            $yestCashExpenseIncrement = Transaction::where('table_type', 'Expenses')
                ->whereIn('transaction_type', ['Current', 'Prepaid'])
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', '<=', $yest)
                ->sum('at_amount');

            $yestCashAssetIncrement = Transaction::where('table_type', 'Assets')
                ->where('transaction_type', 'Sold')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', '<=', $yest)
                ->sum('at_amount');

            $yestCashLiabilitiesIncrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Received')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', '<=', $yest)
                ->sum('at_amount');

            $yestCashEquityIncrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Received')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', '<=', $yest)
                ->sum('at_amount');

        $totalYestCashIncrement = $yestCashIncomeIncrement  + $yestCashAssetIncrement + $yestCashLiabilitiesIncrement + $yestCashEquityIncrement;

                // dd($totalYestCashIncrement);
            //Till Yesterday Cash Decrement

            $yestExpenseCashDecrement = Transaction::where('table_type', 'Expenses')
                ->whereIn('transaction_type', ['Current', 'Due Adjust'])
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');
                

            $yestAssetCashDecrement = Transaction::where('table_type', 'Assets')
                ->whereIn('transaction_type', ['Payment', 'Purchase'])
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');

            $yestLiabilitiesCashDecrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');

            $yestEquityCashDecrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');

            $yestIncomeCashDecrement = Transaction::where('table_type', 'Income')
                ->where('transaction_type', 'Refund')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');

        $totalYestCashDecrement = $yestExpenseCashDecrement + $yestAssetCashDecrement + $yestLiabilitiesCashDecrement + $yestEquityCashDecrement + $yestIncomeCashDecrement;

            //Till Today Bank Increment

            $yestExpenseBankIncrement = Transaction::where('table_type', 'Expenses')
                ->where('transaction_type', 'Due Adjust')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', $yest)
                ->sum('at_amount');

            $yestAssetBankIncrement = Transaction::where('table_type', 'Assets')
                ->whereIn('transaction_type', ['Payment', 'Purchase'])
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', $yest)
                ->sum('at_amount');

            $yestLiabilitiesBankIncrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', $yest)
                ->sum('at_amount');

            $yestEquityBankIncrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', $yest)
                ->sum('at_amount');

            $yestIncomeBankIncrement = Transaction::where('table_type', 'Income')
                ->where('transaction_type', 'Refund')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', $yest)
                ->sum('at_amount');

        
        
        $totalYestBankIncrement = $yestExpenseBankIncrement + $yestAssetBankIncrement + $yestLiabilitiesBankIncrement + $yestEquityBankIncrement + $yestIncomeBankIncrement;


            //Till Yesterday Bank Decrement

            $yestExpenseBankDecrement = Transaction::where('table_type', 'Expenses')
                ->where('transaction_type', 'Due Adjust')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', $yest)
                ->sum('at_amount');

            $yestAssetBankDecrement = Transaction::where('table_type', 'Assets')
                ->whereIn('transaction_type', ['Payment', 'Purchase'])
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', $yest)
                ->sum('at_amount');

            $yestLiabilitiesBankDecrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', $yest)
                ->sum('at_amount');

            $yestEquityBankDecrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', $yest)
                ->sum('at_amount');

            $yestIncomeBankDecrement = Transaction::where('table_type', 'Income')
                ->where('transaction_type', 'Refund')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', $yest)
                ->sum('at_amount');

        $totalYestBankDecrement = $yestExpenseBankDecrement + $yestAssetBankDecrement + $yestLiabilitiesBankDecrement + $yestEquityBankDecrement + $yestIncomeBankDecrement;  

        $yesCashInHand = $totalYestCashIncrement - $totalYestCashDecrement;
        $yesBankInHand = $totalYestBankIncrement - $totalYestBankDecrement;
            // dd($totalYestCashIncrement);
        return view('admin.balance_sheet.index', compact('currentAssetIds', 'currentBankAsset', 'currentCashAsset', 'accountReceiveable', 'accountPayable', 'currentLiability', 'longTermLiabilities', 'equityCapital', 'retainedEarning','currentAssets', 'fixedAssets', 'fixedAsset', 'shortTermLiabilities', 'currentLiabilities', 'equityCapitals', 'retainedEarnings', 'cashInHand', 'cashInBank', 'inventory', 'netProfit', 'yesCashInHand', 'yesBankInHand', 'yesAccountReceiveable', 'yesInventory', 'todayLoss', 'todayProfit', 'netProfitTillYesterday'));
    }

    public function calculateNetProfit(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $branchId = auth()->user()->branch_id;

        // Sales sum
        $salesSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->where('branch_id', $branchId)
            ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('startDate'), $request->input('endDate')]);
            })
            ->sum('amount');

        // Sales Return
        $salesReturn = SalesReturn::where('branch_id', $branchId)
            ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                $query->whereBetween('returndate', [$request->input('startDate'), $request->input('endDate')]);
            })
            ->sum('net_total');

        // Sales Discount
        $salesDiscount = Order::where('branch_id', $branchId)
            ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                $query->whereBetween('orderdate', [$request->input('startDate'), $request->input('endDate')]);
            })
            ->sum('discount_amount');

        // Purchase sum (Cost of Goods Sold)
        $purchaseSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('startDate'), $request->input('endDate')]);
            })
            ->sum('amount');

        // Operating Expenses
        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');
        $operatingExpenseSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('startDate'), $request->input('endDate')]);
            })
            ->sum('amount');

        // Administrative Expenses
        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');
        $administrativeExpenseSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('startDate'), $request->input('endDate')]);
            })
            ->sum('amount');

        // VAT Calculations
        $purchaseVatSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('startDate'), $request->input('endDate')]);
            })
            ->sum('vat_amount');

        $salesVatSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('startDate'), $request->input('endDate')]);
            })
            ->sum('vat_amount');

        $operatingExpenseVatSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('startDate'), $request->input('endDate')]);
            })
            ->sum('vat_amount');

        $administrativeExpenseVatSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('startDate'), $request->input('endDate')]);
            })
            ->sum('vat_amount');

        // Net Profit Calculation
        $taxAndVat = $purchaseVatSum + $salesVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum;
        $netSales = $salesSum - $salesReturn - $salesDiscount;
        $grossProfit = $netSales - $purchaseSum;
        $profitBeforeTax = $grossProfit - $operatingExpenseSum - $administrativeExpenseSum;
        $netProfit = $profitBeforeTax - $taxAndVat;

        return $netProfit;
    }

    public function calculateTodayProfit()
    {
        $branchId = auth()->user()->branch_id;
        $today = now()->format('Y-m-d');

        $salesSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->where('branch_id', $branchId)
            ->whereDate('date', $today)
            ->sum('amount');

        $purchaseVatSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereDate('date', $today)
            ->sum('vat_amount');

        $salesVatSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereDate('date', $today)
            ->sum('vat_amount');

        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');
        $operatingExpenseVatSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', $today)
            ->sum('vat_amount');

        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');
        $administrativeExpenseVatSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', $today)
            ->sum('vat_amount');

        $netSales = $salesSum;
        $taxAndVat = $purchaseVatSum + $salesVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum;

        $netProfit = $netSales - $taxAndVat;

        return $netProfit;
    }

    public function calculateTodayLoss()
    {
        $branchId = auth()->user()->branch_id;
        $today = now()->format('Y-m-d');

        $salesReturn = SalesReturn::where('branch_id', $branchId)
            ->whereDate('returndate', $today)
            ->sum('net_total');

        $salesDiscount = Order::where('branch_id', $branchId)
            ->whereDate('orderdate', $today)
            ->sum('discount_amount');

        $purchaseSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereDate('date', $today)
            ->sum('amount');

        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');
        $operatingExpenseSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', $today)
            ->sum('amount');

        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');
        $administrativeExpenseSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', $today)
            ->sum('amount');

        $purchaseVatSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereDate('date', $today)
            ->sum('vat_amount');

        $operatingExpenseVatSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', $today)
            ->sum('vat_amount');

        $administrativeExpenseVatSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', $today)
            ->sum('vat_amount');

        $totalLoss = $salesReturn + $salesDiscount + $purchaseSum + $operatingExpenseSum + $administrativeExpenseSum + $purchaseVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum;

        return $totalLoss;
    }

    public function calculateNetProfitTillYesterday()
    {
        $branchId = auth()->user()->branch_id;

        // Calculate the date for "yesterday"
        $yesterday = now()->subDay()->format('Y-m-d');

        // Sales sum
        $salesSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yesterday)
            ->sum('amount');

        // Sales Return
        $salesReturn = SalesReturn::where('branch_id', $branchId)
            ->whereDate('returndate', '<=', $yesterday)
            ->sum('net_total');

        // Sales Discount
        $salesDiscount = Order::where('branch_id', $branchId)
            ->whereDate('orderdate', '<=', $yesterday)
            ->sum('discount_amount');

        // Purchase sum (Cost of Goods Sold)
        $purchaseSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereDate('date', '<=', $yesterday)
            ->sum('amount');

        // Operating Expenses
        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');
        $operatingExpenseSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yesterday)
            ->sum('amount');

        // Administrative Expenses
        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');
        $administrativeExpenseSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yesterday)
            ->sum('amount');

        // VAT Calculations
        $purchaseVatSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereDate('date', '<=', $yesterday)
            ->sum('vat_amount');

        $salesVatSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereDate('date', '<=', $yesterday)
            ->sum('vat_amount');

        $operatingExpenseVatSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yesterday)
            ->sum('vat_amount');

        $administrativeExpenseVatSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yesterday)
            ->sum('vat_amount');

        // Net Profit Calculation
        $taxAndVat = $purchaseVatSum + $salesVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum;
        $netSales = $salesSum - $salesReturn - $salesDiscount;
        $grossProfit = $netSales - $purchaseSum;
        $profitBeforeTax = $grossProfit - $operatingExpenseSum - $administrativeExpenseSum;
        $netProfitTillYesterday = $profitBeforeTax - $taxAndVat;

        return $netProfitTillYesterday;
    }

}
