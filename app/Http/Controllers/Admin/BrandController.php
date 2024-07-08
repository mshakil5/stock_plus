<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class BrandController extends Controller
{
    public function get_all_brand()
    {
      $brand = Brand::all();
      return Response::json($brand);

    }


    public function save_brand(Request $request)
      {

        $check = Brand::where('brandid', $request->brandid)->first();
        if ($check) {
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This ID already exits..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();

        }

        $brand = new Brand();
        $brand->name = $request->brand;
        $brand->brandid = $request->brandid;
        $brand->status = "1";
        $brand->created_by = Auth::user()->id;
        $brand->save();
        return $brand;
      }

  public function view_product_brand(Request $request)
  {
      
      if($request->ajax()){
          $brand = Brand::all();
          return Datatables::of($brand)->make(true);
      }
      return view("admin.product.brand");
  }

  public function published_brand($ID) {

    Brand::where('id', $ID)
    ->update(['status' => 1]);

    return ;
  }


  public function unpublished_brand($ID) {
    Brand::where('id', $ID)
      ->update(['status' => 0]);

      return ;
  }

  public function edit_brand(Request $request, $id)
  {

      $check = Brand::where('brandid', $request['data']['brandid'])->where('id','!=', $id)->first();
      if ($check) {
        $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This ID already exits..!</b></div>";
        return response()->json(['status'=> 303,'message'=>$message]);
        exit();
      }

        $brand = Brand::where('id',$id)
                ->update(['name' =>$request['data']['brandname'], 'brandid' =>$request['data']['brandid']]);

        return;
  }

}
