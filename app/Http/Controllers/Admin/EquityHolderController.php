<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquityHolder;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class EquityHolderController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $equityHolders = EquityHolder::with('branch')->latest()->get();
            return DataTables::of($equityHolders)
                ->addColumn('branch_name', function ($equityHolder) {
                    return $equityHolder->branch->name;
                })
                ->make(true);
        }
        return view('admin.equity_holders.index');
    }

    public function store(Request $request)
    {
        if (empty($request->name)) {
            return response()->json(['status' => 303, 'message' => 'Name Field Is Required..!']);
        }
        if (empty($request->company_name)) {
            return response()->json(['status' => 303, 'message' => 'Company Field Is Required..!']);
        }
        if (empty($request->phone)) {
            return response()->json(['status' => 303, 'message' => 'Phone Field Is Required..!']);
        }
        if (empty($request->tax_number)) {
            return response()->json(['status' => 303, 'message' => 'Tax Field Is Required..!']);
        }
        if (empty($request->tin)) {
            return response()->json(['status' => 303, 'message' => 'Tin Field Is Required..!']);
        }
        if (empty($request->address)) {
            return response()->json(['status' => 303, 'message' => 'Address Field Is Required..!']);
        }

        $equityHolder = new EquityHolder();
        $equityHolder->name = $request->name;
        $equityHolder->company_name = $request->company_name;
        $equityHolder->phone = $request->phone;
        $equityHolder->tax_number = $request->tax_number;
        $equityHolder->tin = $request->tin;
        $equityHolder->branch_id = $request->branch_id;
        $equityHolder->address = $request->address;
        $equityHolder->branch_id = Auth::user()->branch_id;
        $equityHolder->created_by = Auth::user()->id;
        $equityHolder->save();

        return response()->json(['status' => 200, 'message' => 'Created Successfully']);
    }

    public function edit($id)
    {
        $chartDtl = EquityHolder::where('id', '=', $id)->first();
        if(empty($chartDtl)){
            return response()->json(['status'=> 303,'message'=>"No data found"]);
        }else{
            return response()->json(['status'=> 300,
            'id'=>$chartDtl->id,
            'name'=>$chartDtl->name,'company_name'=>$chartDtl->company_name,
            'phone'=>$chartDtl->phone,
            'tax_number'=>$chartDtl->tax_number,
            'address'=>$chartDtl->address,
            'tin'=>$chartDtl->tin]);
        }
    }

    public function update(Request $request, $id)
    {
        if (empty($request->name)) {
            return response()->json(['status' => 303, 'message' => 'Name Field Is Required..!']);
        }
        if (empty($request->company_name)) {
            return response()->json(['status' => 303, 'message' => 'Company Field Is Required..!']);
        }
        if (empty($request->phone)) {
            return response()->json(['status' => 303, 'message' => 'Phone Field Is Required..!']);
        }
        if (empty($request->tax_number)) {
            return response()->json(['status' => 303, 'message' => 'Tax Field Is Required..!']);
        }
        if (empty($request->tin)) {
            return response()->json(['status' => 303, 'message' => 'Tin Field Is Required..!']);
        }
        if (empty($request->address)) {
            return response()->json(['status' => 303, 'message' => 'Address Field Is Required..!']);
        }

        $equityHolder = EquityHolder::find($id);
        $equityHolder->name = $request->name;
        $equityHolder->company_name = $request->company_name;
        $equityHolder->phone = $request->phone;
        $equityHolder->tax_number = $request->tax_number;
        $equityHolder->tin = $request->tin;
        $equityHolder->address = $request->address;
        $equityHolder->updated_by = Auth::user()->id;
        $equityHolder->save();

        return response()->json(['status' => 200, 'message' => 'Updated Successfully']);
    }
}
