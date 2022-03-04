<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use \Symfony\Component\HttpFoundation\Response;//webステータスコード

/* LINE関係 */
use Illuminate\Support\Str;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;


class lineController extends Controller
{
    //
    public function reMessage(REQUEST $request){
        return response()->json(['test'=>'testMessage'],200);
    }

    public function mytles(REQUEST $request){

        return response()->json(['mytle'=>'mytle'],200);
    }
}
