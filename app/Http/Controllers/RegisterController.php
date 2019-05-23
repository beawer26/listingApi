<?php

namespace App\Http\Controllers;

use App\Image;
use App\Profile;
use App\Subscriptions;
use App\User;
use App\PlansList;
use App\VerifyMail;
use DateTime;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image as ImageInt;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Stripe;
use App\Http\Controllers\Listing;
use Carbon\Carbon;

class RegisterController extends Controller
{

    public $successStatus = 200;

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
        $stripe = new Stripe(config('services.stripe.secret'));

        /* set plan properties to view */
        $plans = $stripe->plans()->all();

        return view('auth.register', ['plans' => $plans['data']]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'zip' => 'required|integer|min:5'
        ]);
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(Request $request)
    {

        $stripe = new Stripe(config('services.stripe.secret'));
        /* create user account */
        $input = $request->all();
        /* add customer to stripe service*/
        $token = Listing\TokenController::getToken();

        $arr_check_user = array(
            "token" => $token,
//            "name" => $input['name'],
            "email" => $input['email'],
//            "phone" => $input['mobile']
        );
        $url_ckeck = "https://slipstream.homejunction.com/ws/users/get?";

        $result = Listing\QueriesController::getQuery($arr_check_user, $url_ckeck);
        

        if (empty($result["result"]["users"])) {

            $arr_listing = array(
                "token" => $token,
                "name" => $input['name'],
                "email" => $input['email'],
                "phone" => $input['mobile']
            );
            $url = "https://slipstream.homejunction.com/ws/users/create?";

            $respons = Listing\QueriesController::getQuery($arr_listing, $url);
            
            $listing_id = $respons['result']['user']['id'];
        }else{
            $listing_id = $result["result"]["users"][0]["id"];
        }

        $plan = PlansList::where('plan_id', $input['plan'])->firstOrFail();

        $customer = $stripe->customers()->create([
            'email' => $input['email'],
        ]);
        /* add customer card */
        $token = $stripe->tokens()->create([

            'card' => [
                'number' => $input['number'],
                'exp_month' => $input['exp_month'],
                'cvc' => $input['cvc'],
                'exp_year' => $input['exp_year'],
            ],
        ]);
        /* create card with token */
        $card_id = $stripe->cards()->create($customer['id'], $token['id']);

        /* buy subscribe */
        $trial_end = Carbon::now()->addDays(30)->timestamp;
        $subscription = $stripe->subscriptions()->create($customer['id'], [
            'plan' => $input['plan'],
            'trial_end' => $trial_end,
        ]);

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'stripe_id' => $customer['id'],
            'stripe_card_id' => $card_id['id'],
            'listing_id' => $listing_id

        ]);

        /* create image path and upload on server */
        $path = public_path() . '/upload/';
        if($request->hasFile('file')) {
            $file[] = $request->file('file');

            foreach ($file as $f) {
                $filename = str_random(20) . '.' . $f->getClientOriginalExtension() ?: 'png';
                $img = ImageInt::make($f);
                $img->save($path . $filename);
                $user->Image = Image::create([
                    'img' => $filename,
                    'user_id' => $user->id
                ]); // save image path to table images
            }
        }
        else{
            $user->Image = Image::create([
                'img' => '',
                'user_id' => $user->id
            ]);
        }

        /* create user profile */
        $user->Profile = Profile::create([
            'user_id' => $user->id,
            'user_name' => $input['name'],
            'user_email' => $input['email'],
            'phone' => $input['phone'],
            'mobile' => $input['mobile'],
            'website' => $input['website'],
            'address' => $input['address'],
            'city' => $input['city'],
            'state' => $input['state'],
            'zip' => $input['zip'],
            'logo' => $user->Image->id,
            'company' => $input['company'],
            'stripe_plan' => $input['plan'],
            'buyers_left' => $plan['plan_buyers'],
            'sms_left' => $plan['plan_sms'],
            'license' => $input['license']
        ]);
        $start = DateTime::createFromFormat('U', $subscription['current_period_start']);
        $end = DateTime::createFromFormat('U', $subscription['current_period_end']);

        $user->Subscritions = Subscriptions::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'stripe_id' => $customer['id'],
            'stripe_plan' => $input['plan'],
            'sub_id' => $subscription['id'],
            'current_period_start' => $start,
            'current_period_end' => $end,
            'plan_buyers' => $plan['plan_buyers'],
            'plan_sms' => $plan['plan_sms'],
            'plan_notification' => $plan['plan_notification'],
            'plan_showing' => $plan['plan_showing'],
            'plan_trial' => $plan['plan_trial'],
            'status' => true
        ]);
        $user->VerifyMail = VerifyMail::create([
            'user_id' => $user->id,
            'token' => base64_encode($request['email'])
        ]);
        if ($user) {
            $mail = new MailController();

            $mail->send($request['email'], $user->id);
        }
        return response()->json();
    }

    protected function getEmails(Request $request)
    {
        $emails = User::where('email', $request->email)->first();
        if ($emails) {
            return response()->json('Email is already taken');
        } else {
            return response()->json(true);
        }
    }

    public function sendResetEmail(Request $request)
    {
        $send = new Auth\ForgotPasswordController;
        $send->sendResetLinkEmail($request);
        return response()->json();
    }

    public function resetPassword(Request $request)
    {
        $reset = new Auth\ResetPasswordController;
        $reset->reset($request);
        return response()->json();
    }
    
    public function checkCard(Request $request)
    {
        return response()->json('Invalid card credentials');
    }

}
