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
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

//クイックリプライ関係
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;

use Illuminate\Support\Facades\DB;
use App\Models\reComment;
use LINE\LINEBot\MessageBuilder\RawMessageBuilder;

class talkRepeat
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  mytleRepeat  $event
     * @return void
     */
    public function handle(mytleRepeat $values)
    {
        //$event    //eventsの変数を扱える
        //変数初期化
        $inputs=$values->request;
        $sendMessage = new MultiMessageBuilder();

        foreach($inputs['events'] as $event){
            //token取得（存在していないアクションもあるのでisset）
            if(isset($event['replyToken'])){
                $reply_token=$event['replyToken'];
            }
            //イベント処理
            switch($event['type']){
                case 'message':
                    switch($event['message']['type']){
                        //メッセージ
                        //$event['source']['userId']・・・userID

                        case 'text':
                            switch($event['message']['text']){
                                default:
                                    $sendMessage->add(new TextMessageBuilder($this->repeat($event['message']['text'])));
                                    break;
                            }
                            break;

                        //スタンプ
                        case 'sticker':
                            //$bot->replytext($reply_token,"知ってるんだなっ！\nこれはスタンプなんだなっ！\nかつて和歌山を２度、なにもない焦土にかえたこわいやつなんだなっ！！");
                            $sendMessage->add(new TextMessageBuilder("知ってるんだなっ！"));
                            $sendMessage->add(new TextMessageBuilder("これはスタンプなんだなっ！"));
                            $sendMessage->add(new TextMessageBuilder("かつて和歌山を７度、なにもない焦土にかえたこわいやつなんだなっ！！"));
                            break;
                        //画像
                        case 'image':
                            $sendMessage->add(new TextMessageBuilder("知ってるんだなっ！"));
                            $sendMessage->add(new TextMessageBuilder("これは写真なんだなっ！"));
                            $sendMessage->add(new TextMessageBuilder("かつて和歌山を６度、氷の世界に変えたこわいやつなんだなっ！！"));
                            break;
                        //映像
                        case 'video':
                            $sendMessage->add(new TextMessageBuilder("知ってるんだなっ！"));
                            $sendMessage->add(new TextMessageBuilder("これはむーびーなんだなっ！"));
                            $sendMessage->add(new TextMessageBuilder("かつて和歌山を５度、誰も住めない毒でいっぱいにしたこわいやつなんだなっ！！"));
                            break;
                        //音声
                        case 'audio':
                            $sendMessage->add(new TextMessageBuilder("知ってるんだなっ！"));
                            $sendMessage->add(new TextMessageBuilder("これはおんがくなんだなっ！"));
                            $sendMessage->add(new TextMessageBuilder("かつて和歌山を３度、海の底に沈めたすごいやつなんだなっ！！"));
                            break;
                        //その他（locationなど）
                        default:
                            $sendMessage->add(new TextMessageBuilder("メタメタに…メタメタにやられたんだなっ…！"));
                            $sendMessage->add(new TextMessageBuilder("泣いても…許してくれなかったんだなっ…！"));
                            break;
                    }
                    break;

                case 'follow':
                    $sendMessage->add(new TextMessageBuilder($reply_token,$event['source']['type']." さん！よろしくなんだなっ！"));
                    break;
                default:
                    break;

            }
        }


        //返答送信
        $askAgeBuilder = new RawMessageBuilder($this->quickReplyDataA());
        $values->bot->replyMessage($reply_token,$sendMessage,$askAgeBuilder);

        return 0;
    }

    //オウム返し＋α
    //ここでもうメッセージ処理も行っている
    private function repeat($message){
        //変数初期化
        $comment="";
        $sendMessage = new MultiMessageBuilder();

        //テーブル：オウム返しのキーワード等を取得
        $keywords = DB::table('re_comments')->get();
        //メッセージの中に、キーワード（猫とか犬とか）が含まれているか確認
        foreach($keywords as $keyword){
            //あればコメントを返す準備をする
            if(strpos($message,$keyword->keyword)!==false){
                return $keyword->comment;
            }
        }
        return ($message."\n"."なんだなっ！");
    }



    private function quickReplyDataA(){
        $values=array(
                        'type'=>'text',
                        'text'=>'text',
                        'quickReply' => array(
                        'items' => array(
                            array('type' => 'action',
                                    'action' => array(
                                    'type' => 'postback',
                                    'label' => 'Data Send',
                                    'data' => 'PostBackData',
                                    'displayText' => 'ポストバックデータを送ります。',
                                )
                            ),
                            array('type' => 'action',
                                    'action' => array(
                                    'type' => 'message',
                                    'label' => 'Message Send',
                                    'text' => 'テキストを送信します。',
                                )
                            )
                        )
                    )
                );
        return $values;
    }


}
