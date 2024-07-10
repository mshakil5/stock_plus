<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $transactions = Transaction::with('chartOfAccount')->where('table_type', 'Assets')->get();
            return DataTables::of($transactions)
                ->addColumn('chart_of_account', function ($transaction) {
                    return $transaction->chartOfAccount->account_name;
                })
                ->make(true);
        }
        return view('admin.transactions.assets');
    }
}
