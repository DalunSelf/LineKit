<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifyChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notify_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');
            $table->string('ClientId')->unique();
            $table->string('ClientSecret')->unique();
            $table->timestamps();

            // 唯一值
            $table->unique('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notify_channels');
    }
}
