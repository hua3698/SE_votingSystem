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
        Schema::create('vote_records', function (Blueprint $table) {
            $table->increments('record_id');
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('event_id')->on('vote_events')->onDelete('cascade');
            $table->unsignedInteger('cand_id');
            $table->foreign('cand_id')->references('cand_id')->on('candidates')->onDelete('cascade');
            $table->unsignedInteger('code_id');
            $table->foreign('code_id')->references('code_id')->on('generate_qrcodes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_records');
    }
};
