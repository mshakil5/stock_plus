<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Size;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class SizeController extends Controller
{
    public function get_all_size()
    {
      $size = Size::all();
  
  
          return Response::json($size);
  
    }

    public function view_product_size(Request $request)
  {
    if($request->ajax()){
      $size = Size::all();
      return Datatables::of($size)->make(true);
  }
      
      return view("admin.product.size");
  }
  
  
      public function save_size(Request $request)
    {
      $size = new Size();
      $size->name = $request->size;
      $size->created_by = Auth::user()->id;
      $size->save();
      return;
    }

    public function published_size($ID) {

      Size::where('id', $ID)
      ->update(['status' => 1]);
  
      return ;
    }
  
  
    public function unpublished_size($ID) {
      Size::where('id', $ID)
        ->update(['status' => 0]);
  
        return ;
    }

    public function edit_size(Request $request, $id)
  {
            $size = Size::where('id',$id)
                    ->update(['name' =>$request['data']['sizename']]);

            return;
  }
}
