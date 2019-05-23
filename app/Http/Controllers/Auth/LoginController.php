<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\VerifyMail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->middleware('guest')->except('logout');
    }

    public function verify($token)
    {
        $verify_token = VerifyMail::where("token", $token)->first();

        if($verify_token){
            $user_verify = User::where("id", $verify_token["user_id"])->update([
                'verified' => 1,
            ]);
            if($user_verify){
                VerifyMail::where("token", $token)->delete();
            }
        }

        return response()->json();
    }
}
