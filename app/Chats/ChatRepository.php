<?php
namespace App\Chats;

class ChatRepository{
    public function findLastChatLineUserId($lineUserId = null){
        return Chats::with([])
            ->where('line_id', $lineUserId)
            ->orderBy('id','desc')
            ->first();
    }

    public function createChat($roomId, $lineUserId, $replyToken, $type, $message){
        $chat   = new Chats();
        $chat->room_id      = $roomId;
        $chat->line_id      = $lineUserId;
        $chat->reply_token  = $replyToken;
        $chat->type         = $type;
        $chat->message      = $message;
        $chat->save();

        return $chat;
    }
}
