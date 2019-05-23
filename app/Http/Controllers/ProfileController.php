<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;
use App\Image;
use App\User;
use App\Subscriptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image as ImageInt;
use Cartalyst\Stripe\Stripe;
use File;

class ProfileController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $stripe = new Stripe(config('services.stripe.secret'));

        $profile = Profile::where('user_id', $id)->first(); // get profile fore when we will find logo


        $images = Image::where('user_id', $id)->first(); // get image form logo id
        $profile['logo'] = $images['img'];

        return response()->json( $profile);

    }

    public function showPayment($id)
    {
        $stripe = new Stripe(config('services.stripe.secret'));
        $user = User::where('id', $id)->get()->first();
        $card = $stripe->cards()->find($user['stripe_id'], $user['stripe_card_id']);

        $subscription = Subscriptions::where('user_id', $id)->get()->first();

        $card['plan_name'] = $subscription['stripe_plan'];

        return response()->json($card);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $get_image_path = Image::where('user_id', $id)->first();

        $pattern = "/".$get_image_path['img']."/";

        

        $path = public_path() . '/upload/';

        if($request->hasFile('file')) {
            if (File::exists($path . $get_image_path['img'])) {
                File::delete($path . $get_image_path['img']);
            }
            Image::where('user_id', $id)->delete();
            $file[] = $request->file('file');
            foreach ($file as $f) {
                $filename = str_random(20) . '.' . $f->getClientOriginalExtension() ?: 'png';
                $img = ImageInt::make($f);
                $img->save($path . $filename);
                Image::create([
                    'img' => $filename,
                    'user_id' => $id
                ]); // save image path to table images
            }
        }
        unset($input['file']);

        $profile = Profile::where('user_id', $id)->update($input);
        $user = User::where('id', $id)->update(['email' => $input['user_email']]);

        return response()->json($profile);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    public function checkOldPassword(Request $request)
    {

        $input = $request->all();

        $user = User::select('password')->where('id', $input['id'])->first();

        $check = Hash::check($input['old_password'], $user['password']);

        if($check){
            return response()->json(true);
        }else{
            return response()->json("Invalid password");
        }
    }

    public function changePassword(Request $request)
    {

        $input = $request->all();

        User::where('id', $input['id'])->update([
            'password' => Hash::make($input['password'])
        ]);

    }

    public function destroy($id)
    {
        //
    }
}
