<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_unit', function (Blueprint $table) {
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('student_unit', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])
                    ->default('pending')
                    ->after('enrolled_at');
            }

            // Add approved_by column (who approved/rejected)
            if (!Schema::hasColumn('student_unit', 'approved_by')) {
                $table->foreignId('approved_by')
                    ->nullable()
                    ->after('status')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // Add approved_at column (when it was approved/rejected)
            if (!Schema::hasColumn('student_unit', 'approved_at')) {
                $table->timestamp('approved_at')
                    ->nullable()
                    ->after('approved_by');
            }

            // Add rejected_reason (optional - why it was rejected)
            if (!Schema::hasColumn('student_unit', 'rejected_reason')) {
                $table->text('rejected_reason')
                    ->nullable()
                    ->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_unit', function (Blueprint $table) {
            $columns = ['status', 'approved_by', 'approved_at', 'rejected_reason'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('student_unit', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
