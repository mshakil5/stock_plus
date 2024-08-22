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

class IncomestatementController extends Controller
{
    public function incomeStatement(Request $request)
    {
        $purchaseSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', auth()->user()->branch_id)
            ->whereNull('chart_of_account_id')
            ->sum('amount');

        $salesSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->where('branch_id', auth()->user()->branch_id)
            ->whereNull('chart_of_account_id')
            ->sum('amount');

        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');

        $operatingExpenses = Transaction::select('chart_of_account_id', DB::raw('SUM(amount) as total_amount'))
            ->with('chartOfAccount')
            ->whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->groupBy('chart_of_account_id')
            ->get();

        $operatingExpenseSum = Transaction::with('chartOfAccount')->whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('amount');

        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');

        $administrativeExpenses = Transaction::with('chartOfAccount')
            ->select('chart_of_account_id', DB::raw('SUM(amount) as total_amount'))
            ->whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->groupBy('chart_of_account_id')
            ->get();

        $administrativeExpenseSum = Transaction::with('chartOfAccount')->whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('amount');

        $salesReturn = SalesReturn::where('branch_id', auth()->user()->branch_id)->sum('net_total');
        $salesDiscount = Order::where('branch_id', auth()->user()->branch_id)->sum('discount_amount');

        $openingStocks = Stock::select('product_id', DB::raw('SUM(purchase_price * quantity) as opening_stock'))
            ->groupBy('product_id')
            ->get();

        $totalOpeningStock = $openingStocks->sum('opening_stock');

        $totalClosingStock = $totalOpeningStock + $purchaseSum - ($salesSum - $salesReturn);

        $purchaseVatSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', auth()->user()->branch_id)
            ->whereNull('chart_of_account_id')
            ->sum('vat_amount');

        $SalesVatSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereNull('chart_of_account_id')
            ->sum('vat_amount');

        $operatingExpenseVatSum = Transaction::with('chartOfAccount')->whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('vat_amount');

        $administrativeExpenseVatSum = Transaction::with('chartOfAccount')->whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('amount');

        $taxAndVat = $purchaseVatSum + $SalesVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum;

        return view('admin.income_statement.index', compact('purchaseSum', 'salesSum', 'operatingExpenses', 'administrativeExpenses', 'salesReturn', 'salesDiscount', 'operatingExpenseSum', 'administrativeExpenseSum', 'totalOpeningStock', 'totalClosingStock', 'taxAndVat'));
    }

    public function incomeStatementByDate(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $branchId = auth()->user()->branch_id;

        $purchaseSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $salesSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');
        $operatingExpenses = Transaction::select('chart_of_account_id', DB::raw('SUM(amount) as total_amount'))
            ->with('chartOfAccount')
            ->whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('chart_of_account_id')
            ->get();
        $operatingExpenseSum = $operatingExpenses->sum('total_amount');

        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');
        $administrativeExpenses = Transaction::select('chart_of_account_id', DB::raw('SUM(amount) as total_amount'))
            ->with('chartOfAccount')
            ->whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('chart_of_account_id')
            ->get();
        $administrativeExpenseSum = $administrativeExpenses->sum('total_amount');

        $salesReturn = SalesReturn::where('branch_id', $branchId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('net_total');

        $salesDiscount = Order::where('branch_id', $branchId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('discount_amount');

        $stocks = Stock::select('product_id', 'purchase_price')->get();

        $totalOpeningStock = 0;
        $updatedStartDate = Carbon::parse($startDate)->format('Y-m-d');
        $updatedEndDate = Carbon::parse($endDate)->format('Y-m-d');

        foreach ($stocks as $stock) {
            $totalQuantity = DailyStockLog::where('product_id', $stock->product_id)
                ->whereBetween('log_date', [$updatedStartDate, $updatedEndDate])
                ->sum('quantity');
            $purchasePrice = $stock->purchase_price;
            $productTotalValue = $totalQuantity * $purchasePrice;
            $totalOpeningStock += $productTotalValue;
        }

        $totalClosingStock = $totalOpeningStock + $purchaseSum - ($salesSum - $salesReturn);

        $purchaseVatSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('vat_amount');

        $salesVatSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereNull('chart_of_account_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('vat_amount');

        $operatingExpenseVatSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('vat_amount');

        $administrativeExpenseVatSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('vat_amount');

        $taxAndVat = $purchaseVatSum + $salesVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum;

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
            'taxAndVat'
        ))->with('start_date', $startDate)->with('end_date', $endDate);
    }

}
