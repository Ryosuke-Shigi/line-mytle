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
use LINE\LINEBot\MessageBuilder\RawMessageBuilder;

use Illuminate\Support\Facades\DB;

use App\Models\reComment;
use App\Models\lineUser;

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
            //unfollowでないかぎりあります
            //useridは全てにあります
            if(isset($event['replyToken'])){
                $reply_token=$event['replyToken'];
            }

            //イベント処理
            switch($event['type']){
                case 'message':
                    switch($event['message']['type']){
                        //メッセージ
                        //$event['source']['userId']・・・userID
                        //メッセージ
                        case 'text':
                            //ユーザID存在チェック なければ作成
                            $user=$this->checkUserid($event['source']['userId']);
                            switch($user['status']){
                                case 'init':
                                    $sendMessage->add(new TextMessageBuilder($this->repeat($event['message']['text'],$user)));
                                    break;
                            }
                            break;

                        //スタンプ
                        case 'sticker':
                            //$bot->replytext($reply_token,"知ってるんだなっ！\nこれはスタンプなんだなっ！\nかつて和歌山を２度、なにもない焦土にかえたこわいやつなんだなっ！！");
                            $sendMessage->add(new TextMessageBuilder("知ってるんだなっ！"));
                            $sendMessage->add(new TextMessageBuilder("これはスタンプなんだなっ！"));
                            $sendMessage->add(new TextMessageBuilder("かつて和歌山を７度、なにもない焦土にかえたこわいやつなんだなっ！！"));
                            $sendMessage->add(new RawMessageBuilder($this->quickReplyDataA()));

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
                    $sendMessage->add(new TextMessageBuilder($reply_token,"よろしくお願いするんだなっ！"));
                    break;
                default:
                    break;

            }
        }
        //返答送信
        $values->bot->replyMessage($reply_token,$sendMessage);

        return 0;
    }

    //オウム返し＋α
    //ここでもうメッセージ処理も行っている
    private function repeat($message,$user){
        //変数初期化
        $comment="";
        $sendMessage = new MultiMessageBuilder();
        switch($message){
            case "_大きいつづら_":
                $comment = "君のように勘のいい子供は嫌いだよ";
                break;
            case "_小さいつづら_":
                $comment = "ただし魔法は尻から出る";
                break;
            default:
                //テーブル：オウム返しのキーワード等を取得
                $keywords = DB::table('re_comments')->get();
                //一旦、そのままのコメントを保持する
                $comment=$message;
                //メッセージの中に、キーワード（猫とか犬とか）が含まれているか確認
                foreach($keywords as $keyword){
                    //あればコメントを返す準備をする
                    if(strpos($message,$keyword->keyword)!==false){
                        $comment = $keyword->comment."\nなんだなっ！";
                        break;
                    }
                }
                break;
        }
        return $comment;
    }

    //userid登録済かを確認 なければ作成する
    private function checkUserid($id){
        //ユーザ存在チェックフラグ
        $values=array();
        $lineUser = DB::table('line_users')->where('userid','=',$id)->first();
        //なければ作成
        if($lineUser==null){
            DB::beginTransaction();
            try{
                $newData = new lineUser;
                $newData->userid=$id;
                $newData->status="init";
                $newData->step=0;
                $newData->save();
                $values=array('status'=>"init",'step'=>0);
                DB::commit();
            }catch(Exception $exception){
                DB::rollBack();
                return false;
            }
        }else{
            $values=array('status'=>$lineUser->status,'step'=>$lineUser->step);
        }
        return $values;
    }




    //クイックリプライ用配列変換
    private function quickReplyDataA(){
        $array = [
            'type' => 'text',
            'text' => 'めーどのみやげを選ぶんだなっ！',
            'quickReply' => [
                'items' => [
                  [
                        'type' => 'action',
                        'action' => [
                          'type' => 'message',
                          'label' => '_小さいつづら_',
                          'text' => '_小さいつづら_'
                        ]
                  ],
                  [
                        'type' => 'action',
                        'action' => [
                          'type' => 'message',
                          'label' => '_大きいつづら_',
                          'text' => '_大きいつづら_'
                        ]
                  ],
                ]
            ]
          ];
        return $array;
    }


}
