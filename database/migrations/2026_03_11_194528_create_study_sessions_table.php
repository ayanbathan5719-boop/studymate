<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('resource_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->integer('duration_seconds')->nullable(); // Calculated when ended
            $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
            $table->string('session_type')->default('resource'); // resource, forum, custom
            $table->json('metadata')->nullable(); // Extra data like scroll position, last page, etc.
            $table->timestamps();

            // Indexes for fast queries and reporting
            $table->index(['student_id', 'created_at']);
            $table->index(['student_id', 'unit_id', 'created_at']);
            $table->index(['status', 'started_at']);

            // For motivation summaries (daily/weekly)
            $table->index(['student_id', 'status', 'ended_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_sessions');
    }
};
