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
        Schema::table('forum_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('forum_replies', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('forum_post_id')
                    ->constrained('forum_replies')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_replies', function (Blueprint $table) {
            if (Schema::hasColumn('forum_replies', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
        });
    }
};
