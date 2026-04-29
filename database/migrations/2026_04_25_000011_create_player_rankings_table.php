<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->decimal('average_rating', 5, 2)->default(0);
            $table->integer('matches_played')->default(0);
            $table->integer('goals')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('mvp_count')->default(0);
            $table->decimal('points_penalty', 5, 2)->default(0);
            $table->decimal('total_score', 8, 2)->default(0);
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_rankings');
    }
};
