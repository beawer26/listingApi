<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchProposition extends Model
{
    protected $fillable = [
        "buyers_id",
        "search_id",
        "listing_id"
    ];
}
