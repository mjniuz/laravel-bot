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
        $events     = $request->get('events');
        $iMessage   = $this->line->start($events);
        if($iMessage['status'] !== true){
            Log::critical(json_encode([$events, $iMessage]));
        }

        return response()->json([
            'status'    => 'ok'
        ]);
    }
}
