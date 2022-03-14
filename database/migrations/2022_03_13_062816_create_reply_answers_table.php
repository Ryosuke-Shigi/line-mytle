<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplyAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    //リプライ選択肢
    public function up()
    {
        Schema::create('reply_answers', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('status',191);
            $table->integer('step');
            $table->string('text',191);
            $table->timestamps();
            $table->foreign('status')->references('keyword')->on('re_replies')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reply_answers');
    }
}
