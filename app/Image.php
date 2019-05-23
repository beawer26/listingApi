<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';

    protected $fillable = [
        'img',
        'user_id'
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }
}
