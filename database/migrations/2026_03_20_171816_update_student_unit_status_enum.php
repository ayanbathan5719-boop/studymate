<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_unit', function (Blueprint $table) {
            // First, drop the old enum column
            $table->dropColumn('status');
        });

        Schema::table('student_unit', function (Blueprint $table) {
            // Add new enum with correct values
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('unit_id');
        });
    }

    public function down(): void
    {
        Schema::table('student_unit', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('student_unit', function (Blueprint $table) {
            $table->enum('status', ['enrolled', 'completed', 'dropped'])->default('enrolled')->after('unit_id');
        });
    }
};
