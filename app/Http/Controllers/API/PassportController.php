<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class PassportController extends Controller
{

    public $successStatus = 200;
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $verify = User::where('email', \request('email'))->first();
            if($verify['verified'] == 1) {
                $user = Auth::user();
                $success['access-token'] = $user->createToken('MyAPI')->accessToken;
                $success['user_id'] = $verify['id'];
                
                return response()->json($success, $this->successStatus);
            }
            else{
                return response()->json(['error'=>'Unverified'], 302);
            }
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }
    
    public function logout()
    {
        return;
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'comfirm_password' => 'required|same:password']);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['access-token'] =  $user->createToken('MyAPI')->accessToken;

        return response()->json($success, $this->successStatus);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function userInfo()
    {
        $user = Auth::user();
        return response()->json($user, $this->successStatus);
    }
}