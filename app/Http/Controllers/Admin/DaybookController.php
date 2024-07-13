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
        ->select('id', 'date', 'description', 'ref', 'chart_of_account_id', 'transaction_type', 'at_amount')
        ->get();
        $totalAmount = Transaction::where('payment_type', 'Cash')->sum('at_amount');
        return view('admin.daybook.cashbook', compact('cashbooks', 'totalAmount'));
    }

    public function bankbook()
    {
        $bankbooks = Transaction::where('payment_type', 'Bank')
        ->select('id', 'date', 'description', 'ref', 'chart_of_account_id', 'transaction_type', 'at_amount')
        ->get();
        $totalAmount = Transaction::where('payment_type', 'Bank')->sum('at_amount');
        return view('admin.daybook.bankbook', compact('bankbooks', 'totalAmount'));
    }
}
