<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vote_events', function (Blueprint $table) {
            $table->increments('event_id');
            $table->string('event_name');
            $table->datetime('start_time')->comment('投票活動開啟時間');
            $table->datetime('end_time')->comment('投票活動結束時間');
            $table->integer('max_vote_count')->comment('每張qrcode最多可以投幾票');
            $table->integer('number_of_qrcodes')->comment('要產生的qrcode數量');
            $table->integer('number_of_candidates')->comment('共有幾位候選人');
            $table->integer('number_of_winners')->comment('共有幾位得名者');
            $table->boolean('manual_control')->default(true)->comment('是否手動控制投票活動');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_events');
    }
};
