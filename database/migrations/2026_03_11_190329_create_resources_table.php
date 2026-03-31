<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path'); // Where the file is stored
            $table->string('file_name'); // Original filename
            $table->string('file_type'); // pdf, docx, jpg, etc.
            $table->integer('file_size'); // In bytes
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_official')->default(true); // Lecturer upload = official, Student upload = peer resource
            $table->timestamps();

            // Index for faster searching
            $table->index(['unit_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
