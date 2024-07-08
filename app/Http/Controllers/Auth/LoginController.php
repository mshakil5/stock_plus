<?php
  
namespace App\Http\Controllers\Auth;
  
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\support\Facades\Auth;

  
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
  
    use AuthenticatesUsers;
  
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
 
    public function login(Request $request)
    {   
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        if (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            // return ['email' => $request->get('email'), 'password'=>$request->get('password')];

            $chksts = User::where('email', $input['email'])->first();
            if ($chksts) {
                if ($chksts->status == 1) {
                    if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'])))
                        {
                            if (auth()->user()->type == 1) {
                                return redirect()->route('admin.home');
                            }else if (auth()->user()->type == 2) {
                                return redirect()->route('manager.home');
                            }else if (auth()->user()->type == 0) {
                                return redirect()->route('user.home');
                            }else{
                                return redirect()->route('home');
                            }
                        }else{
                            return view('auth.login')
                                ->with('message','Email And Password Are Wrong.');
                        }
                }else{
                    return view('auth.login')
                    ->with('message','Your ID is Deactive.');
                }
            }else {
                return view('auth.login')
                    ->with('message','Credential Error. You are not authenticate user.');
            }

        }else{
            // return ['username' => $request->get('email'), 'password'=>$request->get('password')];
            $chksts = User::where('username', $input['email'])->first();
            if ($chksts) {
                if ($chksts->status == 1) {
                    if(auth()->attempt(array('username' => $input['email'], 'password' => $input['password'])))
                        {
                            if (auth()->user()->type == 1) {
                                return redirect()->route('admin.home');
                            }else if (auth()->user()->type == 2) {
                                return redirect()->route('manager.home');
                            }else if (auth()->user()->type == 0) {
                                return redirect()->route('user.home');
                            }else{
                                return redirect()->route('home');
                            }
                        }else{
                            return view('auth.login')
                                ->with('message','Username And Password Are Wrong.');
                        }
                }else{
                    return view('auth.login')
                    ->with('message','Your ID is Deactive.');
                }
            }else {
                return view('auth.login')
                    ->with('message','Credential Error. You are not authenticate user.');
            }



        }
        
    }
}