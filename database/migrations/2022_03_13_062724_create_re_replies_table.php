<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    //リプライ発火用データ
    public function up()
    {
        Schema::create('re_replies', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('keyword',191);
            $table->string('status',191);
            $table->integer('step');
            $table->string('text',191);
            $table->string('nextStatus',191);
            $table->integer('nextStep');//-1で終了　テキストのみでの表示をして後はstep0にしておしまい
            $table->timestamps();

            $table->unique('keyword');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('re_replies');
    }
}
