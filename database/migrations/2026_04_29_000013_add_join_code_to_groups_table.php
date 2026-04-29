<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('join_code', 8)->unique()->nullable()->after('status');
        });

        DB::table('groups')->orderBy('id')->each(function ($group) {
            $code = strtoupper(Str::random(6));
            DB::table('groups')->where('id', $group->id)->update(['join_code' => $code]);
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('join_code');
        });
    }
};
