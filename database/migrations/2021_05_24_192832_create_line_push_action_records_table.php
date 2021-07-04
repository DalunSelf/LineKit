<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinePushActionRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_push_action_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_push_id')->constrained()->onDelete('cascade');
            $table->foreignId('line_push_sample_id')->constrained()->onDelete('cascade');
            $table->string('serial_code')->comment('流水碼');
            $table->longText('original_format')->comment('原本的格式');
            $table->string('display_text')->comment('顯示文字');
            $table->integer('click_total_count')->comment('觸發次數');
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
        Schema::dropIfExists('line_push_action_records');
    }
}
