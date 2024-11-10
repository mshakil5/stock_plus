<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
        
        if (auth()->user()) {
            return redirect()->route('home');
        }else {
            return view('auth.login');
        }
    } 
}
