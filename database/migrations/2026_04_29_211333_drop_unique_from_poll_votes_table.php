<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('poll_votes')) {
            return;
        }

        if ($this->foreignKeyExists('poll_votes', 'poll_votes_poll_id_foreign')) {
            Schema::table('poll_votes', function (Blueprint $table) {
                $table->dropForeign('poll_votes_poll_id_foreign');
            });
        }

        if ($this->indexExists('poll_votes', 'poll_votes_poll_id_voter_id_unique')) {
            Schema::table('poll_votes', function (Blueprint $table) {
                $table->dropUnique('poll_votes_poll_id_voter_id_unique');
            });
        }

        if (
            Schema::hasTable('polls')
            && Schema::hasColumn('poll_votes', 'poll_id')
            && !$this->foreignKeyExists('poll_votes', 'poll_votes_poll_id_foreign')
        ) {
            Schema::table('poll_votes', function (Blueprint $table) {
                $table->foreign('poll_id')->references('id')->on('polls')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('poll_votes')) {
            return;
        }

        if ($this->foreignKeyExists('poll_votes', 'poll_votes_poll_id_foreign')) {
            Schema::table('poll_votes', function (Blueprint $table) {
                $table->dropForeign('poll_votes_poll_id_foreign');
            });
        }

        if (
            Schema::hasColumn('poll_votes', 'poll_id')
            && Schema::hasColumn('poll_votes', 'voter_id')
            && !$this->indexExists('poll_votes', 'poll_votes_poll_id_voter_id_unique')
        ) {
            Schema::table('poll_votes', function (Blueprint $table) {
                $table->unique(['poll_id', 'voter_id']);
            });
        }

        if (
            Schema::hasTable('polls')
            && Schema::hasColumn('poll_votes', 'poll_id')
            && !$this->foreignKeyExists('poll_votes', 'poll_votes_poll_id_foreign')
        ) {
            Schema::table('poll_votes', function (Blueprint $table) {
                $table->foreign('poll_id')->references('id')->on('polls')->cascadeOnDelete();
            });
        }
    }

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $database = DB::getDatabaseName();

        $result = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_TYPE = ? AND CONSTRAINT_NAME = ? LIMIT 1',
            [$database, $table, 'FOREIGN KEY', $constraintName]
        );

        return $result !== null;
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        $result = DB::selectOne(
            'SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1',
            [$database, $table, $indexName]
        );

        return $result !== null;
    }
};
