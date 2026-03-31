<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (!Schema::hasColumn('resources', 'topic_id')) {
                $table->foreignId('topic_id')->nullable()->after('unit_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (Schema::hasColumn('resources', 'topic_id')) {
                $table->dropForeign(['topic_id']);
                $table->dropColumn('topic_id');
            }
        });
    }
};
