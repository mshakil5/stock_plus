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
        // dd($operatingIncomeSums);

        $operatingIncomeRefundSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNotNull('chart_of_account_id')
            ->whereIn('transaction_type', ['Refund'])
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('amount');
            
        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');
        $operatingExpenses = Transaction::select('chart_of_account_id', DB::raw('SUM(amount) as total_amount'))
            ->with('chartOfAccount')
            ->whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->groupBy('chart_of_account_id')
            ->get();
        $operatingExpenseSum = $operatingExpenses->sum('total_amount');

        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');
        $administrativeExpenses = Transaction::select('chart_of_account_id', DB::raw('SUM(amount) as total_amount'))
            ->with('chartOfAccount')
            ->whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->groupBy('chart_of_account_id')
            ->get();
        $administrativeExpenseSum = $administrativeExpenses->sum('total_amount');

        $salesReturn = SalesReturn::where('branch_id', $branchId)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('net_total');

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

        $taxAndVat = $purchaseVatSum + $salesVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum + $operatingIncomeVatSum;

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
            'operatingIncomeRefundSum'
        ))->with('start_date', $startDate)->with('end_date', $endDate);
    }

}
