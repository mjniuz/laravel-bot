<?php
namespace App\Line;

class LineService{

    public function identityEvent($events = []){
        if($this->_validateEvents($events) !== true){
            return $this->_validateEvents($events);
        }

        if(!empty($events[0]['type'])){
            /**
             * Type:
             * follow
             * unfollow
             * message
             * image
             * audio
             * video
             * sticker
             */
            return $events[0]['type'];
        }

        return false;
    }

    public function identityMessage($events = []){
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

    public function identityUser($events = []){
        if($this->_validateEvents($events) !== true){
            return $this->_validateEvents($events);
        }

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
}
