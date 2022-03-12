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

            /*
                if(firststep){
                    $table=DB(最初のコメントへの対応をテーブルでししぼる)
                    $answertable->DB(そのメッセージに関連するクイックリプライ項目を全て取得)
                    $answers=array();
                    //解答関係を全て一つの配列にまとめる
                    foreach(answertable){
                        array_push($answers,解答)
                    }
                    //クイックリプレイにまとめる
                    $sendMessage->add($this->quickReply('選んでほしいんだなっ！',array('- STAMP_RALLY -','- 地図茶 -')));

                }else{
                    $table=DB('現在どのステップか　で取り出すテーブルを変える)->(where step)->first()
                    $answerTable=DB::('ステップとstatusから該当する解凍（場合によっては複数をとりだす)->get()
                    $answers=array();
                        答えをいれる
                            $sendMessage->add(new TextMessageBuilder("メタメタに…メタメタにやられたんだなっ…！"));
                    }
                    status='init'
                    step=0;
                    にする

                }




            */


            //イベント処理（テーブル仕様にした場合、ここは大幅修正が必要
            switch($event['type']){
                case 'message':
                    switch($event['message']['type']){
                        //メッセージ
                        //$event['source']['userId']・・・userID
                        //メッセージ
                        case 'text':
                            //ユーザID存在チェック なければ作成
                            $user=$this->checkUserid($event['source']['userId']);
                            if($user!=false){
                                $sendMessage=$this->repeat($event['message']['text'],$user);
                                break;
                            }
                            break;
                        //スタンプ
                        case 'sticker':
                            $sendMessage=$this->reSticker();
                            break;
                        //画像
                        case 'image':
                            $sendMessage=$this->reImage();
                            break;
                        //映像
                        case 'video':
                            $sendMessage=$this->reVideo();
                            break;
                        //音声
                        case 'audio':
                            $sendMessage=$this->reAudio();
                            break;
                        //その他（locationなど）
                        default:
                            $sendMessage->add(new TextMessageBuilder("メタメタに…メタメタにやられたんだなっ…！"));
                            $sendMessage->add(new TextMessageBuilder("泣いても…許してくれなかったんだなっ…！"));
                            break;
                    }
                    break;

                case 'follow':
                    $sendMessage->add(new TextMessageBuilder("よろしくお願いするんだなっ！"));
                    break;
                default:
                    break;

            }
        }
        //返答送信
        $values->bot->replyMessage($reply_token,$sendMessage);

        return 0;
    }








    ////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  返答アクション
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////////




    //オウム返し＋α
    //ここでもうメッセージ処理も行っている
    private function repeat($message,$user){
        //変数初期化
        $sendMessage = new MultiMessageBuilder();
/*
        if($lineUser->status == 'init'){
            $firstTalk = DB::table('first_talks')->where('message','=',$message)->first();
            if($firstTalk==null){
                //オウム返し
            }else{
                //テーブル：オウム返しのキーワード等を取得
                $keywords = DB::table('re_comments')->get();
                //一旦、そのままのコメントを保持する
                $sendMessage->add(new TextMessageBuilder($message."\nなんだなっ！"));
                //メッセージの中に、キーワード（猫とか犬とか）が含まれているか確認
                foreach($keywords as $keyword){
                    //あればコメントを返す準備をする
                    if(strpos($message,$keyword->keyword)!==false){
                        $sendMessage->add(new TextMessageBuilder($keyword->comment."\nなんだなっ！"));
                        break;
                    }
                }
            }
        }else{



        }
 */



        //状態を取得
        //$lineUser = DB::table('line_users')->where('userid','=',$user->userid)->first();

        //メッセージ内容について
        switch($message){
            case "ポートフォリオ":
                $sendMessage->add($this->quickReply('選んでほしいんだなっ！',array('- STAMP_RALLY -','- 地図茶 -')));
                break;
            case "- STAMP_RALLY -":
                $sendMessage->add(new TextMessageBuilder("スタンプラリーを作成・遊べる\n初自作ＷＥＢアプリ"));
                $sendMessage->add(new TextMessageBuilder("https://stamprally-laravel.herokuapp.com/LP"));
                break;
            case "- 地図茶 -":
                $sendMessage->add(new TextMessageBuilder("地図共有できる\nリアルタイムチャット\npusherを使ってみたかった"));
                $sendMessage->add(new TextMessageBuilder("https://map-talk.herokuapp.com/"));
                break;
            default:
                //テーブル：オウム返しのキーワード等を取得
                $keywords = DB::table('re_comments')->get();
                //一旦、そのままのコメントを保持する
                $sendMessage->add(new TextMessageBuilder($message."\nなんだなっ！"));
                //メッセージの中に、キーワード（猫とか犬とか）が含まれているか確認
                foreach($keywords as $keyword){
                    //あればコメントを返す準備をする
                    if(strpos($message,$keyword->keyword)!==false){
                        $sendMessage->add(new TextMessageBuilder($keyword->comment."\nなんだなっ！"));
                        break;
                    }
                }
                //なんらかのアクションにはいっているさなか、適当メッセージを送っていたら
                if($lineUser->status != 'init'){
                    $user = LineUser::where('userid','=',$$user->userid)->first();
                    $user->status="init";
                    $user->step=0;
                    $user->update();
                }
                break;
        }
        return $sendMessage;
    }





    //スタンプ返答
    private function reSticker(){
        $sendMessage = new MultiMessageBuilder();
        $sendMessage->add(new TextMessageBuilder("知ってるんだなっ！"));
        $sendMessage->add(new TextMessageBuilder("これはスタンプなんだなっ！"));
        $sendMessage->add(new TextMessageBuilder("かつて和歌山を７度、なにもない焦土にかえたこわいやつなんだなっ！！"));
        return $sendMessage;
    }
    //画像返答
    private function reImage(){
        $sendMessage = new MultiMessageBuilder();
        $sendMessage->add(new TextMessageBuilder("知ってるんだなっ！"));
        $sendMessage->add(new TextMessageBuilder("これは写真なんだなっ！"));
        $sendMessage->add(new TextMessageBuilder("かつて和歌山を６度、氷の世界に変えたこわいやつなんだなっ！！"));
        return $sendMessage;
    }
    //映像返答
    private function reVideo(){
        $sendMessage = new MultiMessageBuilder();
        $sendMessage->add(new TextMessageBuilder("知ってるんだなっ！"));
        $sendMessage->add(new TextMessageBuilder("これはむーびーなんだなっ！"));
        $sendMessage->add(new TextMessageBuilder("かつて和歌山を５度、誰も住めない毒でいっぱいにしたこわいやつなんだなっ！！"));
        return $sendMessage;
    }
    //音声返答
    private function reAudio(){
        $sendMessage = new MultiMessageBuilder();
        $sendMessage->add(new TextMessageBuilder("知ってるんだなっ！"));
        $sendMessage->add(new TextMessageBuilder("これはおんがくなんだなっ！"));
        $sendMessage->add(new TextMessageBuilder("かつて和歌山を３度、海の底に沈めたすごいやつなんだなっ！！"));
        return $sendMessage;
    }




    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  ユーザテーブル編集
    //
    /////////////////////////////////////////////////////////////////////////////////////////////




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
                $values=array('userid'=>$id,'status'=>"init",'step'=>0);
                DB::commit();
            }catch(Exception $exception){
                DB::rollBack();
                return false;
            }
        }else{
            $values=array('userid'=>$id,'status'=>$lineUser->status,'step'=>$lineUser->step);
        }
        return $values;
    }

    //ユーザ初期化
    private function initStatus($id){
        $user = LineUser::where('userid','=',$id)->first();
        $user->status='init';
        $user->step=0;
        $user->update();
        return 0;
    }
    //ステータス変更
    private function changeStatus($id,$status){
        $user = LineUser::where('userid','=',$id)->first();
        $user->status=$status;
        $user->update();
        return 0;
    }
    //ステップ変更
    private function changeStep($id,$step){
        $user = LineUser::where('userid','=',$id)->first();
        $user->step=$step;
        $user->update();
        return 0;
    }








    /////////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  クイックリプライ
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////



    //クイックリプライ変換
    //$question クエスチョン
    //$answer 解答（配列で複数個いれられる）
    private function quickReply($question,$answer){
        $values=array('type'=>'text');
        $values+=array('text'=>$question);
        $values+=array('quickReply'=>array('items'=>array()));
        foreach($answer as $value){
            array_push($values['quickReply']['items'],array('type'=>'action','action'=>array('type'=>'message','label'=>$value,'text'=>$value)));
        }
        $sendMessage=new RawMessageBuilder($values);
        return $sendMessage;
    }

    //おためしクイックリプライ用配列変換
    private function quickReplyDataA(){

        $items=array('_小さいつづら_','_大きいつづら_');
        $values=$this->quickReply('めーどのみやげを選ぶんだなっ！',$items);


/*         $values=array('type'=>'text');
        $values+=array('text'=>'めーどのおみやげを選ぶんだなっ');
        $values+=array('quickReply'=>array('items'=>array()));
        //$values['quickReply']['items']+=array('type'=>'action','action'=>array('type'=>'message','label'=>'text1','text'=>'text1'));
        $item=array('type'=>'action','action'=>array('type'=>'message','label'=>'_小さいつづら_','text'=>'_小さいつづら_'));
        $item2=array('type'=>'action','action'=>array('type'=>'message','label'=>'_大きいつづら_','text'=>'_大きいつづら_'));
        array_push($values['quickReply']['items'],$item);
        array_push($values['quickReply']['items'],$item2); */


/*         $array = [
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
 */
        return $values;
    }


}
