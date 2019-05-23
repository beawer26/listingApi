<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Stripe;
use PhpParser\Node\Expr\Array_;

class ChargeController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $stripe = new Stripe(config('services.stripe.secret'));
//
//        $token = $stripe->tokens()->create([
//
//            'card' => [
//                'number'    => '4242424242424242',
//                'exp_month' => 10,
//                'cvc'       => 314,
//                'exp_year'  => 2020,
//            ],
//        ]);
//
//        $stripe->subscriptions()->create($customer['id'], [
//            'plan' => 'monthly',
//        ]);
//
//        $stripe->cards()->create($customer['id'], $token['id']);
//
//        return view('user.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function destroy($id)
    {
        //
    }

    public function showBilling()
    {
        $stripe = new Stripe(config('services.stripe.secret'));
        $billing = $stripe->charges()->all(['limit'=>100]);
        $new_arr = array();
        foreach($billing['data'] as $bill)
        {
            $customer = User::where('stripe_id', $bill['customer'])->get()->first();
            if($customer){
                $bill['customer'] = $customer['name'];
                array_push($new_arr, $bill);
            }
        }
        return response()->json($new_arr);
    }
}
