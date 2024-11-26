<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function create_employee()
    {
    	return view('admin.employee.create');
    }

    public function manage_employee()
    {
      $users = User::when(Auth::user()->role_id != 1, function($query) {
            return $query->where('branch_id', Auth::user()->branch_id);
        })
        ->where('is_employee', '=', '1')
        ->get();

        return view('admin.employee.manageemployee', compact('users'));
    }

    public function published_user($ID) {

      User::where('id', $ID)
      ->update(['status' => 1]);
  
      return ;
    }
  
  
    public function unpublished_user($ID) {
      User::where('id', $ID)
        ->update(['status' => 0]);
  
        return ;
    }


    public function save_employee(Request $request)
    {

        $request->validate([
            'email' => 'required|unique:users,email',
            'username' => 'required|unique:users,username',
            // 'branch_id' => 'required',
            'name' => 'required',
            'role_id' => 'required',
            'password' => [
              'required',
              'min:6',
          ],
          'password_confirmation' => 'required|same:password'
        ]);



        $user_email = $request['email'];
        $username = $request['username'];
        if (User::where('username',$username)->first()) {
          return redirect()->back()->with('error', 'This username has already used. Please try another username..'); 
        }
        
        if(User::where('email',$user_email)->first())
        {
          return redirect()->back()->with('error', 'This email has already used. Please try another email..'); 
        }else{

          $data = new User;
          $data->name = $request['name'];
          $data->email = $request['email'];
          $data->username = $request['username'];
          $data->password = Hash::make($request['password']);
          $data->branch_id = auth()->user()->branch_id;
          $data->role_id = $request['role_id'];
          $data->phone = $request['phone'];
          $data->type = '1';
          $data->is_employee = '1';
          $data->save();
           return redirect()->back()->with('success', 'Employee Create Successfully'); 
        }
  	}

    public function update_employee(Request $request)
    {

      $user_email = $request['email'];
      $id = $request['userid'];

      if ($request['password']) {
            $request->validate([
              'email' => [
                              'required',
                              'unique:users,email,'.$id
                          ],
              // 'branch_id' => [
              //               'required'
              //           ],
              'role_id' => [ 'required' ],
              'username' => [
                                'required',
                                'unique:users,username,'.$id
                            ],
              'name' => 'required',
              'password' => [
                  'nullable',
                  'min:6',
              ],
              'password_confirmation' => [
                  'nullable',
                  'same:password',
                  'required_with:password',
              ],
          ]);
      } else {
            $request->validate([
              'email' => [
                              'required',
                              'unique:users,email,'.$id
                          ],
              // 'branch_id' => [
              //               'required'
              //           ],
              'name' => 'required',       
              'role_id' => [ 'required' ],
              'username' => [
                                'required',
                                'unique:users,username,'.$id
                            ]
          ]);
      }

        $username = $request['username'];
        if (User::where('username',$username)->where('id','!=',$id)->first()) {
          return redirect()->back()->with('error', 'This username has already used. Please try another username..'); 
        }
        
        if(User::where('email',$user_email)->where('id','!=',$id)->first())
        {
          return redirect()->back()->with('error', 'This email has already used. Please try another email..'); 
        }else{

          $data = User::find($id);
          $data->name = $request['name'];
          $data->username = $request['username'];
          $data->email = $request['email'];
          if ($request['password']) {
            $data->password = Hash::make($request['password']);
          }
          // $data->branch_id = $request['branch_id'];
          $data->role_id = $request['role_id'];
          $data->phone = $request['phone'];
          $data->save();
           return redirect()->back()->with('success', 'Employee Updated Successfully'); 
        }
  	}
}
