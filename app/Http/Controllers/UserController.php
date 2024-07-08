<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    public function create_user()
    {
    	return view('admin.user.create');
    }


    public function manage_user()
    {
      $users = User::where('type','=','0')->get();
      // dd($users );
    	return view('admin.user.manageuser', compact('users'));
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


    public function save_user(Request $request)
    {

        $request->validate([
            'email' => 'required|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => [
              'required',
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
          $data->branch_id = $request['branch_id'];
          $data->role_id = $request['role_id'];
          $data->phone = $request['phone'];
          $data->save();
           return redirect()->back()->with('success', 'User Create Successfully'); 
        }
  	}

    public function update_user(Request $request)
    {

      $user_email = $request['email'];
      $id = $request['userid'];

      if ($request['password']) {
            $request->validate([
              'email' => [
                              'required',
                              'unique:users,email,'.$id
                          ],
              'branch_id' => [
                            'required'
                        ],
              'role_id' => [ 'required' ],
              'username' => [
                                'required',
                                'unique:users,username,'.$id
                            ],
              'password' => [
                                'required',
                                Password::min(8)
                                    ->letters()
                                    ->mixedCase()
                                    ->numbers()
                                    ->symbols()
                                    ->uncompromised()
                            ]
          ]);
      } else {
            $request->validate([
              'email' => [
                              'required',
                              'unique:users,email,'.$id
                          ],
              'branch_id' => [
                            'required'
                        ],
                        
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
          $data->branch_id = $request['branch_id'];
          $data->role_id = $request['role_id'];
          $data->phone = $request['phone'];
          $data->save();
           return redirect()->back()->with('success', 'User Updated Successfully'); 
        }
  	}

    public function create_admin()
    {
    	return view('admin.user.createadmin');
    }

    public function manage_admin()
    {
      $users = User::where('type','=','1')->where('id','>', '4')->get();
      // dd($users );
    	return view('admin.user.manageadmin', compact('users'));
    }

    public function save_admin(Request $request)
    {
       $request->validate([
            'email' => 'required|unique:users,email',
            'username' => 'required|unique:users,username',
            'branch_id' => 'required',
            'role_id' => 'required',
            'password' => [
              'required'
            ],
            'password_confirmation' => 'required|same:password'
        ]);



        $user_email = $request['email'];
        $username = $request['username'];
        $branchIDs = $request['branch_id'];
        if (User::where('username',$username)->first()) {
          return redirect()->back()->with('error', 'This username has already used. Please try another username..'); 
        }
        
        if(User::where('email',$user_email)->first())
        {
          return redirect()->back()->with('error', 'This email has already used. Please try another email..'); 
        }else{

          $data = new User;
          $data->name = $request['name'];
          $data->username = $request['username'];
          $data->email = $request['email'];
          $data->phone = $request['phone'];
          if ($request['password']) {
            $data->password = Hash::make($request['password']);
          }
          $data->role_id = $request['role_id'];
          $data->branch_id = $branchIDs[0];
          $data->branchaccess = json_encode($branchIDs);
          $data->type = '1';
          $data->save();
          return redirect()->back()->with('success', 'Admin Create Successfully'); 
        }
  	}

    public function update_admin(Request $request)
    {

      $user_email = $request['email'];
      $id = $request['userid'];

      if ($request['password']) {
            $request->validate([
              'email' => [
                              'required',
                              'unique:users,email,'.$id
                          ],
              'username' => [
                                'required',
                                'unique:users,username,'.$id
                            ],
              'branch_id' => [ 'required' ],
              'role_id' => [ 'required' ],
              'password' => [
                                'required',
                                Password::min(8)
                                    ->letters()
                                    ->mixedCase()
                                    ->numbers()
                                    ->symbols()
                                    ->uncompromised()
                            ]
          ]);
      } else {
            $request->validate([
              'email' => [
                              'required',
                              'unique:users,email,'.$id
                          ],
              'branch_id' => [ 'required' ],
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
      
        $branchIDs = $request['branch_id'];
        // dd($request['branch_id']);
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
          $data->role_id = $request['role_id'];
          $data->phone = $request['phone'];
          if ($branchIDs) {
            $data->branch_id = $branchIDs[0];
          }
          $data->branchaccess = json_encode($branchIDs);
          $data->save();
           return redirect()->back()->with('success', 'User Updated Successfully'); 
        }
  	}

    public function switch_branch()
    {
    	return view('admin.user.switch');
    }

    public function switch_branch_store(Request $request)
    {
          $data = User::find(Auth::user()->id);
          $data->branch_id = $request['branch_id'];
          $data->save();
          return redirect()->back()->with('success', 'Branch Switch Successfully'); 
  	}

    public function super_admin()
    {
    	return view('admin.user.super_admin');
    }

    public function update_super_admin(Request $request)
    {

      $user_email = $request['email'];
      $id = Auth::user()->id;

      if ($request['password']) {
            $request->validate([
              'email' => [
                              'required',
                              'unique:users,email,'.$id
                          ],
              'username' => [
                                'required',
                                'unique:users,username,'.$id
                            ],
              'password' => [
                                'required',
                                Password::min(8)
                                    ->letters()
                                    ->mixedCase()
                                    ->numbers()
                                    ->symbols()
                                    ->uncompromised()
              ],
              'password_confirmation' => 'required|same:password'
          ]);
      } else {
            $request->validate([
              'email' => [
                              'required',
                              'unique:users,email,'.$id
                          ],
              'username' => [
                                'required',
                                'unique:users,username,'.$id
                            ]
          ]);
      }
      
        // dd($request['branch_id']);
        $username = $request['username'];
        if (User::where('username',$username)->where('id','!=',$id)->first()) {
          return redirect()->back()->with('error', 'This username has already used. Please try another username..'); 
        }
        if(User::where('email',$user_email)->where('id','!=',$id)->first())
        {
          return redirect()->back()->with('error', 'This email has already used. Please try another email..'); 
        }else{

          $data = User::find(Auth::user()->id);
          $data->name = $request['name'];
          $data->username = $request['username'];
          $data->email = $request['email'];
          $data->phone = $request['phone'];
          if ($request['password']) {
            $data->password = Hash::make($request['password']);
          }
          $data->save();
           return redirect()->back()->with('success', 'User Updated Successfully'); 
        }
  	}

    
}
