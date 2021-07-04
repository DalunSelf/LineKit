<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineKeywordSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_keyword_samples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_keyword_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->longText('parameter_value')->nullable();
            $table->unsignedTinyInteger('order')->default(0);
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
        Schema::dropIfExists('line_keyword_samples');
    }
}
