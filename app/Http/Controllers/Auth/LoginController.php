<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm(){
      $pageConfigs = ['myLayout' => 'blank'];
      return view('auth.login', ['pageConfigs' => $pageConfigs]);
    }

    protected function credentials(Request $request): array
    {
      return [
        $this->username() => $request->input($this->username()),
        'password' => $request->input('password'),
        'status' => true,
      ];
    }

    protected function authenticated(Request $request, $user)
    {
      return redirect()->intended($user->isSecurityOfficer() ? route('attendee.qr.scan') : route('dashboard'));
    }
}
