<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['enrolled', 'completed', 'dropped'])->default('enrolled');
            $table->date('enrolled_at')->useCurrent();
            $table->date('completed_at')->nullable();
            $table->boolean('is_custom')->default(false); // True if student-created unit
            $table->timestamps();

            // Ensure a student can't add the same unit twice
            $table->unique(['student_id', 'unit_id']);

            // Indexes for fast queries
            $table->index(['student_id', 'status']);
            $table->index(['unit_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_unit');
    }
};
