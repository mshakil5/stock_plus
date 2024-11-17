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
        $chartOfAccounts = ChartOfAccount::with('branch')
        ->where('branch_id', auth()->user()->branch_id)
        ->select('id', 'account_head', 'account_name')
        ->get();
        return view('admin.ledger.chart_of_accounts', compact('chartOfAccounts'));
    }

    public function asset($id, Request $request)
    {
        $assets = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        $totalDrAmount = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Purchase', 'Payment'])
            ->sum('at_amount');

        $totalCrAmount = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Sold', 'Deprication'])
            ->sum('at_amount');

        $totalAsset = $totalDrAmount - $totalCrAmount;

        return view('admin.ledger.asset', compact('assets', 'totalAsset'));
    }

    public function expense($id, Request $request)
    {
        $assets = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due Adjust'])
            ->get();

        $totalDrAmount = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due Adjust'])
            ->sum('at_amount');

        $totalAsset = $totalDrAmount;
        return view('admin.ledger.expense', compact('assets', 'totalAsset'));
    }

    public function income($id, Request $request)
    {
        $assets = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Current', 'Advance Adjust', 'Refund'])
            ->get();

        $totalDrAmount = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Refund'])
            ->sum('at_amount');

        $totalCrAmount = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Current', 'Advance Adjust'])
            ->sum('at_amount');

        $totalAsset = $totalDrAmount - $totalCrAmount;

        return view('admin.ledger.income', compact('assets', 'totalAsset'));
    }

    public function liability($id, Request $request)
    {
        $assets = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        $totalDrAmount = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Payment'])
            ->sum('at_amount');

        $totalCrAmount = Transaction::where('chart_of_account_id', $id)
            ->whereIn('transaction_type', ['Received'])
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');

        $totalAsset = $totalDrAmount - $totalCrAmount;

        return view('admin.ledger.liability', compact('assets', 'totalAsset'));
    }

    public function equity($id, Request $request)
    {
        $assets = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        $totalDrAmount = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Payment'])
            ->sum('at_amount');

        $totalCrAmount = Transaction::where('chart_of_account_id', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('transaction_type', ['Received'])
            ->sum('at_amount');

        $totalAsset = $totalDrAmount - $totalCrAmount;
        
        return view('admin.ledger.equity', compact('assets', 'totalAsset'));
    }
}
