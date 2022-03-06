<?php

namespace App\Listeners;

use App\Events\mytleRepeat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/* LINE関係 */
use Illuminate\Support\Str;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;

class talkRepeat
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    public $words;
    public $voices;

    public function __construct()
    {
        //
        $this->words=array('猫','パンダ','犬','タスマニアデビル');
        $this->voices=array('にゃ～ん','笹食ってる場合じゃねぇ！','ワンワンイヌドッグ！','たすまにぁーんよいとこー');
    }

    /**
     * Handle the event.
     *
     * @param  mytleRepeat  $event
     * @return void
     */
    public function handle(mytleRepeat $event)
    {
        //$event    //eventsの変数を扱える
        //変数初期化
        $voice="";

        //トークン関係初期化
        $channel_secret = env('LINE_CHANNEL_SECRET');
        $access_token = env('LINE_ACCESS_TOKEN');

        // メッセージからreplyTokenを取得
        $inputs=$event->request;
        $reply_token=$inputs['events'][0]['replyToken'];


        //wordに含まれている言葉が含まれていれば　そのキーを使ってvoicesからvoiceへ入れる
        //含まれてなければなにもいれない
        foreach($this->words as $key=>$index){
            if(strpos($inputs['events'][0]['message']['text'],$index) !== false){
                $voice=$this->voices[$key];
                break;
            }
        }

        //LINE-OBJECTを作成
        $client = new CurlHTTPClient($access_token);
        $bot = new LINEBot($client, ['channelSecret' => $channel_secret]);
        //メッセージ送信
        if($voice == ""){
            $bot->replytext($reply_token,$inputs['events'][0]['message']['text']."\n"."なんだなっ！");
        }else{
            $bot->replytext($reply_token,$voice);
        }

        return 0;
    }
}
