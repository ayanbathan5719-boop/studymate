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
        Schema::create('deadline_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deadline_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('reminded_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('score')->nullable();
            $table->timestamps();
            
            // Prevent duplicate entries
            $table->unique(['deadline_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deadline_student');
    }
};