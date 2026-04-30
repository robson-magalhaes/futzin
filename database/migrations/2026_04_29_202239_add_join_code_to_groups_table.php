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
        if (!Schema::hasColumn('groups', 'join_code')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->string('join_code', 8)->unique()->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('groups', 'join_code')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('join_code');
            });
        }
    }
};
