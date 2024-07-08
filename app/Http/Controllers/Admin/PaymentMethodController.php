<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class PaymentMethodController extends Controller
{
    public function get_all_method()
    {
      $method = PaymentMethod::all();
      return Response::json($method);

    }


    public function save_method(Request $request)
    {
      $method = new PaymentMethod();
      $method->name = $request->name;
      $method->created_by = Auth::user()->id;
      $method->save();
      return;
    }

    public function view_payment_method(Request $request)
    {
        
        if($request->ajax()){
            $method = PaymentMethod::all();
            return Datatables::of($method)->make(true);
        }
        return view("admin.payment.paymentmethod");
    }

    public function published_method($ID) {

      PaymentMethod::where('id', $ID)
      ->update(['status' => 1]);

      return ;
    }


    public function unpublished_method($ID) {
      PaymentMethod::where('id', $ID)
        ->update(['status' => 0]);

        return ;
    }

    public function edit_method(Request $request, $id)
    {
      $method = PaymentMethod::where('id',$id)
              ->update(['name' =>$request['data']['name']]);

      return;
    }

  

    public function getpaymentmethod()
    {
        $paymentDtl = PaymentMethod::where('status', '=', 1)->get();
        return Response::json($paymentDtl);
    }
}
