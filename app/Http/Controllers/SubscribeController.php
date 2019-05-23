<?php

namespace App\Http\Controllers;

use App\PlansList;
use App\Profile;
use App\Subscriptions;
use App\User;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Stripe;
use Illuminate\Support\Facades\DB;
use DateTime;


class SubscribeController extends Controller
{
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

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

        $stripe = new Stripe(config('services.stripe.secret'));

        $subscribe = Subscriptions::where('user_id', $id)->first();

        $subscription = $stripe->subscriptions()
            ->update($subscribe->stripe_id, $subscribe->sub_id, [
                'plan' => $input['plan_name'],
            ]);

        $start = DateTime::createFromFormat('U', $subscription['current_period_start']);

        $end = DateTime::createFromFormat('U', $subscription['current_period_end']);

        $plan = PlansList::where('plan_id', $input['plan_name'])->first();

        Subscriptions::where("user_id", $id)
            ->update([
                'stripe_plan' => $plan['plan_id'],
                'current_period_start' => $start,
                'current_period_end' => $end,
                'plan_buyers' => $plan['plan_buyers'],
                'plan_sms' => $plan['plan_sms'],
                'plan_notification' => $plan['plan_notification'],
                'plan_showing' => $plan['plan_showing'],
                'status' => true
            ]);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $subscription = \Stripe\Subscription::retrieve($subscribe['sub_id']);

        $response = \Stripe\Subscription::update($subscribe['sub_id'], [
            'cancel_at_period_end' => false,
            'items' => [
                [
                    'id' => $subscription->items->data[0]->id,
                    'plan' => $plan['plan_id'],
                ],
            ],
        ]);

        if ($response) {

            $profile = Profile::where('user_id', $id)->first();

            $plan = PlansList::where('plan_id', $profile["stripe_plan"])->first();

            $sms_left = $plan["plan_sms"] - $profile["sms_left"];

            $buyers_left = $plan["plan_buyers"] - $profile["buyers_left"];

            $new_plan = PlansList::where('plan_id', $input['plan_name'])->first();

            $new_sms = $new_plan["plan_sms"] - $sms_left;

            $new_buyers = $new_plan["plan_buyers"] - $buyers_left;

            Profile::where('user_id', $id)->update([
                'stripe_plan' => $input['plan_name'],
                'sms_left' => $new_sms,
                'buyers_left' => $new_buyers
            ]);
        }

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function cardUpdate(Request $request, $id)
    {
        $input = $request->all();

        $user = User::where('id', $id)->first();

        $stripe = new Stripe(config('services.stripe.secret'));

        $stripe->cards()->delete($user->stripe_id, $user->stripe_card_id);

        $token = $stripe->tokens()->create([

            'card' => [
                'number' => $input['number'],
                'exp_month' => $input['exp_month'],
                'cvc' => $input['cvc'],
                'exp_year' => $input['exp_year'],
            ],
        ]);
        $card = $stripe->cards()->create($user->stripe_id, $token['id']);
        User::where('id', $id)->update(['stripe_card_id' => $card['id']]);
        return response()->json();
    }

    public function getSubscribtion($id)
    {
        $subscription = DB::table('subscriptions')->join('plans_lists', 'subscriptions.stripe_plan', '=', 'plans_lists.plan_id')->where('subscriptions.user_id', $id)->get();
        return response()->json($subscription);
    }

    public function endSubscribtion($id)
    {
        $subscribe = Subscriptions::where('user_id', $id)->first();

        $stripe = new Stripe(config('services.stripe.secret'));

        $stripe->subscriptions()->cancel($subscribe['stripe_id'], $subscribe['sub_id'], true);

        return response()->json();
    }

    public function suspend($id)
    {
        $stripe = new Stripe(config('services.stripe.secret'));
        $sub_id = Subscriptions::where('user_id', $id)->firstOrFail();
        $subscriptions = $stripe->subscriptions()->all($sub_id->stripe_id);

        if ($sub_id->status == true) {
            foreach ($subscriptions['data'] as $subscription) {
                $stripe->subscriptions()->cancel($sub_id->stripe_id, $subscription['id'], true);
            }
            $sub_id->status = false;
            $sub_id->save();
        } else {
            foreach ($subscriptions['data'] as $subscription) {
                $stripe->subscriptions()->reactivate($sub_id->stripe_id, $subscription['id']);
            }
            $sub_id->status = true;
            $sub_id->save();
        }
        return response()->json($subscriptions);
    }
}
