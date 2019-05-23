<?php

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cartalyst\Stripe\Stripe;
use App\User;
use App\Subscriptions;
use App\Image;
use App\Buyers;
use App\PlansList;
use App\Http\Controllers\Auth;
use File;

class UserController extends Controller
{
    public function __construct()
    {
        return $this->middleware('guest');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $profile = DB::table('profiles')->join('subscriptions', 'subscriptions.user_id', '=', 'profiles.user_id')->get();

        return response()->json($profile);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $stripe = new Stripe(config('services.stripe.secret'));
        $user = new User();
        $profile = new Profile();
        $image = new Image();
        $sub = new Subscriptions();
        $buyers = new Buyers();
        foreach ($request->all() as $id){
            $user->where('id', $id)->delete();
            $profile->where('user_id', $id)->delete();
            $path = public_path() . '/upload/';
            $img = $image->where('user_id', $id)->firstOrFail();
            $filename = $img->img;
            if (File::exists($path . $filename)) {
                File::delete($path . $filename);
            }
            $image->where('user_id', $id)->delete();
            $sub_id = $sub->where('user_id', $id) ->firstOrFail();
            $subscriptions = $stripe->subscriptions()->all($sub_id->stripe_id);

            foreach ($subscriptions['data'] as $subscription) {
                $stripe->subscriptions()->cancel($sub_id->stripe_id, $subscription['id']);
            }
            $stripe->customers()->delete($sub_id->stripe_id);
            $sub->where('user_id', $id)->delete();
            $buyers->where('user_id', $id)->delete();
        }

        return response()->json();
    }

    public function checkStatus($id)
    {
        $stripe = new Stripe(config('services.stripe.secret'));

        $user = Subscriptions::where('user_id', $id)->first();

        $subscription = $stripe->subscriptions()->find($user['stripe_id'], $user['sub_id']);

        $plan = PlansList::where('plan_id', $subscription["plan"]["id"])->first();

        $date = Date('Y-m-d');

        $str_date = strtotime($date);

        $end_date = strtotime($subscription['current_period_end']);

        $next_date = date('Y-m-d', strtotime("+30 days"));

        $result = $end_date - $str_date;

        if($result < 0 && $user["status"] != 0){
            Profile::where("user_id", $id)->update([
                'sms_left' => $plan['plan_sms']
            ]);
            Subscriptions::where('user_id', $id)->update([
                'current_period_end' => $next_date
            ]);

        }
        return response()->json($user["status"]);
    }
}
