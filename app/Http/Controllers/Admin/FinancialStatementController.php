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

    public function getStartDate()
    {
        return view('admin.balance_sheet.start_date');
    }

    public function postStartDate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
        ]);

        return redirect()->route('admin.balancesheet', ['start_date' => $request->input('start_date')]);
    }

    public function balanceSheet(Request $request)
    {
        $startDate = $request->query('start_date');

        $startDate = $request->input('start_date');
        if ($startDate) {
            $yest = Carbon::parse($startDate)->subDay()->format('Y-m-d');
        } else {
            $yest = Carbon::yesterday()->format('Y-m-d');
        }

        //Net Profit
        $netProfit = $this->calculateNetProfit($request);

        //Net Profit Till Yesterday
        $netProfitTillYesterday = $this->calculateNetProfitTillYesterday($yest);

        //All Fixed Asset
        $fixedAssetIds = ChartOfAccount::where('sub_account_head', 'Fixed Asset')
            ->pluck('id');

        $fixedAsset = Transaction::whereIn('chart_of_account_id', $fixedAssetIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        $yesterday = Carbon::yesterday();
        $today = date('Y-m-d');
        $branchId = auth()->user()->branch_id;

        //Current Asset yesterday to today
        $currentAssets = ChartOfAccount::where('sub_account_head', 'Current Asset')
            ->where('branch_id', auth()->user()->branch_id)
            ->with(['transactions' => function ($query) use ($yest) {
                $query->where('branch_id', auth()->user()->branch_id);
            }])

            ->get();
            
        $currentAssets->each(function ($asset) use ($yest) {
            $asset->total_debit_yesterday = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date', '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $currentAssets->each(function ($asset) use ($yest) {
            $asset->total_credit_yesterday = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $currentAssets->each(function ($asset) use ($today) {
            $asset->total_debit_today = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date', $today)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $currentAssets->each(function ($asset) use ($today) {
            $asset->total_credit_today = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', $today)
                ->where('status', 0)
                ->sum('at_amount');
        });  
        
        //Fixed Asset till yesterday, Today debit, credit
        $fixedAssets = ChartOfAccount::where('sub_account_head', 'Fixed Asset')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yest) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->where('status', 0)
                    ->whereDate('date', '<=', $yest);
            }], 'at_amount')
            ->get();

        $fixedAssets->each(function ($asset) use ($yest) {
            $asset->total_debit_yesterday = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Purchase')
                ->whereDate('date', '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $fixedAssets->each(function ($asset) use ($yest) {
            $asset->total_credit_yesterday = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->whereIn('transaction_type', ['Sold', 'Depreciation'])
                ->whereDate('date', '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $fixedAssets->each(function ($asset) use ($today) {
            $asset->total_debit_today = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Purchase')
                ->whereDate('date', $today)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $fixedAssets->each(function ($asset) use ($today) {
            $asset->total_credit_today = $asset->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->whereIn('transaction_type', ['Sold', 'Depreciation'])
                ->whereDate('date', $today)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Account Receivable
        $accountReceiveableIds = ChartOfAccount::where('sub_account_head', 'Account Receivable')
        ->where('branch_id', auth()->user()->branch_id)
        ->pluck('id');

        $todaysAccountReceivableCredit = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
                    ->where('status', 0)
                    ->where('branch_id', auth()->user()->branch_id)
                    ->where('transaction_type', 'Received')
                    ->whereDate('date', $today)
                    ->sum('at_amount');

        $salesReturnCredit = Transaction::where('table_type', 'Income')
                ->where('status', 0)
                ->where('payment_type', 'Account Receivable')
                ->where('transaction_type', 'Return')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

        $totalTodaysAccountReceivableCredit = $todaysAccountReceivableCredit + $salesReturnCredit;


        $todaysAccountReceivableDebit = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
                    ->where('status', 0)
                    ->where('branch_id', auth()->user()->branch_id)
                    ->whereIn('transaction_type', ['Purchase', 'Payment'])
                    ->whereDate('date', $today)
                    ->sum('at_amount');

        $todaysAssetSoldAR = Transaction::whereIn('asset_id', $accountReceiveableIds)
                    ->where('status', 0)
                    ->where('branch_id', auth()->user()->branch_id)
                    ->where('transaction_type', 'Sold')
                    ->whereDate('date', $today)
                    ->sum('at_amount');



        $orderDues = Order::where('branch_id', auth()->user()->branch_id);

        //Yesterday's account receivable
        

        $yesAccountReceiveablesDebit = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
                            ->where('status', 0)
                            ->where('branch_id', auth()->user()->branch_id)
                            ->where('transaction_type', 'Payment')
                            ->where('date', '<=', $yest)
                            ->sum('at_amount');

        $yesAccountReceiveablesCredit = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
                            ->where('status', 0)
                            ->where('branch_id', auth()->user()->branch_id)
                            ->where('transaction_type', 'Received')
                            ->where('date', '<=', $yest)
                            ->sum('at_amount');

        $yesAssetSoldAR = Transaction::whereIn('asset_id', $accountReceiveableIds)
                            ->where('status', 0)
                            ->where('branch_id', auth()->user()->branch_id)
                            ->where('transaction_type', 'Sold')
                            ->where('date', '<=', $yest)
                            ->sum('at_amount');

        $yesProductCreditSold = Transaction::where('status', 0)
                            ->where('table_type', 'Income')
                            ->whereNotNull('order_id')
                            ->where('branch_id', auth()->user()->branch_id)
                            ->where('transaction_type', 'Current')
                            ->where('payment_type', 'Account Receivable')
                            ->where('date', '<=', $yest)
                            ->sum('at_amount');

        $yesReturnAR = Transaction::where('status', 0)
                            ->where('table_type', 'Income')
                            ->whereNotNull('order_id')
                            ->where('branch_id', auth()->user()->branch_id)
                            ->where('transaction_type', 'Return')
                            ->where('payment_type', 'Account Receivable')
                            ->where('date', '<=', $yest)
                            ->sum('at_amount');             

        $yesAccountReceiveable = $yesAccountReceiveablesDebit + $yesAssetSoldAR - $yesAccountReceiveablesCredit + $yesProductCreditSold - $yesReturnAR;
        
        
        $accountPayableIds = ChartOfAccount::where('sub_account_head', 'Account Payable')
            ->where('branch_id', auth()->user()->branch_id)
            ->pluck('id');


        $todaysAccountPayableCredit = Transaction::whereIn('chart_of_account_id', $accountPayableIds)
                ->where('status', 0)
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', $today)
                ->sum('at_amount');

         $todaysPurchaseReturnAP = Transaction::where('table_type', 'Cogs')
                    ->where('transaction_type', 'Return')
                    ->where('status', 0)
                    ->where('payment_type', 'Account Payable')
                    ->whereDate('date', $today)
                    ->where('branch_id', auth()->user()->branch_id)
                    ->sum('at_amount'); 

         $todaysPurchaseReturnAP = Transaction::where('table_type', 'Cogs')
                    ->where('transaction_type', 'Return')
                    ->where('status', 0)
                    ->where('payment_type', 'Account Payable')
                    ->whereDate('date', $today)
                    ->where('branch_id', auth()->user()->branch_id)
                    ->sum('at_amount');    


          $totalTodaysAccountPayableCredit = $todaysAccountPayableCredit + $todaysPurchaseReturnAP;      

        //Today's account payable debit
        $todaysAccountPayableDebit = Transaction::whereIn('chart_of_account_id', $accountPayableIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Received'])
            ->whereDate('date', $today)
            ->sum('at_amount');

        //Today's product purchse by credit
        $todaysCreditPurchaseAP = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('payment_type', 'Account Payable')
            ->where('transaction_type', 'Current')
            ->whereDate('date', $today)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');


         //Todays due account payable debit   
        $todaysDueAccountPayableDebit = Transaction::whereIn('liability_id', $accountPayableIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Payment', 'Due', 'Purchase'])
            ->whereDate('date', $today)
            ->sum('at_amount');


        //This query is related to account receivable
        $todaysProductCreditSold = Transaction::where('status', 0)
            ->whereNotNull('order_id')
            ->where('branch_id', auth()->user()->branch_id)
            ->where('transaction_type', 'Current')
            ->where('payment_type', 'Account Receivable')
            ->whereDate('date', $today)
            ->sum('at_amount');

        //Yesterday's account payable credit    
        $yesAccountPayableCredit = Transaction::whereIn('chart_of_account_id', $accountPayableIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->where('transaction_type', 'Payment')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Yesterday's account payable debit
        $yesAccountPayableDebit = Transaction::whereIn('chart_of_account_id', $accountPayableIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->where('transaction_type', 'Received')
            ->where('date', '<=', $yest)
            ->sum('at_amount'); 

         //Yesterday's Asset expense and due   
        $yesAssetExpenseDue = Transaction::whereIn('liability_id', $accountPayableIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Due', 'Purchase'])
            ->where('date', '<=', $yest)
            ->sum('at_amount'); 

        //Yesterday's product purchse by credit
        $yesProductCreditPurchase = Transaction::where('status', 0)
            ->whereNotNull('purchase_id')
            ->where('branch_id', auth()->user()->branch_id)
            ->where('transaction_type', 'Current')
            ->where('payment_type', 'Account Payable')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //yesterday's purchase return by credit
        $yesPurchaseReturnAP = Transaction::where('table_type', 'Cogs')
            ->where('transaction_type', 'Return')
            ->where('status', 0)
            ->where('payment_type', 'Account Payable')
            ->where('date', '<=', $yest)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount'); 
            
        //Total yesterday's account payable
        $yesAccountPayable = $yesAccountPayableDebit + $yesProductCreditPurchase + $yesAssetExpenseDue - $yesAccountPayableCredit - $yesPurchaseReturnAP;  

        //Short Term Liabilities
        $shortTermLiabilities = ChartOfAccount::where('sub_account_head', 'Short Term Liabilities')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yest) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('date', '<=', $yest)
                    ->where('status', 0);
            }], 'at_amount')
            ->get();

        $shortTermLiabilities->each(function ($liability) use ($yest) {
            $liability->total_debit_yesterday = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date', '<=',  $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });
    
        $shortTermLiabilities->each(function ($liability) use ($yest) {
            $liability->total_credit_yesterday = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', '<=',  $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $shortTermLiabilities->each(function ($liability) use ($today) {
            $liability->total_debit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date', $today)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $shortTermLiabilities->each(function ($liability) use ($today) {
            $liability->total_credit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', $today)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Long Term Liabilities yesterday to today
        $longTermLiabilities = ChartOfAccount::where('sub_account_head', 'Long Term Liabilities')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yest) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('date', '<=', $yest)
                    ->where('status', 0);
            }], 'at_amount')
            ->get();

        $longTermLiabilities->each(function ($liability) use ($yest) {
            $liability->total_debit_yesterday = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date',  '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $longTermLiabilities->each(function ($liability) use ($yest) {
            $liability->total_credit_yesterday = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date',  '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $longTermLiabilities->each(function ($liability) use ($today) {
            $liability->total_debit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date', $today)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $longTermLiabilities->each(function ($liability) use ($today) {
            $liability->total_credit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', $today)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Current Liabilities
        $currentLiabilities = ChartOfAccount::where('sub_account_head', 'Current Liabilities')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yest) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('date', '<=', $yest)
                    ->where('status', 0);
            }], 'at_amount')
            ->get();

        $currentLiabilities->each(function ($liability) use ($today) {
            $liability->total_debit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date', $today)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $currentLiabilities->each(function ($liability) use ($today) {
            $liability->total_credit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', $today)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $currentLiabilities->each(function ($liability) use ($yest) {
            $liability->total_debit_yesterday = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date','<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $currentLiabilities->each(function ($liability) use ($yest) {
            $liability->total_credit_yesterday = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date','<=', $yest)
                ->where('status', 0)
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

        $equityCapitals->each(function ($equity) use ($branchId, $yest) {
            $equity->total_previous_payment = $equity->transactions()
                ->where('branch_id', $branchId)->where('status', 0)
                ->where('transaction_type', 'Payment')
                ->whereDate('date','<=', $yest)
                ->sum('at_amount');
        });

        $equityCapitals->each(function ($equity) use ($branchId, $yest) {
            $equity->total_previous_receive = $equity->transactions()
                ->where('branch_id', $branchId)->where('status', 0)
                ->where('transaction_type', 'Received')
                ->whereDate('date','<=', $yest)
                ->sum('at_amount');
        });

        $equityCapitals->each(function ($equity) use ($branchId, $today) {
            $equity->total_debit_today = $equity->transactions()
                ->where('branch_id', $branchId)->where('status', 0)
                ->where('transaction_type', 'Received')
                ->whereDate('date', $today)
                ->sum('at_amount');
        });

        $equityCapitals->each(function ($equity) use ($branchId, $today) {
            $equity->total_credit_today = $equity->transactions()
                ->where('branch_id', $branchId)->where('status', 0)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', $today)
                ->sum('at_amount');
        }); 

        //Retained Earnings
        $retainedEarnings = ChartOfAccount::where('sub_account_head', 'Retained Earnings')
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum(['transactions' => function ($query) use ($yest) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereDate('date', '<=', $yest)
                    ->where('status', 0);
            }], 'at_amount')
            ->get();

        $retainedEarnings->each(function ($liability) use ($yest) {
            $liability->total_debit_yesterday = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date', '<=',  $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $retainedEarnings->each(function ($liability) use ($yest) {
            $liability->total_credit_yesterday = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', '<=',  $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $retainedEarnings->each(function ($liability) use ($today) {
            $liability->total_debit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Received')
                ->whereDate('date', $today)
                ->where('status', 0)
                ->sum('at_amount');
        });

        $retainedEarnings->each(function ($liability) use ($today) {
            $liability->total_credit_today = $liability->transactions()
                ->where('branch_id', auth()->user()->branch_id)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', $today)
                ->where('status', 0)
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

        //Account Payable date to date
        
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
        //All retained earnings end

            //Cash Increment
            //Cash Income Increment today
            $CashIncomeIncrementToday = Transaction::where('table_type', 'Income')
                ->whereIn('transaction_type', ['Current', 'Advance'])
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

            
            
            //Cash Asset Increment today
            $CashAssetIncrementToday = Transaction::where('table_type', 'Assets')
                ->whereIn('transaction_type', ['Sold', 'Received'])
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

            //Cash Liabilities Increment today
            $CashLiabilitiesIncrementToday = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Received')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

            //Cash Equity Increment today
            $CashEquityIncrementToday = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Received')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');
                
            $PurchaseReturnCashIncrementToday = Transaction::where('table_type', 'Cogs')
                ->where('transaction_type', 'Return')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

            //Total Cash Increment today
            $totalTodayCashIncrements = $CashIncomeIncrementToday + $CashAssetIncrementToday + $CashLiabilitiesIncrementToday + $CashEquityIncrementToday + $PurchaseReturnCashIncrementToday;

            //Bank Increment
            //Bank Income Increment today
            $todayBankIncomeIncrement = Transaction::where('table_type', 'Income')
                ->whereIn('transaction_type', ['Current', 'Advance'])
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)->sum('at_amount');


            //Bank Asset Increment today
            $todayBankAssetIncrement = Transaction::where('table_type', 'Assets')
                ->where('transaction_type', 'Sold')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)->sum('at_amount');


            //Bank Liabilities Increment today
            $todayBankLiabilitiesIncrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Received')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)->sum('at_amount');


            //Bank Equity Increment today
            $todayBankEquityIncrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Received')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)->sum('at_amount');

            $PurchaseReturnBankIncrementToday = Transaction::where('table_type', 'Cogs')
                ->where('transaction_type', 'Return')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

            //Total Today Bank Increment
            $totalTodayBankIncrements = $todayBankIncomeIncrement + $todayBankAssetIncrement + $todayBankLiabilitiesIncrement + $todayBankEquityIncrement + $PurchaseReturnBankIncrementToday;

            //Cash Decrement

            //Cash Expense Decrement today
            $expenseCashDecrement = Transaction::where('table_type', 'Expenses')
                ->whereIn('transaction_type', ['Current','Prepaid'])
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');
            
            //Cash Asset Decrement today
            $assetCashDecrement = Transaction::where('table_type', 'Assets')
                ->whereIn('transaction_type', ['Payment', 'Purchase'])
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

            //Cash Liabilities Decrement today
            $liabilitiesCashDecrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

                

            //Cash Equity Decrement today
            $equityCashDecrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

                

            //Cash Income Decrement today
            $incomeCashDecrement = Transaction::where('table_type', 'Income')
                ->where('transaction_type', 'Refund')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

             $purchaseCashDecrement = Transaction::where('table_type', 'Cogs')
                ->where('status', 0)
                -> where('payment_type', 'Cash')
                ->where('transaction_type', 'Current')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

             $salesReturnCashDecrement = Transaction::where('table_type', 'Income')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('transaction_type', 'Return')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

            //Total Today Cash Decrement
            $totalTodayCashDecrements = $expenseCashDecrement + $assetCashDecrement + $liabilitiesCashDecrement + $equityCashDecrement + $incomeCashDecrement + $purchaseCashDecrement + $salesReturnCashDecrement;
            
            //Bank Decrement

            //Bank Expense Decrement today
            $todayExpenseBankDecrement = Transaction::where('table_type', 'Expenses')
                ->whereIn('transaction_type', ['Current','Prepaid'])
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)->sum('at_amount');


            //Bank Asset Decrement today
            $todayAssetBankDecrement = Transaction::where('table_type', 'Assets')
                ->whereIn('transaction_type', ['Payment', 'Purchase'])
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)->sum('at_amount');


            //Bank Liabilities Decrement today
            $todayLiabilitiesBankDecrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)->sum('at_amount');


            //Bank Equity Decrement today
            $todayEquityBankDecrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)->sum('at_amount');


            //Bank Income Decrement today
            $todayIncomeBankDecrement = Transaction::where('table_type', 'Income')
                ->where('transaction_type', 'Refund')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)->sum('at_amount');

            $purchaseBankDecrement = Transaction::where('table_type', 'Cogs')
                ->where('status', 0)
                -> where('payment_type', 'Bank')
                ->where('transaction_type', 'Current')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');
                
            $salesReturnBankDecrement = Transaction::where('table_type', 'Income')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('transaction_type', 'Return')
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');


            //Total Today Bank Decrement
            $totalTodayBankDecrements = $todayExpenseBankDecrement + $todayAssetBankDecrement + $todayLiabilitiesBankDecrement + $todayEquityBankDecrement + $todayIncomeBankDecrement + $purchaseBankDecrement + $salesReturnBankDecrement;

            //Cash in Hand and Bank
            $cashInHand = $totalTodayCashIncrements - $totalTodayCashDecrements;
            $cashInBank = $totalTodayBankIncrements - $totalTodayBankDecrements;
            

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

            $yestCashAssetIncrement = Transaction::where('table_type', 'Assets')
                ->whereIn('transaction_type', ['Received', 'Sold'])
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

            $yestpurchaseReturnCashIncrement = Transaction::where('table_type', 'Cogs')
                ->where('transaction_type', 'Return')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('date', '<=', $yest)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

        $totalYestCashIncrement = $yestCashIncomeIncrement  + $yestCashAssetIncrement + $yestCashLiabilitiesIncrement + $yestCashEquityIncrement + $yestpurchaseReturnCashIncrement;

                // dd($totalYestCashIncrement);
            //Till Yesterday Cash Decrement

            $yestExpenseCashDecrement = Transaction::where('table_type', 'Expenses')
                ->whereIn('transaction_type', ['Current','Prepaid'])
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

            $yestPurchaseCashDecrement = Transaction::where('table_type', 'Cogs')
                ->where('transaction_type', 'Current')
                ->where('status', 0)
                ->where('payment_type', 'Cash')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');

                // dd($yestPurchaseCashDecrement);

        $totalYestCashDecrement = $yestExpenseCashDecrement + $yestAssetCashDecrement + $yestLiabilitiesCashDecrement + $yestEquityCashDecrement + $yestIncomeCashDecrement + $yestPurchaseCashDecrement;

            //Till Today Bank Increment

            $yestAssetBankIncrement = Transaction::where('table_type', 'Assets')
                ->where('transaction_type', 'Sold')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', '<=', $yest)
                ->sum('at_amount');

            $yestLiabilitiesBankIncrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Received')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', '<=', $yest)
                ->sum('at_amount');

            $yestEquityBankIncrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Received')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', '<=', $yest)
                ->sum('at_amount');

            $yestIncomeBankIncrement = Transaction::where('table_type', 'Income')
                ->whereIn('transaction_type', ['Current', 'Advance'])
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date', '<=', $yest)
                ->sum('at_amount');
                // dd($yestIncomeBankIncrement);

            $yestpurchaseReturnCashIncrement = Transaction::where('table_type', 'Cogs')
                ->where('transaction_type', 'Return')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('date', '<=', $yest)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');
        
        
        $totalYestBankIncrement = $yestAssetBankIncrement + $yestLiabilitiesBankIncrement + $yestEquityBankIncrement + $yestIncomeBankIncrement + $yestpurchaseReturnCashIncrement;


            //Till Yesterday Bank Decrement

            $yestExpenseBankDecrement = Transaction::where('table_type', 'Expenses')
                ->whereIn('transaction_type', ['Current','Prepaid'])
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');

            $yestAssetBankDecrement = Transaction::where('table_type', 'Assets')
                ->whereIn('transaction_type', ['Payment', 'Purchase'])
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');

            $yestLiabilitiesBankDecrement = Transaction::where('table_type', 'Liabilities')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');

            $yestEquityBankDecrement = Transaction::where('table_type', 'Equity')
                ->where('transaction_type', 'Payment')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');

            $yestIncomeBankDecrement = Transaction::where('table_type', 'Income')
                ->where('transaction_type', 'Refund')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');

            $yestPurchaseBankDecrement = Transaction::where('table_type', 'Cogs')
                ->where('transaction_type', 'Current')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');

            $yestSalesRetunBankDecrement = Transaction::where('table_type', 'Income')
                ->where('transaction_type', 'Return')
                ->where('status', 0)
                ->where('payment_type', 'Bank')
                ->where('branch_id', auth()->user()->branch_id)
                ->where('date','<=', $yest)
                ->sum('at_amount');

        $totalYestBankDecrement = $yestExpenseBankDecrement + $yestAssetBankDecrement + $yestLiabilitiesBankDecrement + $yestEquityBankDecrement + $yestIncomeBankDecrement + $yestPurchaseBankDecrement + $yestSalesRetunBankDecrement;  

        $yesCashInHand = $totalYestCashIncrement - $totalYestCashDecrement;
        $yesBankInHand = $totalYestBankIncrement - $totalYestBankDecrement;
            // dd($totalYestCashIncrement);
            // $cashInHand = $totalTodayCashIncrements - $totalTodayCashDecrements;
        return view('admin.balance_sheet.index', compact('currentAssetIds', 'currentBankAsset', 'currentCashAsset', 'currentLiability', 'longTermLiabilities', 'equityCapital', 'retainedEarning','currentAssets', 'fixedAssets', 'fixedAsset', 'shortTermLiabilities', 'currentLiabilities', 'equityCapitals', 'retainedEarnings', 'cashInHand', 'cashInBank', 'inventory', 'netProfit', 'yesCashInHand', 'yesBankInHand', 'yesAccountReceiveable', 'yesInventory', 'netProfitTillYesterday','totalTodayCashIncrements','totalTodayCashDecrements', 'totalTodayBankIncrements', 'totalTodayBankDecrements', 'todaysAccountReceivableDebit','todaysAssetSoldAR', 'yesAccountPayable', 'totalTodaysAccountPayableCredit', 'todaysAccountPayableDebit', 'todaysProductCreditSold','todaysDueAccountPayableDebit', 'todaysCreditPurchaseAP', 'totalTodaysAccountReceivableCredit'));
    }

    public function calculateNetProfit(Request $request)
    {
        $yesterday = Carbon::yesterday();
        $today = date('Y-m-d');
        $branchId = auth()->user()->branch_id;

        // Sales sum today
        $salesSumToday = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->whereNot('transaction_type','Return')
            ->where('branch_id', $branchId)
            ->whereDate('date', $today)
            ->sum('amount');

        // Sales Return
        $salesReturnToday = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('transaction_type', 'Return')
            ->whereDate('date', $today)
            ->where('branch_id', $branchId)
            ->sum('amount');

        // Sales Discount
        $salesDiscount = Order::where('branch_id', $branchId)
            ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                $query->whereBetween('orderdate', [$request->input('startDate'), $request->input('endDate')]);
            })
            ->sum('discount_amount');

        // Purchase sum (Cost of Goods Sold) Today
        $purchaseSumToday = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereDate('date', $today)
            ->sum('amount');

        // Operating Income today
        $operatingIncomeSumToday = Transaction::where('table_type', 'Income')
                ->where('status', 0)
                ->whereNotNull('chart_of_account_id')
                ->where('branch_id', $branchId)
                ->whereIn('transaction_type', ['Current', 'Advance'])
                ->whereDate('date', $today)
                ->sum('amount');

        $operatingIncomeRefundToday = Transaction::where('table_type', 'Income')
                ->where('status', 0)
                ->whereNotNull('chart_of_account_id')
                ->where('branch_id', $branchId)
                ->whereIn('transaction_type', ['Refund'])
                ->whereDate('date', $today)
                ->sum('amount');

        //Purchase return today
        $purchaseReturnToday = Transaction::where('table_type', 'Cogs')
                ->where('transaction_type', 'Return')
                ->where('status', 0)
                ->whereDate('date', $today)
                ->where('branch_id', auth()->user()->branch_id)
                ->sum('at_amount');

        // Operating Expenses today
        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');
        $operatingExpenseSumToday = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->whereDate('date', $today)
            ->sum('amount');

        // OverHead Expenses today
        $overHeadExpenseId = ChartOfAccount::where('sub_account_head', 'Overhead Expense')->pluck('id');
        $overHeadExpenseSumToday = Transaction::whereIn('chart_of_account_id', $overHeadExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->whereDate('date', $today)
            ->sum('amount');

        // Administrative Expenses
        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');
        $administrativeExpenseSumToday = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->whereDate('date', $today)
            ->sum('amount');

        //  //Fixed Assets   
        $FixedAssetId = ChartOfAccount::where('sub_account_head', 'Fixed Asset')->where('account_head', 'Assets')->pluck('id');

        // //Fixed Asset sold depriciation Today
        $FixedAssetDepriciationToday = Transaction::whereIn('chart_of_account_id', $FixedAssetId)
            ->where('status', 0)
            ->where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Depreciation'])
            ->where('branch_id', $branchId)
            ->whereDate('date', $today)
            ->sum('amount');

        // VAT Calculations
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

        // Net Profit Calculation
        $taxAndVat = $purchaseVatSum + $salesVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum;
        $netSalesToday = $salesSumToday - $salesReturnToday - $salesDiscount;
        $grossProfit = $netSalesToday + $purchaseReturnToday - $purchaseSumToday ;
        $profitBeforeTax = $grossProfit + $operatingIncomeSumToday - $operatingIncomeRefundToday - $operatingExpenseSumToday - $administrativeExpenseSumToday - $overHeadExpenseSumToday - $FixedAssetDepriciationToday;
        $netProfit = $profitBeforeTax - $taxAndVat;
        return $netProfit;

    }

    public function calculateNetProfitTillYesterday($yest)
    {
        $branchId = auth()->user()->branch_id;

        // Operating Income
        $yesOperatingIncome = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNotNull('chart_of_account_id')
            ->whereIn('transaction_type', ['Current','Advance'])
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yest)
            ->sum('amount');

        // Operating Income Refund
        $yesOperatingIncomeRefund = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNotNull('chart_of_account_id')
            ->where('transaction_type', 'Refund')
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yest)
            ->sum('amount');

        // Sales sum
        $salesSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->whereNot('transaction_type', 'Return')
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yest)
            ->sum('amount');

        //Previous Sales Return
        $salesReturn = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('transaction_type', 'Return')
            ->whereDate('date', '<=', $yest)
            ->where('branch_id', $branchId)
            ->sum('amount');

        // Sales Discount
        $salesDiscount = Order::where('branch_id', $branchId)
            ->whereDate('orderdate', '<=', $yest)
            ->sum('discount_amount');

        // Purchase sum (Cost of Goods Sold)
        $purchaseSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereDate('date', '<=', $yest)
            ->sum('amount');

        //Previous Purchase Return
        $previousPurchaseReturn = Transaction::where('table_type', 'Cogs')
            ->where('transaction_type', 'Return')
            ->where('status', 0)
            ->whereDate('date', '<=', $yest)
            ->where('branch_id', $branchId)
            ->sum('at_amount');

        // Operating Expenses
        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');
        $operatingExpenseSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yest)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->sum('amount');

        // Overhead Expenses
        $overheadExpenseId = ChartOfAccount::where('sub_account_head', 'Overhead Expense')->pluck('id');
        $overheadExpenseSum = Transaction::whereIn('chart_of_account_id', $overheadExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yest)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->sum('amount');

        // Administrative Expenses
        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');
        $administrativeExpenseSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yest)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->sum('amount');

        //Fixed Assets   
        $FixedAssetId = ChartOfAccount::where('sub_account_head', 'Fixed Asset')->where('account_head', 'Assets')->pluck('id');

        // //Fixed Asset sold depriciation previous
        $FixedAssetDepriciation = Transaction::whereIn('chart_of_account_id', $FixedAssetId)
            ->where('status', 0)
            ->where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Depreciation'])
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yest)
            ->sum('amount');

        // VAT Calculations
        $purchaseVatSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereDate('date', '<=', $yest)
            ->sum('vat_amount');

        $salesVatSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yest)
            ->sum('vat_amount');

        $operatingExpenseVatSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yest)
            ->sum('vat_amount');

        $administrativeExpenseVatSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereDate('date', '<=', $yest)
            ->sum('vat_amount');

        // Net Profit Calculation
        $taxAndVat = $purchaseVatSum + $salesVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum;
        $netSales = $salesSum + $previousPurchaseReturn - $salesReturn - $salesDiscount + $yesOperatingIncome - $yesOperatingIncomeRefund - $FixedAssetDepriciation;

        $grossProfit = $netSales - $purchaseSum;
        $profitBeforeTax = $grossProfit - $operatingExpenseSum - $administrativeExpenseSum - $overheadExpenseSum;
        $netProfitTillYesterday = $profitBeforeTax - $taxAndVat;
        
        return $netProfitTillYesterday;
    }

}
