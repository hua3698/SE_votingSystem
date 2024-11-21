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
            $table->datetime('start_time')->nullable()->comment('投票活動開啟時間');
            $table->datetime('end_time')->nullable()->comment('投票活動結束時間');
            $table->integer('max_vote_count')->comment('每張qrcode最多可以投幾票');
            $table->integer('number_of_qrcodes')->comment('要產生的qrcode數量');
            $table->integer('number_of_candidates')->comment('共有幾位候選人');
            $table->integer('number_of_winners')->comment('共有幾位得名者');
            $table->tinyInteger('manual_control')->default(0)->comment('是否手動控制投票活動');
            $table->string('vote_is_ongoing')->default(0)->comment('只有在manual_control=1才有效，0:尚未開始,1:投票進行中,2:已結束');
            $table->integer('is_delete')->default(0)->comment('是否已刪除');
            $table->integer('is_lock')->default(0);
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
