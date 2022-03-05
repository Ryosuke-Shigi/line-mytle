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
        //初期化
        $words=array('猫','パンダ','犬');
        $voices=array('にゃ～ん','笹食ってる場合じゃねぇ！','ワンワンイヌドッグ！');
        $keyword="";
        $voice="";


        //トークン関係初期化
        $channel_secret = env('LINE_CHANNEL_SECRET');
        $access_token = env('LINE_ACCESS_TOKEN');

        // メッセージからreplyTokenを取得
        $inputs=json_decode(json_encode($request->all()),true);
        $reply_token=$inputs['events'][0]['replyToken'];

        //LINE-OBJECTを作成
        $client = new CurlHTTPClient($access_token);
        $bot = new LINEBot($client, ['channelSecret' => $channel_secret]);

        foreach($words as $key=>$index){
            if(strpos($inputs['events'][0]['message']['text'],$index)){
                $voice=$voices[$key];
                break;
            }
        }

        //メッセージ送信
        if($voice == ""){
            $bot->replytext($reply_token,$voice.$inputs['events'][0]['message']['text']." なんだなっ！");
            return $voice.$inputs['events'][0]['message']['text']." なんだなっ！";
        }else{
            $bot->replytext($reply_token,$voice);
            return $voice;
        }

        return 0;

    }




}
