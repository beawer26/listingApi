<?php

namespace App\Http\Controllers;


use App\Buyers;
use App\Image;
use App\PlansList;
use App\Profile;
use App\SearchProposition;
use App\Subscriptions;
use App\User;
use Cartalyst\Stripe\Stripe;
use ChrisKonnertz\OpenGraph\OpenGraph;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BuyersController;
use Carbon\Carbon;


class TestController extends Controller
{
    public function index()
    {
        return view('test');
    }

    /**
     * @throws \Exception
     */
    public function test()
    {
        $all_list = array();

        $page = 1000;

        $buyers = Buyers::find('238');

        $locations = json_decode($buyers['location']);
        $polygon = array();


        $token = Listing\TokenController::getToken();

        $arr_listing = array(
            "token" => $token,
            "details" => true,
            "images" => true,
            "market" => "carets",
            "pageSize" => '1000',
            "sortOrder" => "desc",
            "sortField" => "listingDate",
            "features" => true

        );

        $arr_filters = array(
            "beds" => $buyers['min_beds'] . ":" . $buyers['max_beds'],
            "baths" => $buyers['min_baths'] . ":" . $buyers['max_baths'],
            "listPrice" => $buyers['min_price'] . ":" . $buyers['max_price'],
            "propertyType" => $buyers['type'],

        );
        $arr_filters['geometry']['type'] = 'polygon';
        foreach($locations as $loc){
            $arr_filters['geometry']['coordinates'] = (float)$loc->lat.', '. (float)$loc->lng;
        }
        $arr_filters['geometry']['coordinates'] = (float)$locations[0]->lat.', '.(float)$locations[0]->lng;

        $all_array = array_merge($arr_filters, $arr_listing);

        $url = "https://slipstream.homejunction.com/ws/listings/search?";

        $respons = Listing\QueriesController::getQuery($all_array, $url);

        dd($respons);
    }
}
