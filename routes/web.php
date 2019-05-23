<?php

Route::get('/', function (){
    return view('welcome');
});

/* Register */
Route::post('register', 'RegisterController@create')->name('register');
Route::post('emails', 'RegisterController@getEmails')->name('emails');
Route::post('check-card', 'RegisterController@checkCard');
Route::post('password/email','RegisterController@sendResetEmail');
Route::post('password/reset','RegisterController@resetPassword');

/* Plans */
Route::get('plans', 'PlansListController@show')->name('plans');
Route::post('add-plan', 'PlansListController@create')->name('add-plan');
Route::post('edit-plan', 'PlansListController@edit')->name('edit-plan');
Route::delete('delete-plan/{id}', 'PlansListController@destroy')->name('delete-plan');
Route::get('get-plan/{id}', 'PlansListController@getPlanById')->name('get-plan');

/* Profile */
Route::get('profiles/{id}', 'ProfileController@show');
Route::post('profile/{id}', 'ProfileController@update');
Route::get('payment/{id}', 'ProfileController@showPayment');
Route::post('old-pass', 'ProfileController@checkOldPassword');
Route::post('change-pass', 'ProfileController@changePassword');



/* Buyers */
Route::post('buyers', 'BuyersController@create')->name('buyers');
Route::get('buyers/{id}', 'BuyersController@show');
Route::get('get-buyer/{id}', 'BuyersController@getBuyer');
Route::post('buyers-delete/{id}', 'BuyersController@destroy');
Route::post('edit-buyer/{id}', 'BuyersController@update');
Route::get('get-listing/{token}', 'BuyersController@getListing');
Route::post('unsubscribe/{id}', 'BuyersController@unSubscribe');
Route::post('send-request', 'BuyersController@sendRequest');
Route::get('get-cities', 'BuyersController@getCities');
Route::get('get-subdivision/{city}', 'BuyersController@getSubdivision');


/* User */
Route::get('get-users', 'UserController@show')->name('get-users');
Route::post('user-delete', 'UserController@destroy');
Route::post('check-status/{id}', 'UserController@checkStatus');


/* Charge */
Route::get('billing-history', 'ChargeController@showBilling')->name('billing-history');

/* Subscribe */
Route::get('customer-billing/{id}', 'SubscribeController@getSubscribtion');
Route::post('sub-update/{id}', 'SubscribeController@update')->name('sub-update');
Route::get('suspend-subscription/{id}', 'SubscribeController@suspend')->name('suspend-subscription');
Route::post('end-sub/{id}', 'SubscribeController@endSubscribtion')->name('end-sub');
Route::post('card-update/{id}', 'SubscribeController@cardUpdate')->name('card-update');

/* Test */
Route::get('test', 'TestController@test');
Route::get('verify/{token}', 'Auth\LoginController@verify');


