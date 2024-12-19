<?php
  
namespace App\Http\Controllers\Auth;
  
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\support\Facades\Auth;
use App\Models\Branch;
use App\Models\UserLogHistory;

  
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
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $input = $request->all();
        $isEmail = filter_var($input['email'], FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'username';
        
        $user = User::where($field, $input['email'])->first();

        if (!$user) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->with('message', 'Credential Error. You are not an authenticated user.');
        }

        if ($user->status != 1) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->with('message', 'Your ID is deactivated.');
        }

        $branch = Branch::find($user->branch_id);

        if ($branch && $branch->status == 0) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->with('message', 'Your Branch is deactivated.');
        }

        if (!auth()->attempt([$field => $input['email'], 'password' => $input['password']])) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->with('message', 'Email/Username and Password are wrong.');
        }

        $authUser = auth()->user();
        $branch = Branch::find($authUser->branch_id);

        $logEntry = new UserLogHistory();
        $logEntry->user_id = $authUser->id;
        $logEntry->branch_id = $authUser->branch_id;
        $logEntry->ip_address = $request->ip();
        $logEntry->save();

        if ($authUser->type == 1) {
            return redirect()->route('admin.home');
        } elseif ($authUser->type == 2) {
            return redirect()->route('manager.home');
        } elseif ($authUser->type == 0) {
            return redirect()->route('user.home');
        } else {
            return redirect()->route('home');
        }
    }
}