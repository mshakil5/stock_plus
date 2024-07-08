<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class GroupController extends Controller
{
    public function get_all_group()
    {
      $group = Group::all();
      return Response::json($group);
  
    }
  
  
    public function save_group(Request $request)
    {
      $check = Group::where('groupid', $request->groupid)->first();
      if ($check) {
        $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This ID already exits..!</b></div>";
        return response()->json(['status'=> 303,'message'=>$message]);
        exit();

      }
      


      $group = new Group();
      $group->name = $request->group;
      $group->groupid = $request->groupid;
      $group->status = "1";
      $group->created_by = Auth::user()->id;
      $group->save();
      return $group;
    }
  
    public function view_product_group(Request $request)
    {
        
        if($request->ajax()){
            $group = Group::all();
            return Datatables::of($group)->make(true);
        }
        return view("admin.product.group");
    }
  
    public function published_group($ID) {
  
        Group::where('id', $ID)
      ->update(['status' => 1]);
  
      return ;
    }
  
  
    public function unpublished_group($ID) {
        Group::where('id', $ID)
        ->update(['status' => 0]);
  
        return ;
    }
  
    public function edit_group(Request $request, $id)
    {

      $check = Group::where('groupid', $request['data']['groupid'])->where('id','!=', $id)->first();
      if ($check) {
        $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This ID already exits..!</b></div>";
        return response()->json(['status'=> 303,'message'=>$message]);
        exit();

      }
          $group = Group::where('id',$id)
                  ->update(['name' =>$request['data']['groupname'], 'groupid' =>$request['data']['groupid']]);

          return;
    }
}
