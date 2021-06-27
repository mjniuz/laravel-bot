<?php

namespace App\Line;

class MessageBuilderService{
    public function notSupportedMessage(){
        $text   = "Sorry untuk saat ini hanya bisa ngirim pesan teks biasa aja...";
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text);

        return $msgResponse;
    }

    public function opponentNotFound(){
        $text   = "Yahh... sorry nih kamu ga dapetin temen curcol random, coba lagi nanti yah...";
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text);

        return $msgResponse;
    }

    public function randomWait(){
        $items   = [
            'Tunggu yah, aku masih nyariin temen kesepian kyk kamu juga, bentar lagi kok...',
            'Temen curcolmu belum ketemu nih, tapi tunggu ya masih aku cariin, biar kamu ga kesepian...',
            'Aku ngerti kamu lagi kesepian kok, jadi tunggu yah aku masih nyari temen buat kamu...',
            'Temen randommu belum nongol, masih aku usahain cari ya, tggu bentaran aja...',
            'Masih nungguin kamu dapet temen curcol nih, beberapa saat lagi ya...'
        ];

        $text   = $items[array_rand($items)];
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text);

        return $msgResponse;

    }

    public function gotAFriend(){
        // Confirm
        $title          = "Yey, kamu udh dapet temen random, mulai ngobrol aja. Balas /leave untuk keluar";
        $actions        = [
            [
                'type'  => 'message',
                'label' => 'Say Hello!',
                'text'  => 'Hi, salam kenal'
            ],
            [
                'type'  => 'message',
                'label' => 'Kenalin diri',
                'text'  => 'Hi... nih gue orang yg baik loh'
            ]
        ];
        $postbackAction = [];
        foreach ($actions as $action){
            $postbackAction[] = new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder($action['label'],$action['text']);
        }
        $buildTemplate  = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder($title, $actions);
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($title,$buildTemplate);

        return $msgResponse;
    }

    public function createRoom(){
        $title          = "Kamu udh sukses bikin room random, sekarang cukup nungguin si dia join ke room kamu...";
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($title);

        return $msgResponse;
    }

    public function nothingReply(){
        $title          = "Okay kita santai dulu, nanti kalau mau cari temen curcol random, chat aku lagi aja ya...";
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($title);

        return $msgResponse;
    }

    public function friendLeaved(){
        $title          = 'Yahhh teman chat kamu sudah keluar, coba buat chat room random lagi aja ya!';
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($title);

        return $msgResponse;
    }

    public function stillWaiting(){
        $title          = "Aku masih cariin temen random buat kamu yah, tunggu aja, kalau mau keluar dan cari temen random baru cukup balas /leave";
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($title);

        return $msgResponse;
    }

    public function leaveReminder(){
        $title          = "Kamu masih chat sama temen randommu. Cuman ingetin, cukup balas /leave untuk keluar dan cari temen random baru";
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($title);

        return $msgResponse;
    }

    public function introButton(){
        $title          = "Cari temen random mu!";
        $text           = "Curcol ga harus ke sahabat, sama temen random juga bisa!";
        $image          = 'https://storage.googleapis.com/storage.kickrate.com/wp-content/uploads/2021/06/26154129/pic.php_.jpg';
        $actions        = [
            [
                'type'  => 'postback',
                'label' => 'Mulai cari',
                'data'  => 'searching'
            ],
            [
                'type'  => 'postback',
                'label' => 'Nanti aja',
                'data'  => 'nothing'
            ]
        ];
        $imgRatio       = 'rectangle';
        $imageSize      = 'cover';
        $imageBackgroundColor   = '#FFFFFF';
        $postbackAction = [];
        foreach ($actions as $action){
            $postbackAction[] = new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder($action['label'],$action['data']);
        }
        $buildTemplate  = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder($title, $text, $image, $postbackAction, $imgRatio, $imageSize, $imageBackgroundColor);
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($title,$buildTemplate);

        return $msgResponse;
    }

    public function leaveButton(){
        $title          = "Yahh udah keluar dia!";
        $text           = "Temen randommu udh keluar nih, mau cari lainya?";
        $image          = 'https://storage.googleapis.com/storage.kickrate.com/wp-content/uploads/2021/06/26155757/218e3b5d-5ea5-4da0-96a9-c13321e3d670.__CR590661661_PT0_SX300_V1___.jpg';
        $actions        = [
            [
                'type'  => 'postback',
                'label' => 'Mulai cari',
                'data'  => 'searching'
            ],
            [
                'type'  => 'postback',
                'label' => 'Nanti aja',
                'data'  => 'nothing'
            ]
        ];
        $imgRatio       = 'rectangle';
        $imageSize      = 'cover';
        $imageBackgroundColor   = '#FFFFFF';
        $postbackAction = [];
        foreach ($actions as $action){
            $postbackAction[] = new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder($action['label'],$action['data']);
        }
        $buildTemplate  = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder($title, $text, $image, $postbackAction, $imgRatio, $imageSize, $imageBackgroundColor);
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($title,$buildTemplate);


        return $msgResponse;
    }
}
