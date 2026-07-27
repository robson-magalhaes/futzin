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
                $table->unsignedBigInteger('poll_id');
                $table->foreignId('voter_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('candidate_id')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedTinyInteger('rating')->nullable()->comment('1-10 para enquete de notas');
                $table->timestamps();
                $table->unique(['poll_id', 'voter_id']);
            });

            // A migration de polls tem o mesmo timestamp e pode rodar depois em alguns bancos.
            if (Schema::hasTable('polls')) {
                Schema::table('poll_votes', function (Blueprint $table) {
                    $table->foreign('poll_id')->references('id')->on('polls')->cascadeOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
    }
};
