<?php
namespace App\Users;

use App\Line\LineService;

class UserRepository{
    public function findByLineUserId($lineUserId = null){
        return Users::with([])->where('line_id', $lineUserId)->first();
    }

    public function createUpdateUser($lineUserId){
        $user   = $this->findByLineUserId($lineUserId);
        if(!$user){
            // call api
            $line   = new LineService();
            $userDetail     = $line->userInformation($lineUserId);
            if($userDetail !== false){
                $newUser            = new Users();
                $newUser->line_id   = $lineUserId;
                $newUser->name      = $userDetail['name'];
                $newUser->picture   = $userDetail['picture'];
                $newUser->save();

                return $newUser;
            }else{
                return false;
            }
        }

        return $user;
    }

    public function openCloseChat($lineUserId = null, $isOpen = 0){
        $user   = $this->findByLineUserId($lineUserId);
        if(!$user){
            return false;
        }

        $user->is_open_chat     = $isOpen ? date("Y-m-d H:i:s") : null;
        $user->save();
    }
}
