<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlansList extends Model
{
    protected $fillable = [
        'plan_id',
        'plan_name',
        'plan_amount',
        'plan_description',
        'plan_sms',
        'plan_buyers',
        'plan_notification',
        'plan_showing',
        'plan_trial'
    ];
}
