<?php

namespace App\Line;

class MessageBuilderService{
    public function gotAFriend(){
        $title          = "Yey, kamu udh dapet temen random, mulai ngobrol aja. Balas /leave untuk keluar";
        $actions        = [
            [
                'type'  => 'message',
                'label' => 'Say Hello!',
                'text'  => 'Hi, salam kenal, gue mau curhat nih'
            ],
            [
                'type'  => 'message',
                'label' => 'Kenalin diri',
                'text'  => 'Hi... nih gue orang yg baik loh, mau jadi temen curcol ga?'
            ]
        ];
        $buildTemplate  = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder($title, $actions);
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($title,$buildTemplate);

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
        $buildTemplate  = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder($title, $text, $image, $actions, $imgRatio, $imageSize, $imageBackgroundColor);
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
        $buildTemplate  = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder($title, $text, $image, $actions, $imgRatio, $imageSize, $imageBackgroundColor);
        $msgResponse    = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($title,$buildTemplate);


        return $msgResponse;
    }
}
