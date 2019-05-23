<?php

namespace App\Http\Controllers;

use App\Subscriptions;
use Cartalyst\Stripe\API\Plans;
use App\PlansList;

use Illuminate\Http\Request;
use Cartalyst\Stripe\Stripe;

class PlansListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stripe = new Stripe(config('services.stripe.secret'));

        $plans = $stripe->plans()->all();


        return view('plans.index', ['plans' => $plans]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $input = $request->all();
        PlansList::create($input);
        if($input['plan_trial'] == true){
            $p_trial = 30;
        }
        else{
            $p_trial = null;
        }
        $new_descr = substr($input['plan_description'], 0, 20);
        $stripe = new Stripe(config('services.stripe.secret'));
        $stripe->plans()->create([
            'id'                   => $input['plan_id'],
            'name'                 => $input['plan_name'],
            'amount'               => floatval($input['plan_amount'].'.00'),
            'currency'             => 'USD',
            'interval'             => 'month',
            'statement_descriptor' => $new_descr,
            'trial_period_days' => $p_trial
        ]);
        return response()->json();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $plans = PlansList::orderBy('plan_amount', 'asc')->get()->all();
        return response()->json($plans);
    }

    public function getPlanById($plan_id)
    {
        $plan = PlansList::where('plan_id', $plan_id)->first();
        return response()->json($plan);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $input = $request->all();
        PlansList::where('plan_id', $input['plan_id'])->update($input);
        $subscription = Subscriptions::where('stripe_plan', $input['plan_id'])->get()->all();
        foreach($subscription as $sub){
            $sub['plan_notification'] = $input['plan_notification'];
            $sub['plan_showing'] = $input['plan_showing'];
            $sub['plan_trial'] = $input['plan_trial'];
            $sub->save();
        }
        $new_descr = substr($input['plan_description'], 0, 20);
        if($input['plan_trial'] == true){
            $p_trial = 30;
        }
        else{
            $p_trial = null;
        }
        $stripe = new Stripe(config('services.stripe.secret'));
        $stripe->plans()->update($input['plan_id'], [
            'name'                 => $input['plan_name'],
            'metadata' => ['amount'               => floatval($input['plan_amount'].'.00'),
            'currency'             => 'USD',
            'interval'             => 'month'],
            'statement_descriptor' => $new_descr,
            'trial_period_days' => $p_trial
        ]);
        return response()->json();
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
    public function destroy($id)
    {
        $plan = PlansList::where('plan_id', $id)->firstOrFail();
        $plan->delete();
        $stripe = new Stripe(config('services.stripe.secret'));
        $stripe->plans()->delete($id);
    }
}
