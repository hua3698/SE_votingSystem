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
        Schema::create('candidates', function (Blueprint $table) {
            $table->increments('cand_id');
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('event_id')->on('vote_events')->onDelete('cascade');
            $table->string('name')->comment('候選人名稱');
            $table->string('school')->nullable()->comment('候選人學校');
            $table->integer('votes_count')->default(0);
            $table->text('notes')->nullable()->comment('備註');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
