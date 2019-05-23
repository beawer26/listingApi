<?php

namespace App\Http\Controllers;

use App\Buyers;
use App\PlansList;
use App\SearchProposition;
use App\Subscriptions;
use Twilio\Rest\Client;
use App\Profile;
use DateTime;
use Intervention\Image\Facades\Image as ImageInt;
use Cartalyst\Stripe\Stripe;
use File;


class SmsController extends Controller
{
    public function index()
    {
        return view('home');
    }


    public function setSms($phone, $url,  $name)
    {

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_NUMBER');

        $twilio = new Client($sid, $token);

        $text = $name." has found a new listing for you \n".$url;

        $message = $twilio->messages
            ->create($phone, // to
                array(
                    "body" => $text,
                    "from" => $from
                )
            );

        return response()->json($message);
    }

    public function send()
    {
        $stripe = new Stripe(config('services.stripe.secret'));

        $users = Subscriptions::all();

        foreach ($users as $user){
            $subscription_new = $stripe->subscriptions()->find($user['stripe_id'], $user['sub_id']);

            $plan = PlansList::where('plan_id', $subscription_new["plan"]["id"])->first();

            $date = Date('Y-m-d');

            $str_date = strtotime($date);

            $end_date = strtotime($subscription_new['current_period_end']);

            $next_date = date('Y-m-d', strtotime("+30 days"));

            $result = $end_date - $str_date;

            if($result < 0 && $user["status"] != 0 && $subscription_new['status'] == 'active'){
                Profile::where("user_id", $user['user_id'])->update([
                    'sms_left' => $plan['plan_sms']
                ]);
                Subscriptions::where('user_id', $user['user_id'])->update([
                    'current_period_end' => $next_date
                ]);

            }
        }

        $buyers_phone = Buyers::all();

        $url = "http://app.listing2text.com:3000/listing-page/";

        foreach ($buyers_phone as $item) {

            $subscribtion = Subscriptions::where('user_id', $item->user_id)->first();

            $sms_count = Profile::where('user_id', $item->user_id)->first();

            $search = new BuyersController();

            $respons = $search->getListingResponse($item->id);

            $listing_id = SearchProposition::where('buyers_id', $item->id)->select('listing_id')->first();

            if ($respons != null) {
                if (($listing_id['listing_id'] != $respons[0]['id']) && (!empty($respons)) && ($sms_count["sms_left"] != 0) && ($subscribtion["status"] != 0) && ($item->sms_status != 0)) {


                    SearchProposition::where('buyers_id', $item->id)->update([
                        'listing_id' => $respons[0]['id']
                    ]);
                    $path = public_path() . '/upload/Listing/';
                    $filename = $respons[0]['id'].'.jpg';
                    if (File::exists($path . $filename)) {
                        File::delete($path . $filename);
                    }
                    $img = ImageInt::make($respons[0]['images'][0]);
                    $img->save($path . $filename);

                    $sms = self::setSms($item->phone, $url.$item->search_id."_".$respons[0]['id'], $sms_count['user_name']);

                    if($sms){

                        $buyer = Profile::where('user_id', $item->user_id)->firstOrFail();
                        $buyer->sms_left = $buyer->sms_left - 1;
                        $subscription = Subscriptions::where('user_id', $item->user_id)->firstOrFail();
                        if($subscription->plan_notification == 1){
                            $item->notify_status = 1;
                            $item->save();
                        }
                        $buyer->save();
                    }
                }
            }

        }
    }
}
