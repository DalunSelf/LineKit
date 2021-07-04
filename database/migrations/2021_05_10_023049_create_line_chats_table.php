<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('line_member_id');
            $table->morphs('sender');
            $table->string('direction');
            $table->string('from')->nullable();
            $table->string('message');
            $table->string('time');
            $table->integer('unseenMsgs');
            $table->timestamps();

            $table->foreign('line_member_id')->references('id')->on('line_members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_chats');
    }
}
