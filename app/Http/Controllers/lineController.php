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

        // LINEBOTSDKの設定
        // LINEから送られた内容を$inputsに代入
        //return json_decode(json_encode($request->all()),true);
/*         $inputs=json_decode(json_encode($request->all()),true);

        // そこからtypeをとりだし、$message_typeに代入
        $message_type=$inputs['events'][0]['type'];

        // メッセージが送られた場合、$message_typeは'message'となる。その場合処理実行。
        if($message_type=='message') {

            // replyTokenを取得
            $reply_token=$inputs['events'][0]['replyToken'];

            // LINEBOTSDKの設定
            $http_client = new CurlHTTPClient(config('services.line.accessToken'));
            $bot = new LINEBot($http_client, ['channelSecret' => config('services.line.channelSecret')]);

            // 送信するメッセージの設定
            $reply_message='メッセージありがとうございます';

            // ユーザーにメッセージを返す
            $reply=$bot->replyText($reply_token, $reply_message);

            return 'ok';
        }
 */

        $channel_secret = env('LINE_CHANNEL_SECRET');
        $access_token = env('LINE_ACCESS_TOKEN');

        // replyTokenを取得
        $inputs=json_decode(json_encode($request->all()),true);
        $reply_token=$inputs['events'][0]['replyToken'];

        $client = new CurlHTTPClient($access_token);
        $bot = new LINEBot($client, ['channelSecret' => $channel_secret]);

        $bot->replytext($reply_token,$inputs['events'][0]['message']['text']);

        return $inputs['events'][0]['message']['text']." なんだなっ！";


    }




}
