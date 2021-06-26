<?php
namespace App\Line;

class MessageService{
    public function identity($events = []){
        if(!is_array($events)){
            return [
                'status'    => false,
                'message'   => "not an event"
            ];
        }


    }
}
