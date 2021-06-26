<?php

namespace App\Rooms;

use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    protected $table = 'rooms';
    protected $guarded = ['id'];

    public function a_user()
    {
        return $this->belongsTo('App\Users\Users');
    }

    public function b_user()
    {
        return $this->hasOne('App\Users\Users');
    }
}
