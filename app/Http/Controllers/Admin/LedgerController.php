<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\Transaction;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $chartOfAccounts = ChartOfAccount::select('id', 'account_head', 'account_name')
        ->get();
        return view('admin.ledger.chart_of_accounts', compact('chartOfAccounts'));
    }

    public function asset($id, Request $request)
    {
        $assets = Transaction::where('chart_of_account_id', $id)->get();
        $totalAsset = Transaction::where('chart_of_account_id', $id)->sum('at_amount');
        return view('admin.ledger.asset', compact('assets', 'totalAsset'));
    }

    public function expense($id, Request $request)
    {
        $assets = Transaction::where('chart_of_account_id', $id)->get();
        $totalAsset = Transaction::where('chart_of_account_id', $id)->sum('at_amount');
        return view('admin.ledger.expense', compact('assets', 'totalAsset'));
    }

    public function income($id, Request $request)
    {
        $assets = Transaction::where('chart_of_account_id', $id)->get();
        $totalAsset = Transaction::where('chart_of_account_id', $id)->sum('at_amount');
        return view('admin.ledger.income', compact('assets', 'totalAsset'));
    }

    public function liability($id, Request $request)
    {
        $assets = Transaction::where('chart_of_account_id', $id)->get();
        $totalAsset = Transaction::where('chart_of_account_id', $id)->sum('at_amount');
        return view('admin.ledger.liability', compact('assets', 'totalAsset'));
    }

    public function equity($id, Request $request)
    {
        $assets = Transaction::where('chart_of_account_id', $id)->get();
        $totalAsset = Transaction::where('chart_of_account_id', $id)->sum('at_amount');
        return view('admin.ledger.equity', compact('assets', 'totalAsset'));
    }
}
