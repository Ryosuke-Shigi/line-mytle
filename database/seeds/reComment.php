<?php

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;



class reComment extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $params = [
            [
                'keyword'=>'パンダ',
                'comment'=>'笹食ってる場合じゃねぇ！',
            ],
            [
                'keyword'=>'猫',
                'comment'=>'にゃ～ん',
            ],
            [
                'keyword'=>'犬',
                'comment'=>'ワンワンイヌドッグ！',
            ],
        ];


        foreach($params as $param){
            DB::table('re_comments')->insert($param);
        }


    }
}
