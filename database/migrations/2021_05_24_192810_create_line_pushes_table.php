<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinePushesTable extends Migration
{
    /**
     * Run the migrations.
     *\
     * @return void
     */
    public function up()
    {
        Schema::create('line_pushes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_channel_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('send_target')->comment('發送對象');
            $table->text('send_tags')->nullable()->comment('發送標籤');
            $table->unsignedTinyInteger('send_type')->comment('發送方式');
            $table->timestamp('send_time')->nullable()->comment('發送時間');
            $table->unsignedTinyInteger('status')->default(0);
            $table->text('error_message')->nullable();
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
        Schema::dropIfExists('line_pushes');
    }
}
