<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_channels', function (Blueprint $table) {
            $table->id();
            $table->uuid('organization_id')->unique();
            $table->unsignedTinyInteger('status')->default(0)->comment('狀態: 0.停用 | 1.啟用');
            $table->string('userId')->comment('機器人的 userId');
            $table->string('basicId')->unique();
            $table->string('displayName');
            $table->string('pictureUrl')->nullable();
            $table->string('endpoint')->nullable();
            $table->string('liffId')->nullable();
            $table->string('clientAccessToken')->nullable();

            $table->string('ChannelID')->unique();
            $table->string('Channelsecret')->unique();
            $table->string('Channelaccesstoken');
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_channels');
    }
}
