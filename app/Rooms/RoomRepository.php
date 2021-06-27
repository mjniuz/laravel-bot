<?php
namespace App\Rooms;

use App\Line\LineService;

class RoomRepository{
    public function findById($id = null){
        return Rooms::with([])
            ->find($id);
    }

    public function findAvailableRoom($lineUserId){
        $availableRoom  = Rooms::with([])
            ->whereNull('leave_at')
            ->where('a_line_id', '<>', $lineUserId)
            ->where('b_line_id','=','')
            ->orderBy('id','asc')
            ->first();

        return $availableRoom;
    }

    public function findActiveRoom($lineUserId){
        $activeRoom  = Rooms::with([])
            ->whereNull('leave_at')
            ->where(function($query)use($lineUserId){
                $query->where('a_line_id', $lineUserId)
                    ->orWhere('b_line_id', $lineUserId);
            })
            ->where('a_line_id', '<>', '')
            ->where('b_line_id','<>', '')
            ->orderBy('id','desc')
            ->first();

        return $activeRoom;
    }

    public function createJoinRoom($lineUserId){
        $isCreate       = false;
        $availableRoom  = $this->findAvailableRoom($lineUserId);
        if($availableRoom){
            $availableRoom->b_line_id   = $lineUserId;
            $availableRoom->save();
        }else{
            $availableRoom            = new Rooms();
            $availableRoom->a_line_id = $lineUserId;
            $availableRoom->b_line_id = '';
            $availableRoom->leave_at  = null;
            $availableRoom->save();

            $isCreate       = true;
        }

        return [
            'status'    => true,
            'is_create' => $isCreate,
            'data'      => $availableRoom
        ];
    }

    public function leaveChat($roomId = null){
        $room   = $this->findById($roomId);
        if(!$room){
            return false;
        }

        $room->leave_at     = date("Y-m-d H:i:s");
        $room->save();
    }
}
