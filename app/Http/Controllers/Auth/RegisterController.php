<?php

namespace App\Http\Controllers\Auth;

use App\Image;
use App\Plan;
use App\Profile;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Intervention\Image\Facades\Image as ImageInt;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }
    public function index()
    {
        /* set plan properties to view */
        $plans = Plan::all();
        return view('auth.register', ['plans' => $plans]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'zip' => 'required|integer|min:5'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        /* create user account */
        $user = User::create([
            'name'=> $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        /* create image path and upload on server */
        $path =public_path().'/upload/';
        $file = $data['file'];

        foreach ($file as $f) {
            $filename = str_random(20) .'.' . $f->getClientOriginalExtension() ?: 'png';
            $img = ImageInt::make($f);
            $img->save($path . $filename);
         $user->Image = Image::create(['img' => $filename]); // save image path to table images
        }
        /* create user profile */
        $user->Profile = Profile::create([
            'user_id' => $user->id,
            'user_name' => $data['name'],
            'user_email' => $data['email'],
            'phone' => $data['phone'],
            'mobile' => $data['mobile'],
            'website' => $data['website'],
            'address'=> $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'zip' => $data['zip'],
            'logo' => $user->Image->id,
            'company' => $data['company'],
            'mls_option' => $data['mls_option'],
            'mls_user' => $data['mls_user'],
            'mls_email' => $data['mls_email'],
        ]);

        return $user;
    }
}
