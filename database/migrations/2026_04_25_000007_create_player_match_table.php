<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_match', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->cascadeOnDelete();
            $table->decimal('rating', 3, 1)->nullable();
            $table->integer('goals')->default(0);
            $table->integer('assists')->default(0);
            $table->boolean('is_sent_off')->default(false);
            $table->boolean('is_mvp')->default(false);
            $table->decimal('final_score', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'match_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_match');
    }
};
