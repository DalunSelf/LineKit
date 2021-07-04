<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineRichMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_rich_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_channel_id')->constrained()->onDelete('cascade');
            $table->string('richMenuId')->nullable();
            $table->string('name');
            $table->string('chatBarText');
            $table->boolean('selected');
            $table->text('size');
            $table->text('areas');
            $table->longText('image')->nullable();
            $table->boolean('default')->default(false);
            $table->integer('status')->default(0)->comment('選單狀態 1.排程 | 2.運行中 | 3.已結束 | 4.草稿 | 6.暫停');
            $table->string('type')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_rich_menus');
    }
}
