<?php

namespace App\Http\Controllers\Listing;

use App\Http\Controllers\Controller;

class QueriesController extends Controller
{
    static public function getQuery(array $data = array(), $url)
    {
        $data_string = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );

        $response = curl_exec($ch);

        $result = json_decode($response, true);

        return $result;
    }
}
