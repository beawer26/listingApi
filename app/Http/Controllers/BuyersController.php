<?php

namespace App\Http\Controllers;

use App\Buyers;
use App\Image;
use App\Profile;
use App\SearchProposition;
use App\User;
use App\Subscriptions;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use File;
use App\Mail\RegisterMail;
use App\VerifyMail;
use Illuminate\Support\Facades\Mail;


class BuyersController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user.create_buyers');
    }

    public function getAllClients()
    {
        $clients = Buyers::all();

        return response()->json($clients);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 20; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $input = $request->all();

        $input['search_name'] = $randomString;

        $token = Listing\TokenController::getToken();

        $listing_id = User::where('id', $input['user_id'])->first();

//        $arr_filters = array(
//            'listPrice'=> $input['min_price'].":".$input['max_price'],
//            'beds' => $input['beds'],
//            'baths' => $input['baths'],
//            'type' => $input['type'],
//            'address.city' => $input['city']
//        );
        $a_filters = "{\r\n    \"city\": \"" . $input['city'] . "\",\r\n    \"beds\": \"" . $input['min_beds'] . " \",\r\n   \"baths\": \"" . $input['min_baths'] . " \",\r\n \"listPrice\": \"" . $input['min_price'] . ":" . $input['max_price'] . "\"\r\n}";

        $arr_listing = array(
            "token" => $token,
            "market" => "carets",
            'name' => $randomString,
            'userId' => $listing_id->listing_id,
            'filters' => $a_filters
        );

        $url = "https://slipstream.homejunction.com/ws/users/searches/add?";

        $respons = Listing\QueriesController::getQuery($arr_listing, $url);
        $input['search_id'] = $respons["result"]["search"]["id"];

        $buyer = Buyers::create($input);

        $listing = $this->getListingResponse($buyer->id);


        if (!empty($listing)) {
            SearchProposition::create([
                "buyers_id" => $buyer->id,
                "listing_id" => $listing[0]['id']
            ]);
        }

        $profile = Profile::where('user_id', $input['user_id'])->firstOrFail();
        $profile->buyers_left = $profile->buyers_left - 1;


        $jsonString = file_get_contents(public_path().'/buyers_list.json');

        $data = json_decode($jsonString, true);



        // Update Key

        $data[$input['search_id']]['agent_name'] = $profile->user_name;
        $data[$input['search_id']]['agent_company'] = $profile->company;
        $data[$input['search_id']]['buyer_name'] = $input['name'];


        // Write File

        $newJsonString = json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents((public_path().'/buyers_list.json'), stripslashes($newJsonString));
        $info = new \stdClass();
        $info->subject = "Listing2Text";
        $info->text = "You have been signed up to <a href='https://listing2text.com'>Listing2Text</a>";
        $info->token = "Realtor ".$profile['user_name'] . " has signed you up to <a href='https://listing2text.com'>Listing2text</a> <br />".'<br/>Realtor phone: ' . $profile['phone'] . '<br />Realtor email: ' . $profile['user_email'];

        Mail::to($input['email'])->send(new RegisterMail($info));
        $profile->save();



        return response()->json($respons);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $buyers = Buyers::where('user_id', $id)->get()->all();
        $profile = Profile::where('user_id', $id)->first();
        $subscription = Subscriptions::where('user_id', $id)->first();
        array_push($buyers, $profile);
        array_push($buyers, $subscription);
        return response()->json($buyers);
    }

    public function getBuyer($id){
        $buyer = Buyers::where('id', $id)->first();
        return response()->json($buyer);
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
        $token = Listing\TokenController::getToken();

        $listing_id = User::where('id', $input['user_id'])->first();
        $search_id = Buyers::where('id', $id)->first();

        $a_filters = "{\r\n    \"city\": \"" . $input['city'] . "\",\r\n    \"beds\": \"" . $input['min_beds'] . " \",\r\n   \"baths\": \"" . $input['min_baths'] . " \",\r\n \"listPrice\": \"" . $input['min_price'] . ":" . $input['max_price'] . "\"\r\n}";

        $a_filters1 = $a_filters;

        $arr_listing = array(
            "token" => $token,
            "market" => "carets",
            'name' => $search_id->search_name,
            'userId' => $listing_id->listing_id,
            'searchId' => $search_id->search_id,
            'filters' => $a_filters1
        );

        $url = "https://slipstream.homejunction.com/ws/users/searches/add?";

        $respons = Listing\QueriesController::getQuery($arr_listing, $url);

        $jsonString = file_get_contents(public_path().'/buyers_list.json');

        $data = json_decode($jsonString, true);

        $data[$search_id['search_id']]['buyer_name'] = $input['name'];

        $newJsonString = json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents((public_path().'/buyers_list.json'), stripslashes($newJsonString));

        Buyers::where("id", $id)
            ->update($input);

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $buyer = Buyers::find($id);

        $listing_search = SearchProposition::where('buyers_id', $id)->get();

        $token = Listing\TokenController::getToken();

        $listing_id = User::where('id', $buyer['user_id'])->first();

        $arr_listing = array(
            "token" => $token,
            'searchId' => $buyer['search_id'],
            'userId' => $listing_id['listing_id'],
        );

        $url = "https://slipstream.homejunction.com/ws/users/searches/remove?";

        $respons = Listing\QueriesController::getQuery($arr_listing, $url);

        $profile = Profile::where('user_id', $buyer['user_id'])->firstOrFail();
        $path = public_path() . '/upload/Listing/';
        foreach ($listing_search as $listing) {
            $filename = $listing['listing_id'] . '.jpg';
            if (File::exists($path . $filename)) {
                File::delete($path . $filename);
            }
        }
        $jsonString = file_get_contents(public_path().'/buyers_list.json');

        $data = json_decode($jsonString, true);

        unset($data[$buyer['search_id']]);
        $newJsonString = json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents((public_path().'/buyers_list.json'), stripslashes($newJsonString));

        $buyer->delete();
        $profile->buyers_left = $profile->buyers_left + 1;
        $profile->save();


        return response()->json();

    }

    public function getListing($token)
    {
        $buyer_listing = explode("_", $token);
        $id = Buyers::where('search_id', $buyer_listing[0])->first();
        $image = Image::where('user_id', $id['user_id'])->first();
        $profile = Profile::where('user_id', $id['user_id'])->first();
        $subscription = Subscriptions::where('user_id', $id['user_id'])->first();
        $profile['logo'] = $image['img'];
        $listing = $this->getListingResponse($id['id']);
        $url = "app.listing2text.com/listing-page/" . $token;

        //$listing_id = SearchProposition::where('buyers_id', $id['id'])->select('listing_id')->first();
        $listing_id = $buyer_listing[1];
        if ((!empty($listing))) {
            foreach ($listing as $item) {
                if (($listing_id == $item['id'])) {
                    $profile['listing'] = $item;
                }
            }
        } else {
            $profile['listing'] = false;
        }

        $profile['buyer'] = $id;
        $profile['showing'] = $subscription['plan_showing'];

        if ($id['notify_status'] == 1) {
            if($profile['method'] == 0) {
                $sid = env('TWILIO_ACCOUNT_SID');
                $token = env('TWILIO_AUTH_TOKEN');
                $from = env('TWILIO_NUMBER');

                $twilio = new Client($sid, $token);

                $text = $id['name'] . "  is looking at a listing sent on your behalf by Listin2text.com \n" . $url;

                $twilio->messages
                    ->create($profile['phone'], // to
                        array(
                            "body" => $text,
                            "from" => $from
                        )
                    );
            }
            else{
                $info = new \stdClass();
                $info->subject = "Listing viewing";
                $info->text = "Listing viewing";
                $info->token = $id['name'] . "  is looking at a listing sent on your behalf by Listin2text.com \n" . $url;

                Mail::to($profile['user_email'])->send(new RegisterMail($info));
            }
            $id['notify_status'] = 0;
            $id->save();
        }

        return response()->json($profile);
    }

    public function getListingResponse($id)
    {
        $all_list = array();


        $page = 1000;

        $buyers = Buyers::find($id);

        $polygon = array();


        $token = Listing\TokenController::getToken();

        $str = strtotime($buyers['created_at']);

        $arr_filters = array(
            "beds" => $buyers['min_beds'] . ":" . $buyers['max_beds'],
            "baths" => $buyers['min_baths'] . ":" . $buyers['max_baths'],
            "listPrice" => $buyers['min_price'] . ":" . $buyers['max_price'],
            "propertyType" => $buyers['type'],

        );
        if($buyers['city'] != null){
            $arr_filters['city'] = $buyers['city'];
        }

        if($buyers['location'] != null){
            $coords = explode(",", $buyers['location']);
            if($buyers['shape_type'] == 'polygon') {
                for($i = 0; $i < count($coords); $i+=2) {
                    $polygon[] = array($coords[$i+1], $coords[$i]);
                }
                $polygon[] = array($coords[1], $coords[0]);
                $marker = array(

                    'type' => 'Polygon',
                    'coordinates' => array(
                        $polygon
                    )

                );

                $arr_filters['polygon'] = json_encode($marker);
            }
            else if($buyers['shape_type'] == 'circle'){
                $arr_filters['circle'] = $coords[0]. ', '.$coords[1].', '.$coords[2];
            }
        }

        if ($buyers['street'] != null) {
            $arr_filters['street'] = $buyers['street'];
        }
        if ($buyers['subdivision'] != null) {
            $arr_filters['subdivision'] = $buyers['subdivision'];
        }
        if ($buyers['fireplace'] != false) {
            $arr_filters['fireplace'] = $buyers['fireplace'];
        }
        if ($buyers['closet'] != false) {
            $arr_filters['walkInCloset'] = $buyers['closet'];
        }
        if ($buyers['vault'] != false) {
            $arr_filters['vaultedCeiling'] = $buyers['vault'];
        }
        if ($buyers['master_bedroom'] != null) {
            $arr_filters['masterLocation'] = $buyers['master_bedroom'];
        }

        if ($buyers['min_parking'] != null && $buyers['max_parking'] == null) {
            $arr_filters['garageSpaces'] = ">=" . $buyers['min_parking'];
        } else if ($buyers['max_parking'] != null && $buyers['min_parking'] == null) {
            $arr_filters['garageSpaces'] = "<=" . $buyers['max_parking'];
        } else if ($buyers['max_parking'] != null && $buyers['min_parking'] != null) {
            $arr_filters['garageSpaces'] = $buyers['min_parking'] . ":" . $buyers['max_parking'];
        }

        if ($buyers['min_year'] != null && $buyers['max_year'] == null) {
            $arr_filters['yearbuilt'] = ">=" . $buyers['min_year'];
        } else if ($buyers['max_year'] != null && $buyers['min_year'] == null) {
            $arr_filters['yearbuilt'] = "<=" . $buyers['max_year'];
        } else if ($buyers['max_year'] != null && $buyers['min_year'] != null) {
            $arr_filters['yearbuilt'] = $buyers['min_year'] . ":" . $buyers['max_year'];
        }

        if ($buyers['min_floor'] != null && $buyers['max_floor'] == null) {
            $arr_filters['floorCount'] = ">=" . $buyers['min_floor'];
        } else if ($buyers['max_floor'] != null && $buyers['min_floor'] == null) {
            $arr_filters['floorCount'] = "<=" . $buyers['max_floor'];
        } else if ($buyers['max_floor'] != null && $buyers['min_floor'] != null) {
            $arr_filters['floorCount'] = $buyers['min_floor'] . ":" . $buyers['max_floor'];
        }

        if ($buyers['min_lot'] != null && $buyers['max_lot'] == null) {
            $arr_filters['lotsize.sqft'] = ">=" . $buyers['min_lot'];
        } else if ($buyers['max_lot'] != null && $buyers['min_lot'] == null) {
            $arr_filters['lotsize.sqft'] = "<=" . $buyers['max_lot'];
        } else if ($buyers['max_lot'] != null && $buyers['min_lot'] != null) {
            $arr_filters['lotsize.sqft'] = $buyers['min_lot'] . ":" . $buyers['max_lot'];
        }

        if ($buyers['min_living'] != null && $buyers['max_living'] == null) {
            $arr_filters['size'] = ">=" . $buyers['min_living'];
        } else if ($buyers['max_living'] != null && $buyers['min_living'] == null) {
            $arr_filters['size'] = "<=" . $buyers['max_living'];
        } else if ($buyers['max_living'] != null && $buyers['min_living'] != null) {
            $arr_filters['size'] = $buyers['min_living'] . ":" . $buyers['max_living'];
        }

        if ($buyers['status'] != null) {
            $arr_filters['status'] = $buyers['status'];
        }
        if ($buyers['keyword'] != null) {
            $arr_filters['keyword'] = $buyers['keyword'];
        }
        if ($buyers['county'] != null) {
            $arr_filters['county'] = $buyers['county'];
        }
        $arr_listing = array(
            "token" => $token,
            "details" => true,
            "images" => true,
            "market" => "carets",
            "pageSize" => $page,
            "sortOrder" => "desc",
            "sortField" => "listingDate",
            "features" => true

        );

        $all_array = array_merge($arr_filters, $arr_listing);

        $url = "https://slipstream.homejunction.com/ws/listings/search?";

        $respons = Listing\QueriesController::getQuery($all_array, $url);


        for ($i = 0; $i < $respons['result']['total']; $i++) {

            $result = $str - $respons['result']['listings'][$i]['modifiedDate'];

            if (($result < 1500) && ($respons['result']['listings'][$i]['listingType'] == "Residential") && (!empty($respons['result']['listings'][$i]['images']))) {
                $all_list[] = $respons['result']['listings'][$i];
            }
        }
        if (!empty($all_list)) {
            $listing = SearchProposition::where('buyers_id', $id)->select('listing_id')->first();

            if ($listing == null) {
                SearchProposition::create([
                    "buyers_id" => $id,
                    "listing_id" => $all_list[0]['id'] - 1
                ]);
            }
            return $all_list;
        }
    }


    public function unSubscribe($id)
    {
//        Buyers::where('search_id', $id)->update([
//            'sms_status' => 0
//        ]);
        $buyer = Buyers::where('search_id', $id)->first();
        $profile = Profile::where('user_id', $buyer['user_id'])->first();
        $info = new \stdClass();
        $info->subject = "Buyer unsubscribtion";
        $info->text = "Your buyer has opted usubscribe!";
        $info->token = $buyer['name'] . " has opted unsubscribe <br />".'<br/>Buyer phone: ' . $buyer['phone'] . '<br />Buyer email: ' . $buyer['email'];

        Mail::to($profile['user_email'])->send(new RegisterMail($info));

        $token = Listing\TokenController::getToken();

        $listing_id = User::where('id', $buyer['user_id'])->first();

        $arr_listing = array(
            "token" => $token,
            'searchId' => $buyer['search_id'],
            'userId' => $listing_id['listing_id'],
        );

        $url = "https://slipstream.homejunction.com/ws/users/searches/remove?";

        $respons = Listing\QueriesController::getQuery($arr_listing, $url);

        $profile = Profile::where('user_id', $buyer['user_id'])->firstOrFail();
        $path = public_path() . '/upload/';
        $filename = $buyer['search_id'] . '.jpg';
        if (File::exists($path . $filename)) {
            File::delete($path . $filename);
        }
        $jsonString = file_get_contents(public_path().'/buyers_list.json');

        $data = json_decode($jsonString, true);

        unset($data[$buyer['search_id']]);
        $newJsonString = json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents((public_path().'/buyers_list.json'), stripslashes($newJsonString));

        $buyer->delete();
        $profile->buyers_left = $profile->buyers_left + 1;
        $profile->save();

        return response()->json();
    }

    public function sendRequest(Request $request)
    {
        $date = date('m-d-Y', strtotime($request['date']));
        $time = $request['time'];
        $info = new \stdClass();
        $info->subject = "Showing Request";
        $info->text = "Showing Request";
        $info->token = $request['buyer_name'] . " has requested a showing of <br />" . $request['buyer_property'] . '<br />on ' . $date . '<br />at ' . $time . '.<br/>Phone: ' . $request['buyer_phone'] . '<br />Email: ' . $request['buyer_email'];

        Mail::to($request['user_email'])->send(new RegisterMail($info));
    }

    public function getCities()
    {
        $token = Listing\TokenController::getToken();


        $arr_listing = array(
            "token" => $token,
            "pageSize" => 1000,
            "county" => 'Ventura'

        );

        $city = array();

        $url = "https://slipstream.homejunction.com/ws/areas/neighborhoods/search?";

        $respons = Listing\QueriesController::getQuery($arr_listing, $url);

        foreach($respons['result']['neighborhoods'] as $cities){
            preg_match('/\, (.*?)\, /', $cities['label'], $match);
            $city[] = $match[1];
        }

        $result = array_unique($city);

        return response()->json($result);
    }

    public function getSubdivision($city)
    {
        $token = Listing\TokenController::getToken();


        $arr_listing = array(
            "token" => $token,
            "pageSize" => 1000,
            "postalCity" => $city

        );

        $sub = array();

        $url = "https://slipstream.homejunction.com/ws/areas/neighborhoods/search?";

        $respons = Listing\QueriesController::getQuery($arr_listing, $url);

        foreach($respons['result']['neighborhoods'] as $subdivision){
            $sub[] = $subdivision['name'];
        }

        return response()->json($sub);
    }
}
