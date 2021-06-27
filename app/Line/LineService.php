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
        if($userData['status']== false){
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
                return $this->_sendMessage($user->line_id, 'text', $this->builder->notSupportedMessage());
            case 'audio':
                return $this->_sendMessage($user->line_id, 'text', $this->builder->notSupportedMessage());
            case 'video':
                return $this->_sendMessage($user->line_id, 'text', $this->builder->notSupportedMessage());
            case 'sticker':
                return $this->_sendMessage($user->line_id, 'text', $this->builder->notSupportedMessage());
            case 'postback':
                return $this->_postbackProcess($events, $user);
            default:
                return $this->_sendMessage($user->line_id, 'text', $this->builder->notSupportedMessage());
        }
    }

    private function _postbackProcess($events, $user){
        $messageData    = $this->_identityMessage($events);
        if($messageData['data'] == false){
            return $messageData;
        }

        $postback   = $messageData['data'];
        if($postback == 'searching'){
            // find available room or create it if no one
            $findJoinRoom  = $this->room->createJoinRoom($user->line_id);

            if($findJoinRoom['is_create'] === true){
                // create new room, need to wait the opponent
                $messageNewRoom     = $this->builder->createRoom();
                $this->_sendMessage($user->line_id, 'text', $messageNewRoom);

                $count              = 0;
                for($count =0; $count <5; $count++){
                    // Waiting 5 loop time to get opponent, if joined it will return
                    $room               = $this->room->findById($findJoinRoom->id);
                    if($room->b_line_id == '' AND is_null($room->leave_at)){
                        sleep(10);
                        if($count > 5){
                            $this->room->leaveChat($room->id);
                            $this->_sendMessage($user->line_id, 'text', $this->builder->opponentNotFound());
                            sleep(1);

                            return $this->_sendMessage($user->line_id, 'builder', $this->builder->introButton());
                        }
                        return $this->_sendMessage($user->line_id, 'text', $this->builder->randomWait());
                    }else{
                        return [
                            'status'    => true,
                            'message'   => 'Joined successfully'
                        ];
                    }
                }
            }

            // Joined available room
            $joinedMsg  = $this->builder->gotAFriend();
            $this->_sendMessage($user->line_id, 'confirm', $joinedMsg);

            // get opponent ID
            $opponentLineId = ($findJoinRoom->a_line_id == $user->line_id) ? $findJoinRoom->b_line_id : $findJoinRoom->a_line_id;

            // send message to opponent too
            return $this->_sendMessage($opponentLineId, 'confirm', $joinedMsg);
        }

        return $this->_sendMessage($user->line_id, 'text', $this->builder->nothingReply());
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
        $isActiveRoom   = $this->room->findActiveRoom($user->line_id);
        if($isActiveRoom){
            // get opponent ID
            $opponentLineId = ($isActiveRoom->a_line_id == $user->line_id) ? $isActiveRoom->b_line_id : $isActiveRoom->a_line_id;

            if(in_array($message, ['/leave','/exit','/keluar','leave','exit','keluar'])){
                // Leave chat room
                $this->room->leaveChat($isActiveRoom->id);

                // send message self
                $this->_sendMessage($user->line_id, 'text', 'Anda telah keluar dari chat room');

                // leave message to opponent
                $this->_sendMessage($opponentLineId, 'text', 'Yahhh teman chat kamu sudah keluar, coba buat chat room random lagi aja ya!');

                sleep(1);
                $this->_sendMessage($user->line_id, 'builder', $this->builder->introButton());
                $this->_sendMessage($opponentLineId, 'builder', $this->builder->leaveButton());

                return [
                    'status'    => true,
                    'message'   => 'leave chat success'
                ];
            }

            // insert chat from original message
            $this->chat->createChat($isActiveRoom->id, $user->line_id, $replyToken, $type, $message);

            // send original message to opponent
            return $this->_sendMessage($opponentLineId, $type, $message);
        }

        // if not in active room and new user
        return $this->_sendMessage($user->line_id, 'builder', $this->builder->introButton());
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
            $type   = $events[0]['type'];

            switch ($type){
                case 'text':
                    return [
                        'status'    => true,
                        'type'      => 'text',
                        'data'      => $events[0]['message']['text'],
                        'replyToken'    => $events[0]['replyToken']
                    ];
                case 'postback':
                    return [
                        'status'    => true,
                        'type'      => 'postback',
                        'data'      => $events[0]['postback']['data'],
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
            /*if($events[0]['type'] !== 'message'){
                return [
                    'status'    => false,
                    'message'   => "type not a message"
                ];
            }*/

            if(empty($events[0]['source']['type'])){
                return [
                    'status'    => false,
                    'message'   => "type undefined"
                ];
            }

            $type   = $events[0]['source']['type'];

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

    private function _sendMessage($lineId, $type, $message){
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(env('LINE_CHANNEL_ACCESS_TOKEN'));
        $bot        = new \LINE\LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);

        $response   = $bot->pushMessage($lineId, $message);


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
