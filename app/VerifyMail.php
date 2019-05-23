<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerifyMail extends Model
{
    protected $fillable =[
        "user_id",
        "token"
    ];
}
