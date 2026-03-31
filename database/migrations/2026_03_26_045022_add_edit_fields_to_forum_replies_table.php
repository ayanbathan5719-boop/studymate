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
            if (!Schema::hasColumn('forum_replies', 'edited_at')) {
                $table->timestamp('edited_at')->nullable()->after('content');
            }
            if (!Schema::hasColumn('forum_replies', 'edit_count')) {
                $table->integer('edit_count')->default(0)->after('edited_at');
            }
            if (!Schema::hasColumn('forum_replies', 'original_content')) {
                $table->text('original_content')->nullable()->after('edit_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->dropColumn(['edited_at', 'edit_count', 'original_content']);
        });
    }
};
