<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class CashflowController extends Controller
{
    public function cashFlow(Request $request)
    {
        $incomings = Transaction::where(function ($query) {
            $query->where('table_type', 'Assets')
                  ->where('transaction_type', 'Sold');
        })->orWhere(function ($query) {
            $query->where('table_type', 'Income')
                  ->whereIn('transaction_type', ['Current', 'Advance']);
        })->orWhere(function ($query) {
            $query->where('table_type', 'Liabilities')
                  ->where('transaction_type', 'Received');
        })->orWhere(function ($query) {
            $query->where('table_type', 'Equity')
                  ->where('transaction_type', 'Received');
        })->get();

        // cash incoming query start
        $incomes = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        $assetSold = Transaction::where('table_type', 'Assets')
            ->where('transaction_type', 'Sold')
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->sum('at_amount');

        $liabilityReceived = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->sum('at_amount');

        $equityReceived = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->sum('at_amount');

        $expenses = Transaction::whereIn('table_type', ['Expenses', 'Cogs'])
            ->whereIn('transaction_type', ['Current', 'Prepaid'])
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        $assetPurchase = Transaction::where('table_type', 'Assets')
            ->where('transaction_type', 'Purchase')
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->sum('at_amount');

        $liabilityPayment = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->sum('at_amount');

        $equityPayment = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->sum('at_amount');

            // dd($assetPurchase);

        return view('admin.cashflow.index', compact('incomes', 'assetSold', 'liabilityReceived', 'equityReceived','expenses','assetPurchase', 'liabilityPayment', 'equityPayment'));
    }

    public function cashFlowByDate(Request $request)
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

        $assetSold = Transaction::where('table_type', 'Assets')
            ->where('transaction_type', 'Sold')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $liabilityReceived = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $equityReceived = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

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

        $assetPurchase = Transaction::where('table_type', 'Assets')
            ->where('transaction_type', 'Purchase')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $liabilityPayment = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $equityPayment = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        return view('admin.cashflow.index', compact('incomes', 'assetSold', 'liabilityReceived', 'equityReceived','expenses','assetPurchase', 'liabilityPayment', 'equityPayment'));
    }

}
