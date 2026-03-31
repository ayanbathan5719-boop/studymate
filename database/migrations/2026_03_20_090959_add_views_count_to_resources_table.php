<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (!Schema::hasColumn('resources', 'views_count')) {
                $table->integer('views_count')->default(0)->after('download_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (Schema::hasColumn('resources', 'views_count')) {
                $table->dropColumn('views_count');
            }
        });
    }
};
