<?php

namespace App\Http\Controllers\Listing;

use App\Http\Controllers\Controller;

class TokenController extends Controller
{

    static public function getToken()
    {
        $data = array("license" => env("LISTING_SECRET_KEY"));

        $data_string = json_encode($data);

        $ch = curl_init('https://slipstream.homejunction.com/ws/api/authenticate?');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );

        $response = curl_exec($ch);

        $result = json_decode($response, true);

        $token = $result['result']['token'];

        return $token;
    }
}
