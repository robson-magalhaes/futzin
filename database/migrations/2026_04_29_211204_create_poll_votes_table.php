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
        if (!Schema::hasTable('poll_votes')) {
            Schema::create('poll_votes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('poll_id')->constrained('polls')->cascadeOnDelete();
                $table->foreignId('voter_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('candidate_id')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedTinyInteger('rating')->nullable()->comment('1-10 para enquete de notas');
                $table->timestamps();
                $table->unique(['poll_id', 'voter_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
    }
};
