<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    // public function index()
    // {
        
    //     return view('admin.customer.index');
    // }

    public function index(Request $request)
    {
        if($request->ajax()){
            $customers = Customer::where('branch_id', auth()->user()->branch_id)
               ->orderBy('id', 'desc')
               ->get();
            return Datatables::of($customers)->make(true);
        }
        return view('admin.customer.index');
    }

    public function store(Request $request)
    {

        if (empty($request->name)) {
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Name Field Is Required..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
  
          }

    
        $request->validate([
            'name' => 'required',
        ]);
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->branch_id = auth()->user()->branch_id;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;
        $customer->vehicleno = $request->vehicleno;
        $customer->limitation = $request->limitation;
        $customer->vat_number = $request->vat_number;
        $customer->member_id = $request->member_id;
        $customer->type = $request->type;
        $customer->save();

        return response()->json([
            'status' => 201,
            'message' => 'Customer created successfully.',
            'data' => $customer
        ], 201);


    }

    public function edit($id)
    {
        $customerDtl = Customer::where('id', '=', $id)->first();
        if(empty($customerDtl)){
            return response()->json(['status'=> 303,'message'=>"No data found"]);
        }else{
            return response()->json(['status'=> 300,'customername'=>$customerDtl->name,'member_id'=>$customerDtl->member_id,'id'=>$customerDtl->id,'address'=>$customerDtl->address,'vehicleno'=>$customerDtl->vehicleno,'email'=>$customerDtl->email,'phone'=>$customerDtl->phone,'limitation'=>$customerDtl->limitation,'type'=>$customerDtl->type,'vat_number'=>$customerDtl->vat_number]);
        }
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        $request->validate([
            'name' => 'required',
        ]);
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;
        $customer->vehicleno = $request->vehicleno;
        $customer->limitation = $request->limitation;
        $customer->vat_number = $request->vat_number;
        $customer->member_id = $request->member_id;
        $customer->type = $request->type;
        $customer->save();
        return response()->json([
            'status' => 200,
            'message' => 'Customer updated successfully.',
            'data' => $customer
        ], 200);
    }

    public function changeStatus($id)
    {
        $customer = Customer::find($id);
        if($customer->status){
            $customer->status = 0;
        }else{
            $customer->status=1;
        }
        $customer->save();
        return $customer;
    }

    public function activeCustomer(){
        $customers = Customer::where('status',1)->get();
        return $customers;
    }

}
