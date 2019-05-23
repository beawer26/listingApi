<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Buyers extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'min_price',
        'max_price',
        'min_beds',
        'max_beds',
        'min_baths',
        'max_baths',
        'type',
        'city',
        'search_id',
        'search_name',
        'street',
        'subdivision',
        'location',
        'shape_type',
        'fireplace',
        'closet',
        'vault',
        'min_parking',
        'max_parking',
        'master_bedroom',
        'min_year',
        'max_year',
        'min_lot',
        'max_lot',
        'min_living',
        'max_living',
        'min_floor',
        'max_floor',
        'status',
        'keyword',
        'county'
    ];

    public function listId()
    {
        return $this->hasOne(User::class, 'listing_id');
    }
}
