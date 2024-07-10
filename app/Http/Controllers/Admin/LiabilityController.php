<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LiabilityController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $transactions = Transaction::with('chartOfAccount')->where('table_type', 'Liabilities')->get();
            return DataTables::of($transactions)
                ->addColumn('chart_of_account', function ($transaction) {
                    return $transaction->chartOfAccount->account_name;
                })
                ->make(true);
        }
        return view('admin.transactions.liabilities');
    }
}
