<?php
  
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
  
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return view('home');
        if (auth()->user()->type == '1') {
            return redirect()->route('admin.home');
        }elseif (auth()->user()->type == '2') {
            return redirect()->route('manager.home');
        }elseif (auth()->user()->type == '0') {
            return redirect()->route('user.home');
        }else {
            return view('auth.login');
        }
    } 
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminHome()
    {
        return view('admin.dashboard');
    }
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function managerHome()
    {
        return view('managerHome');
    }

    public function userHome()
    {
        return view('user.sales');
    }

    public function sales()
    {
        return view('user.sales');
    }
}