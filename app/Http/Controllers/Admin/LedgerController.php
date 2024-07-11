<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ChartOfAccount;
use App\Models\Transaction;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $chartOfAccounts = ChartOfAccount::get();
            return DataTables::of($chartOfAccounts)
                ->make(true);
        }
        return view('admin.ledger.chart_of_accounts');
    }

    public function show($id, Request $request)
    {
        if ($request->ajax()) {
            $totalAmount = Transaction::where('chart_of_account_id', $id)->sum('amount');
            $transactions = Transaction::where('chart_of_account_id', $id)->select('id', 'chart_of_account_id', 'date', 'amount', 'at_amount', 'description', 'expense_id', 'payment_type', 'ref')->get();
            return DataTables::of($transactions)
                ->editColumn('date', function ($transaction) {
                    return date('d-m-Y', strtotime($transaction->date));
                })
                ->editColumn('debit', function ($transaction) {
                    return $transaction->at_amount;
                })
                ->editColumn('credit', function ($transaction) {
                    return $transaction->at_amount;
                })
                ->editColumn('balance', function ($transaction) {
                    return $transaction->where('chart_of_account_id', $transaction->chart_of_account_id)->sum('at_amount');
                })
                ->make(true);
        }

        $account = ChartOfAccount::find($id);
        return view('admin.ledger.details', compact('account'));
    }
}
