<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'phone',
        'mobile',
        'website',
        'address',
        'city',
        'state',
        'zip',
        'logo',
        'company',
        'stripe_plan',
        'buyers_left',
        'sms_left',
        'license'
    ];

    public function user(){
        return $this->belongsToMany(User::class);
    }
    public function image(){
        return $this->belongsTo(Image::class);
    }

}
