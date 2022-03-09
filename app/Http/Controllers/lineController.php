<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use \Symfony\Component\HttpFoundation\Response;//webステータスコード

/* LINE関係 */
use Illuminate\Support\Str;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

//events
use App\Events\mytleRepeat;

class lineController extends Controller
{
    //
    public function reMessage(REQUEST $request){
        return response()->json(['test'=>'testMessage'],200);
    }

    public function mytles(REQUEST $request){

        //トークン関係初期化
        $channel_secret = env('LINE_CHANNEL_SECRET');
        $access_token = env('LINE_ACCESS_TOKEN');

        //LINE-OBJECTを作成
        $client = new CurlHTTPClient($access_token);
        $bot = new LINEBot($client, ['channelSecret' => $channel_secret]);

        //配列変換
        $values=json_decode(json_encode($request->all()),true);

        //対応イベント
        event(new mytleRepeat($values,$bot));

        return 0;
    }




}
