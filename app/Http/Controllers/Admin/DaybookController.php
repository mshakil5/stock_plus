<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class DaybookController extends Controller
{
    public function cashBook(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $branchId = auth()->user()->branch_id;

        $cashbooks = Transaction::where('payment_type', 'Cash')
        ->where('branch_id', $branchId)
        ->select('id', 'date', 'description', 'ref', 'chart_of_account_id', 'transaction_type', 'at_amount')
        ->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance', 'Purchase', 'Payment', 'Prepaid'])
        ->when($startDate, function($query, $startDate) {
            return $query->whereDate('date', '>=', $startDate);
        })
        ->when($endDate, function($query, $endDate) {
            return $query->whereDate('date', '<=', $endDate);
        })
        ->orderBy('id', 'desc')
        ->get();
        
        $totalDrAmount = Transaction::where('payment_type', 'Cash')
        ->where('branch_id', $branchId)
        ->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance'])
        ->when($startDate, function($query, $startDate) {
            return $query->whereDate('date', '>=', $startDate);
        })
        ->when($endDate, function($query, $endDate) {
            return $query->whereDate('date', '<=', $endDate);
        })
        ->sum('at_amount');

        $totalCrAmount = Transaction::where('payment_type', 'Cash')
        ->where('branch_id', $branchId)
        ->whereIn('transaction_type', ['Purchase', 'Payment', 'Prepaid'])
        ->when($startDate, function($query, $startDate) {
            return $query->whereDate('date', '>=', $startDate);
        })
        ->when($endDate, function($query, $endDate) {
            return $query->whereDate('date', '<=', $endDate);
        })
        ->sum('at_amount');

        $totalAmount = $totalDrAmount - $totalCrAmount;
        return view('admin.daybook.cashbook', compact('cashbooks', 'totalAmount'));
    }

    public function bankBook(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $branchId = auth()->user()->branch_id;

        $bankbooks = Transaction::where('payment_type', 'Bank')
            ->where('branch_id', $branchId)
            ->select('id', 'date', 'description', 'ref', 'chart_of_account_id', 'transaction_type', 'at_amount')
            ->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance', 'Purchase', 'Payment', 'Prepaid'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->orderBy('id', 'desc')
            ->get();

        $totalDrAmount = Transaction::where('payment_type', 'Bank')
            ->where('branch_id', $branchId)
            ->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $totalCrAmount = Transaction::where('payment_type', 'Bank')
            ->where('branch_id', $branchId)
            ->whereIn('transaction_type', ['Purchase', 'Payment', 'Prepaid'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $totalAmount = $totalDrAmount - $totalCrAmount;

        return view('admin.daybook.bankbook', compact('bankbooks', 'totalAmount'));
    }
}
