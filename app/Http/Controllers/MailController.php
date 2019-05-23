<?php

namespace App\Http\Controllers;

use App\Mail\RegisterMail;
use App\VerifyMail;
use App\User;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function send($mail, $id)
    {
        $user_name = User::where("id", $id)->select("name")->first();

        $info = new \stdClass();
        $info->subject = "Welcome to Listing2Text";
        $info->text = "Dear ".$user_name.",<br />Thanks for choosing Listing2text! It's great to have you on board! We hope you will like the service as much as we liked building it.<br /><br />";
        $info->token = "Welcome to Listing2Text.  Login <a href='https://app.listing2text.com/login'>here</a> to start adding buyers.";

        Mail::to($mail)->send(new RegisterMail($info));
    }
}
