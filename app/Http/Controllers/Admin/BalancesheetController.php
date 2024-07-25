<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\Transaction;

class BalancesheetController extends Controller
{
    public function balanceSheet(Request $request)
    {
        $fixedAssetIds = ChartOfAccount::where('sub_account_head', 'Fixed Asset')
            ->pluck('id');
        $fixedAssets = Transaction::whereIn('chart_of_account_id', $fixedAssetIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        $currentAssetIds = ChartOfAccount::where('sub_account_head', 'Current Asset')
            ->pluck('id');
        $currentBankAsset = Transaction::whereIn('chart_of_account_id', $currentAssetIds)
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');
        $currentCashAsset = Transaction::whereIn('chart_of_account_id', $currentAssetIds)
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');

        $accountReceiveableIds = ChartOfAccount::where('sub_account_head', 'Account Receivable')
            ->pluck('id');
        $accountReceiveable = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
            ->where('status', 0)
            ->where('branch_id', auth()->user()->branch_id)
            ->sum('at_amount');

        return view('admin.balance_sheet.index', compact('fixedAssets', 'currentBankAsset', 'currentCashAsset', 'accountReceiveable'));
    }

    public function balanceSheetByDate(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $branchId = auth()->user()->branch_id;

        $fixedAssetIds = ChartOfAccount::where('sub_account_head', 'Fixed Asset')->pluck('id');
        $fixedAssets = Transaction::whereIn('chart_of_account_id', $fixedAssetIds)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->get();

        $currentAssetIds = ChartOfAccount::where('sub_account_head', 'Current Asset')->pluck('id');
        $currentBankAsset = Transaction::whereIn('chart_of_account_id', $currentAssetIds)
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('branch_id', $branchId)
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $currentCashAsset = Transaction::whereIn('chart_of_account_id', $currentAssetIds)
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('branch_id', $branchId)
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $accountReceiveableIds = ChartOfAccount::where('sub_account_head', 'Account Receivable')->pluck('id');
        $accountReceiveable = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        return view('admin.balance_sheet.index', compact('fixedAssets', 'currentBankAsset', 'currentCashAsset', 'accountReceiveable'));
    }
}
