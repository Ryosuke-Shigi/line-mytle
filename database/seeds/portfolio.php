<?php

use Illuminate\Database\Seeder;

class portfolio extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    //ポートフォリオへの対応テーブルデータ作成
    public function run()
    {
        //
        //
        //reReplytable
        $reReplyParams = [
            [
                'keyword'=>'ポートフォリオ',
                'status'=>'init',
                'step'=>0,
                'text'=>'ポートフォリオを選んで欲しいんだなっ！',
                'nextStatus'=>'ポートフォリオ',
                'nextStep'=>0,
            ],
            [
                'keyword'=>'他にない？',
                'status'=>'ポートフォリオ',
                'step'=>0,
                'text'=>'ないんだなっ！',
                'nextStatus'=>'init',
                'nextStep'=>0,
            ],
        ];
        foreach($reReplyParams as $param){
            DB::table('re_replies')->insert($param);
        }
        //クイックリプライ
        //stepで多種多様に返答メッセージを増やせる
        $replyAnswerParams=[
            [
                'status'=>'ポートフォリオ',
                'step'=>0,
                'text'=>'STRAMP-RALLY',
            ],
            [
                'status'=>'ポートフォリオ',
                'step'=>0,
                'text'=>'地図チャット',
            ],
            [
                'status'=>'ポートフォリオ',
                'step'=>0,
                'text'=>'ないんだなっ！',
            ],
        ];
        foreach($replyAnswerParams as $param){
            DB::table('reply_answers')->insert($param);
        }
        $replyReactionParams=[
            //スタンプラリー
            [
                'keyword'=>'STAMP-RALLY',
                'status'=>'ポートフォリオ',
                'step'=>0,
                'text'=>'スタンプラリーを作成＆遊ぶ\nＷＥＢアプリ',
            ],
            [
                'keyword'=>'STAMP-RALLY',
                'status'=>'ポートフォリオ',
                'step'=>0,
                'text'=>'https://stamprally-laravel.herokuapp.com/LP',
            ],

            [
                'keyword'=>'地図チャット',
                'status'=>'ポートフォリオ',
                'step'=>0,
                'text'=>'地図共有\nリアルタイムチャット',
            ],
            [
                'keyword'=>'地図チャット',
                'status'=>'ポートフォリオ',
                'step'=>0,
                'text'=>'https://map-talk.herokuapp.com/',
            ],
        ];
        foreach($replyReactionParams as $param){
            DB::table('reply_reactions')->insert($param);
        }
    }
}
