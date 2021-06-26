<?php

namespace App\Http\Controllers;

use App\Line\LineService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Log;
use Illuminate\Routing\Controller as BaseController;

class StartController extends BaseController
{
    protected $line;
    public function __construct(LineService $line)
    {
        $this->line = $line;
    }

    public function start(Request $request){
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(env('LINE_CHANNEL_ACCESS_TOKEN'));
        $bot        = new \LINE\LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);

        $events     = $request->get('events');
        Log::critical(json_encode($events[0]['type']));
        $iMessage   = $this->line->identityMessage($events);
        if($iMessage['status'] !== true){
            Log::critical(json_encode([$events[0]['type'], $iMessage]));
        }

        $response   = $bot->replyText($iMessage['replyToken'], 'hello! ' . $iMessage['data']);

        return response()->json([
            'status'    => 'ok'
        ]);
    }
}
