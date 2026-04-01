<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('resource_id')->nullable()->constrained('resources')->onDelete('cascade');
            $table->foreignId('topic_id')->nullable()->constrained('topics')->onDelete('cascade');
            $table->integer('duration_minutes')->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamp('last_studied_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_progress');
    }
};