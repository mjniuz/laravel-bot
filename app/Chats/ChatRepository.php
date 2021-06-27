<?php
namespace App\Chats;

class ChatRepository{
    public function findLastChatLineUserId($roomId = null, $lineUserId = null){
        return Chats::with([])
            ->where('room_id', $roomId)
            ->where('line_id', $lineUserId)
            ->orderBy('id','desc')
            ->first();
    }

    public function createChat($roomId, $lineUserId, $replyToken, $type, $message){
        $chat   = new Chats();
        $chat->room_id      = $roomId;
        $chat->line_id      = $lineUserId;
        $chat->reply_token  = $replyToken;
        $chat->message_type = $type;
        $chat->message      = $message;
        $chat->save();

        return $chat;
    }
}
