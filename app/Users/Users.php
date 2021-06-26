<?php

namespace App\Users;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    protected $guarded = ['id'];


    public function quiz()
    {
        return $this->hasMany('App\Quiz\QuizHistory','user_id','id');
    }

    public function getProfilePictureUrlAttribute(){
        $mediaURL   = env('MEDIA_URL', false);
        if($this->source != ""){
            return $mediaURL . '/image/' . $this->source . '/' . $this->profile_picture;
        }

        return $mediaURL . '/image/' . $this->profile_picture;
    }
}
