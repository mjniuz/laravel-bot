<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Log;
use Illuminate\Routing\Controller as BaseController;

class StartController extends BaseController
{
    public function __construct()
    {

    }

    public function start(Request $request){
        Log::critical('wew');
    }
}
