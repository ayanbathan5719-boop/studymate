<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate course-unit assignments
            $table->unique(['course_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_unit');
    }
};
