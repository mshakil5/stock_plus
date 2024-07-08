<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{

    public function get_all_category()
  {
    $category = Category::all();

        // return $category;
        // return response()->json($category);

        return Response::json($category);

  }


    public function save_category(Request $request)
    {

      $check = Category::where('categoryid', $request->categoryid)->first();
      if ($check) {
        $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This ID already exits..!</b></div>";
        return response()->json(['status'=> 303,'message'=>$message]);
        exit();

      }

      

      $category = new Category();
      $category->name = $request->category;
      $category->categoryid = $request->categoryid;
      $category->status = "1";
      $category->created_by = Auth::user()->id;
      $category->save();
      return $category;
    }

  public function view_product_category(Request $request)
  {
      
      if($request->ajax()){
          $category = Category::all();
          return Datatables::of($category)->make(true);
      }
      return view("admin.product.category");
  }

  public function published_category($ID) {

    Category::where('id', $ID)
    ->update(['status' => 1]);

    return ;
}


public function unpublished_category($ID) {
   Category::where('id', $ID)
    ->update(['status' => 0]);

    return ;
}

public function edit_category(Request $request, $id)
  {

    $check = Category::where('categoryid', $request['data']['categoryid'])->where('id','!=', $id)->first();
    if ($check) {
      $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This ID already exits..!</b></div>";
      return response()->json(['status'=> 303,'message'=>$message]);
      exit();

    }

            $category = Category::where('id',$id)
                    ->update(['name' =>$request['data']['categoryname'], 'categoryid' =>$request['data']['categoryid']]);

            return $category;
  }



}
