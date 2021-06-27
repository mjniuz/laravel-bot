<?php
namespace App\Line;

use App\Chats\ChatRepository;
use App\Rooms\RoomRepository;
use App\Users\UserRepository;

class LineService{
    protected $user, $room, $chat, $builder;
    public function __construct()
    {
        $this->user = new UserRepository();
        $this->room = new RoomRepository();
        $this->chat = new ChatRepository();
        $this->builder  = new MessageBuilderService();
    }

    public function start($events = []){
        if($this->_validateEvents($events) !== true){
            return $this->_validateEvents($events);
        }

        // Check user detail
        $userData   = $this->_identityUser($events);
        if($userData['data']== false){
            return $userData;
        }
        $user       = $this->user->createUpdateUser($userData['data']);
        if($user === false){
            return [
                'data'      => false,
                'message'   => 'something wrong with user'
            ];
        }

        $eventName  = $this->_identityEvent($events);
        switch ($eventName){
            case 'follow':
                break;
            case 'unfollow':
                break;
            case 'message':
                return $this->_processMessage($events, $user);
            case 'image':
                break;
            case 'audio':
                break;
            case 'video':
                break;
            case 'sticker':
                break;
            case 'postback':
                break;
        }
    }

    private function _processMessage($events, $user){
        $messageData    = $this->_identityMessage($events);
        if($messageData['data'] == false){
            return $messageData;
        }

        $message    = $messageData['data'];
        $replyToken = $messageData['replyToken'];
        $type       = $messageData['type'];

        // Is in active room mode
        $isActiveRoom   = $this->findActiveRoom($user->line_id);
        if($isActiveRoom){
            // get opponent chat
            $opponentLineId = ($isActiveRoom->a_line_id == $user->line_id) ? $isActiveRoom->b_line_id : $isActiveRoom->a_line_id;

            // Find latest opponent replyToken
            $latestChat     = $this->chat->findLastChatLineUserId($isActiveRoom->id, $opponentLineId);
            if(!$latestChat){
                return [
                    'status'    => false,
                    'message'   => 'something wrong in latest opponent chat not found ' .  json_encode($events)
                ];
            }

            if(in_array($message, ['/leave','/exit','/keluar','leave','exit','keluar'])){
                // Leave chat room
                $this->room->leaveChat($isActiveRoom->id);

                // send message self
                $this->_sendMessage($replyToken, 'text', 'Anda telah keluar dari chat room');

                // leave message to opponent
                $this->_sendMessage($latestChat->reply_token, 'text', 'Yahhh teman chat kamu sudah keluar, coba buat chat room random lagi aja ya!');

                sleep(1);
                $this->_sendMessage($replyToken, 'builder', $this->builder->introButton());
                $this->_sendMessage($latestChat->reply_token, 'builder', $this->builder->leaveButton());

                return [
                    'status'    => true,
                    'message'   => 'leave chat success'
                ];
            }

            // insert chat from original message
            $this->chat->createChat($isActiveRoom->id, $user->line_id, $replyToken, $type, $message);

            // send original message to opponent
            return $this->_sendMessage($latestChat->reply_token, $type, $message);
        }
        return true;
    }

    private function _identityEvent($events = []){

        if(!empty($events[0]['type'])){
            return $events[0]['type'];
        }

        return false;
    }

    private function _identityMessage($events = []){
        if($this->_validateEvents($events) !== true){
            return $this->_validateEvents($events);
        }

        if(count($events) == 1 AND !empty($events[0]['type'])){
            $type   = $events[0]['message']['type'];

            switch ($type){
                case 'text':
                    return [
                        'status'    => true,
                        'type'      => 'text',
                        'data'      => $events[0]['message']['text'],
                        'replyToken'    => $events[0]['replyToken']
                    ];

                default:
                    return [
                        'status'    => false,
                        'message'   => "type undetected"
                    ];
            }
        }


        return [
            'status'    => false,
            'message'   => "more than 1 events " . json_encode($events)
        ];
    }

    private function _identityUser($events = []){
        if(count($events) == 1 AND !empty($events[0]['type'])){
            if($events[0]['type'] !== 'message'){
                return [
                    'status'    => false,
                    'message'   => "type not a message"
                ];
            }

            $type   = $events[0]->source->type;

            switch ($type){
                case 'user':
                    return [
                        'status'    => true,
                        'type'      => 'user',
                        'data'      => $events[0]['source']['userId']
                    ];

                default:
                    return [
                        'status'    => false,
                        'message'   => "type user undetected"
                    ];
            }
        }


        return [
            'status'    => false,
            'message'   => "more than 1 events " . json_encode($events)
        ];
    }

    public function userInformation($lineUserId = null){
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(env('LINE_CHANNEL_ACCESS_TOKEN'));
        $bot        = new \LINE\LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);
        $response = $bot->getProfile($lineUserId);

        if ($response->isSucceeded()) {
            $profile    = $response->getJSONDecodedBody();

            return [
                'name'      => $profile['displayName'],
                'picture'   => $profile['pictureUrl'],
                'status'    => $profile['statusMessage']
            ];
        }

        return false;
    }


    private function _validateEvents($events = []){
        if(!is_array($events)){
            return [
                'status'    => false,
                'message'   => "is not a valid events"
            ];
        }

        return true;
    }

    private function _sendMessage($replyToken, $type, $message){
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(env('LINE_CHANNEL_ACCESS_TOKEN'));
        $bot        = new \LINE\LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);


        if($type == 'builder'){
            $message    = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('', $message);
        }

        $response   = $bot->replyText($replyToken, $message);


        if ($response->isSucceeded()) {
            return [
                'status'    => true,
                'message'   => 'success'
            ];
        }

        return [
            'status'    => false,
            'message'   => $response->getHTTPStatus() . ' ' . $response->getRawBody()
        ];
    }
}
