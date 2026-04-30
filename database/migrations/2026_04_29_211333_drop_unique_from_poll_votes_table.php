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
        Schema::table('poll_votes', function (Blueprint $table) {
            // Drop FK que usa o índice, depois o índice, depois recria FK sem ele
            $table->dropForeign(['poll_id']);
            $table->dropUnique(['poll_id', 'voter_id']);
            $table->foreign('poll_id')->references('id')->on('polls')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('poll_votes', function (Blueprint $table) {
            $table->dropForeign(['poll_id']);
            $table->unique(['poll_id', 'voter_id']);
            $table->foreign('poll_id')->references('id')->on('polls')->cascadeOnDelete();
        });
    }
};
