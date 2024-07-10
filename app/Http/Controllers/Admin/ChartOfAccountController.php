<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\ChartOfAccount;
use Illuminate\Support\Carbon;


class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $chartOfAccounts = ChartOfAccount::with('branch')->get();
            return DataTables::of($chartOfAccounts)
                ->addColumn('branch_name', function($account) {
                    return $account->branch ? $account->branch->name : 'N/A';
                })
                ->make(true);
        }
        return view('admin.chart_of_accounts.index');
    }

    public function store(Request $request)
    {
        if (empty($request->account_name)) {
            return response()->json(['status' => 303, 'message' => 'Name Field Is Required..!']);
        }
        if (empty($request->account_head)) {
            return response()->json(['status' => 303, 'message' => 'Account Head Field Is Required..!']);
        }
        if (empty($request->sub_account_head)) {
            return response()->json(['status' => 303, 'message' => 'Sub Account Field Is Required..!']);
        }

        $existingAccount = ChartOfAccount::where('account_name', $request->account_name)
                                     ->where('branch_id', Auth::user()->branch_id)
                                     ->first();
    
        if ($existingAccount) {
            return response()->json(['status' => 303, 'message' => 'Account Name already exists for this branch..!']);
        }

        $chartOfAccount = new ChartOfAccount();
        $chartOfAccount->account_head = $request->account_head;
        $chartOfAccount->sub_account_head = $request->sub_account_head;
        $chartOfAccount->date = Carbon::now()->format('d-m-Y');
        $chartOfAccount->account_name = $request->account_name;
        $chartOfAccount->description = $request->description;
        $chartOfAccount->status = 1;
        $chartOfAccount->branch_id = Auth::user()->branch_id;
        $chartOfAccount->created_by = Auth::user()->id;
        $chartOfAccount->save();

        return response()->json(['status' => 200, 'message' => 'Created Successfully']);
    }

    public function edit($id)
    {
        $chartDtl = ChartOfAccount::where('id', '=', $id)->first();
        if(empty($chartDtl)){
            return response()->json(['status'=> 303,'message'=>"No data found"]);
        }else{
            return response()->json(['status'=> 300,'account_head'=>$chartDtl->account_head,'sub_account_head'=>$chartDtl->sub_account_head,'id'=>$chartDtl->id,'account_name'=>$chartDtl->account_name,'description'=>$chartDtl->description]);
        }
    }

    public function update(Request $request, $id)
    {
        if (empty($request->account_name)) {
            return response()->json(['status' => 303, 'message' => 'Name Field Is Required..!']);
        }
        if (empty($request->account_head)) {
            return response()->json(['status' => 303, 'message' => 'Account Head Field Is Required..!']);
        }
        if (empty($request->sub_account_head)) {
            return response()->json(['status' => 303, 'message' => 'Sub Account Field Is Required..!']);
        }

        $chartOfAccount = ChartOfAccount::find($id);

        $chartOfAccount->account_head = $request->account_head;
        $chartOfAccount->sub_account_head = $request->sub_account_head;
        $chartOfAccount->account_name = $request->account_name;
        $chartOfAccount->description = $request->description;
        $chartOfAccount->updated_by = Auth::user()->id;
        $chartOfAccount->save();

        return response()->json(['status' => 200, 'message' => 'Updated Successfully']);
    }

    public function changeStatus($id)
    {
        $chartOfAccount = ChartOfAccount::find($id);
        if($chartOfAccount->status){
            $chartOfAccount->status = 0;
        }else{
            $chartOfAccount->status=1;
        }
        $chartOfAccount->save();
        return $chartOfAccount;
    }
}
