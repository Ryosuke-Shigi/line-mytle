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

use Illuminate\Support\Facades\DB;
use App\Models\reComment;



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
        $this->words=array('猫','パンダ','犬','タスマニアデビル','(emoji)');
        $this->voices=array('にゃ～ん','笹食ってる場合じゃねぇ！','ワンワンイヌドッグ！','たすまにぁーんよいとこー','YOU ARE CHICKEN HEART！');
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




        //LINE-OBJECTを作成
        $client = new CurlHTTPClient($access_token);
        $bot = new LINEBot($client, ['channelSecret' => $channel_secret]);

        foreach($inputs['events'] as $event){
            //token取得（存在していないアクションもあるのでisset）
            if(isset($event['replyToken'])){
                $reply_token=$event['replyToken'];
            }
            switch($event['type']){
                case 'message':
                    //メッセージかスタンプかの判断
                    switch($event['message']['type']){
                        case 'text':
                            switch($event['message']['text']){
                                default:
/*                                     $comment="";
                                    $keywords = DB::table('re_comments')->get();
                                    foreach($keywords as $keyword){
                                        if(strpos($event['message']['text'],$keyword->keyword)!==false){
                                            $comment=$keyword->comment;
                                            break;
                                        }
                                    }
                                    if($comment==""){
                                        $bot->replytext($reply_token,$event['message']['text']."\n"."なんだなっ！");
                                    }else{
                                        $bot->replytext($reply_token,$comment);
                                    }
                                    return $comment;
 */
                                    $this->repeat($bot,$reply_token,$event['message']['text']);
                                    break;
                            }

                            break;
                        case 'sticker':
                            $bot->replytext($reply_token,"こ、こんなの知らないんだなっ…！");
                            break;
                        case 'image':
                            $bot->replytext($reply_token,"『魂を抜き取られるがいい』");
                            break;
                        case 'video':
                            $bot->replytext($reply_token,"これは むーびー なんだなっ！");
                            break;
                        case 'audio':
                            $bot->replytext($reply_token,"何か聞こえるんだなっ！");
                            break;
                        default:
                            $bot->replytext($reply_token,"ソレハヒミツナノデス");
                            break;
                    }
                    break;

                case 'follow':
                    $bot->replytext($reply_token,$event['source']['type']." さん！よろしくなんだなっ！");
                    break;
                default:
                    break;

            }
        }
        return 0;
    }

    private function repeat($bot,$reply_token,$message){
        $comment="";
        $keywords = DB::table('re_comments')->get();
        foreach($keywords as $keyword){
            if(strpos($message,$keyword->keyword)!==false){
                $comment=$keyword->comment;
                break;
            }
        }
        if($comment==""){
            $bot->replytext($reply_token,$message."\n"."なんだなっ！");
        }else{
            $bot->replytext($reply_token,$comment);
        }
        return $comment;
    }


}
