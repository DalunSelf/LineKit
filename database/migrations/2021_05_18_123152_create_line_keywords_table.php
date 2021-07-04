<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_channel_id')->constrained()->onDelete('cascade');
            $table->integer('type');
            $table->text('keys');
            $table->integer('level')->nullable();
            $table->text('tags')->nullable();
            $table->integer('click')->default(0);
            $table->unsignedTinyInteger('status')->default(0);
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
        Schema::dropIfExists('line_keywords');
    }
}
