<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('plan', ['free', 'basic', 'premium', 'enterprise'])->default('free');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('status', ['active', 'cancelled', 'expired'])->default('active');
            $table->string('payment_gateway_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
