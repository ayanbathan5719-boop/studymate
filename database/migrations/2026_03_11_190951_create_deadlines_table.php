<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deadlines', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('due_date');
            $table->integer('max_score')->nullable(); // e.g., 100
            $table->boolean('is_required')->default(true); // Mandatory or optional
            $table->enum('type', ['assignment', 'project', 'exam', 'quiz'])->default('assignment');
            $table->json('metadata')->nullable(); // For extra fields like submission type, group work, etc.
            $table->timestamps();

            // Indexes for fast queries
            $table->index(['unit_id', 'due_date']);
            $table->index(['lecturer_id', 'due_date']);
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deadlines');
    }
};
