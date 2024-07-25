<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class DaybookController extends Controller
{
    public function cashbook()
    {
        $cashbooks = Transaction::where('payment_type', 'Cash')
        ->select('id', 'date', 'description', 'ref', 'chart_of_account_id', 'transaction_type', 'at_amount')->orderBy('id', 'desc')->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance', 'Purchase', 'Payment', 'Prepaid'])
        ->get();
        $totalDrAmount = Transaction::where('payment_type', 'Cash')->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance'])->sum('at_amount');
        $totalCrAmount = Transaction::where('payment_type', 'Cash')->whereIn('transaction_type', ['Purchase', 'Payment', 'Prepaid'])->sum('at_amount');
        $totalAmount = $totalDrAmount - $totalCrAmount;
        return view('admin.daybook.cashbook', compact('cashbooks', 'totalAmount'));
    }

    public function cashbookByDate(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $cashbooks = Transaction::where('payment_type', 'Cash')
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
        ->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance'])
        ->when($startDate, function($query, $startDate) {
            return $query->whereDate('date', '>=', $startDate);
        })
        ->when($endDate, function($query, $endDate) {
            return $query->whereDate('date', '<=', $endDate);
        })
        ->sum('at_amount');

        $totalCrAmount = Transaction::where('payment_type', 'Cash')
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

    public function bankbook()
    {
        $bankbooks = Transaction::where('payment_type', 'Bank')
        ->select('id', 'date', 'description', 'ref', 'chart_of_account_id', 'transaction_type', 'at_amount')->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance', 'Purchase', 'Payment', 'Prepaid'])
        ->get();
        $totalDrAmount = Transaction::where('payment_type', 'Bank')->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance'])->sum('at_amount');
        $totalCrAmount = Transaction::where('payment_type', 'Bank')->whereIn('transaction_type', ['Purchase', 'Payment', 'Prepaid'])->sum('at_amount');
        $totalAmount = $totalDrAmount - $totalCrAmount;
        return view('admin.daybook.bankbook', compact('bankbooks', 'totalAmount'));
    }

    public function bankbookByDate(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $bankbooks = Transaction::where('payment_type', 'Bank')
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
            ->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $totalCrAmount = Transaction::where('payment_type', 'Bank')
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
