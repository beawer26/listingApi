<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'stripe_id',
        'stripe_plan',
        'plan_buyers',
        'plan_sms',
        'plan_notification',
        'plan_showing',
        'plan_trial',
        'current_period_start',
        'current_period_end',
        'status',
        'sub_id'
    ];
}
