<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\ChartOfAccount;
use App\Models\SalesReturn;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\Stock;
use Illuminate\Support\Carbon;
use App\Models\DailyStockLog;
use App\Models\PurchaseHistory;
use App\Models\PurchaseHistoryLog;

class IncomestatementController extends Controller
{
    public function incomeStatement(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $branchId = auth()->user()->branch_id;

        $purchaseSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('amount');

        $salesSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->whereNot('transaction_type', 'Return')
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('amount');

        $operatingIncomes = Transaction::where('table_type', 'Income')
            ->with('chartOfAccount')
            ->where('status', 0)
            ->whereNotNull('chart_of_account_id')
            ->where('branch_id', $branchId)
            ->whereIn('transaction_type', ['Current','Advance'])
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->selectRaw('chart_of_account_id, SUM(amount) as total_amount')
            ->groupBy('chart_of_account_id')
            ->get();
        
        $operatingIncomeSums = $operatingIncomes->sum('total_amount');

        $operatingIncomeRefundSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNotNull('chart_of_account_id')
            ->where('transaction_type', 'Refund')
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('amount');
         
         //Operating Expense   
        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');
        $operatingExpenses = Transaction::select('chart_of_account_id', DB::raw('SUM(amount) as total_amount'))
            ->with('chartOfAccount')
            ->whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->groupBy('chart_of_account_id')
            ->get();
        $operatingExpenseSum = $operatingExpenses->sum('total_amount');
        // dd($operatingExpenseSum);

        //Overhead expense
        $overHeadExpenseId = ChartOfAccount::where('sub_account_head', 'Overhead Expense')->pluck('id');
        $overHeadExpenses = Transaction::select('chart_of_account_id', DB::raw('SUM(amount) as total_amount'))
            ->with('chartOfAccount')
            ->whereIn('chart_of_account_id', $overHeadExpenseId)
            ->where('status', 0)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->groupBy('chart_of_account_id')
            ->get();

        $overHeadExpenseSum = $overHeadExpenses->sum('total_amount');

        //Administrative Expense
        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');
        $administrativeExpenses = Transaction::select('chart_of_account_id', DB::raw('SUM(amount) as total_amount'))
            ->with('chartOfAccount')
            ->whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->groupBy('chart_of_account_id')
            ->get();
        $administrativeExpenseSum = $administrativeExpenses->sum('total_amount');

        //Fixed Asset Depreciation Expense
        $fixedAssetId = ChartOfAccount::where('sub_account_head', 'Fixed Asset')->where('account_head', 'Assets')->pluck('id');

        $fixedAssetDepriciation = Transaction::whereIn('chart_of_account_id', $fixedAssetId)
            ->where('status', 0)
            ->where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Depreciation'])
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('amount');

        $salesReturn = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('transaction_type', 'Return')
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('amount');

        $purchaseReturn = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('transaction_type', 'Return')
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('amount');

        $salesDiscount = Order::where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('discount_amount');

        $updatedStartDate = Carbon::parse($startDate)->format('Y-m-d');
        $updatedEndDate = Carbon::parse($endDate)->subDay()->format('Y-m-d');

        $totalOpeningStock = PurchaseHistoryLog::when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
            $query->whereBetween('log_date', [$request->input('start_date'), $request->input('end_date')]);
        })
        ->sum('total_amount');

        $closingBalances = PurchaseHistory::where('available_stock', '>', 0)
        ->get()
        ->groupBy('product_id')
        ->map(function ($purchaseHistories) {
            return $purchaseHistories->sum(function ($purchaseHistory) {
                return $purchaseHistory->available_stock * $purchaseHistory->purchase_price;
            });
        });

        $totalClosingStock = $closingBalances->sum();

        $purchaseVatSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('vat_amount');

        $salesVatSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('vat_amount');

        $operatingIncomeVatSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereNotNull('chart_of_account_id')
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('vat_amount');

        $operatingExpenseVatSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('vat_amount');

        $administrativeExpenseVatSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('vat_amount');

        $taxAndVat =  $salesVatSum + $operatingIncomeVatSum - ($purchaseVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum);

        return view('admin.income_statement.index', compact(
            'purchaseSum', 
            'salesSum', 
            'operatingExpenses', 
            'administrativeExpenses', 
            'salesReturn', 
            'salesDiscount', 
            'operatingExpenseSum', 
            'administrativeExpenseSum', 
            'totalOpeningStock', 
            'totalClosingStock', 
            'taxAndVat',
            'operatingIncomes',
            'operatingIncomeSums',
            'operatingIncomeRefundSum',
            'purchaseReturn',
            'overHeadExpenses',
            'overHeadExpenseSum',
            'fixedAssetDepriciation'
        ))->with('start_date', $startDate)->with('end_date', $endDate);
    }

}
