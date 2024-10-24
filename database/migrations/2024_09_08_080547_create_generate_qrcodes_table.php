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
        Schema::create('generate_qrcodes', function (Blueprint $table) {
            $table->increments('code_id');
            $table->unsignedInteger('event_id'); // 外鍵欄位
            $table->foreign('event_id')->references('event_id')->on('vote_events')->onDelete('cascade');
            $table->string('qrcode_string');
            $table->boolean('has_been_voted')->default(false)->comment('是否已經投過');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generate_qrcodes');
    }
};
