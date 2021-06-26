<?php

namespace App\Chats;

use Illuminate\Database\Eloquent\Model;

class Chats extends Model
{
    protected $table = 'chats';
    protected $guarded = ['id'];

    public function room()
    {
        return $this->belongsTo('App\Rooms\Rooms');
    }

    public function user()
    {
        return $this->belongsTo('App\Users\Users');
    }
}
