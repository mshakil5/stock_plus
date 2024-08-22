<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class IncomestatementController extends Controller
{
    public function incomeStatement(Request $request)
    {
        $incomes = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        $expenses = Transaction::whereIn('table_type', ['Expenses', 'Cogs'])
            ->whereIn('transaction_type', ['Current', 'Prepaid'])
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        return view('admin.income_statement.index', compact('incomes', 'expenses'));
    }

    public function incomeStatementByDate(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $branchId = auth()->user()->branch_id;

        $incomes = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->get();

        $expenses = Transaction::where('table_type', 'Expenses')
            ->whereIn('transaction_type', ['Current', 'Prepaid'])
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->get();

        return view('admin.income_statement.index', compact('incomes', 'expenses'))
            ->with('start_date', $startDate)
            ->with('end_date', $endDate);
    }

}
