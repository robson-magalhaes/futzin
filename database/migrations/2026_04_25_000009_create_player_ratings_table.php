<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rated_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->integer('rating');
            $table->timestamps();
            $table->unique(['user_id', 'rated_by', 'match_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_ratings');
    }
};
