<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->enum('fee_type', ['monthly', 'daily'])->default('monthly')->after('monthly_fee');
            $table->time('confirmation_lock_at')->nullable()->after('status');
            $table->enum('schedule_type', ['scheduled', 'weekly'])->default('scheduled')->after('confirmation_lock_at');
            $table->tinyInteger('weekly_day')->nullable()->after('schedule_type'); // 0=Dom...6=Sáb
            $table->time('weekly_time')->nullable()->after('weekly_day');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['fee_type', 'confirmation_lock_at', 'schedule_type', 'weekly_day', 'weekly_time']);
        });
    }
};
