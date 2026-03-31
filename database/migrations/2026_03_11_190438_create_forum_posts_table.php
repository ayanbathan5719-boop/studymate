<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->json('tags')->nullable(); // ["Assignment Help", "Exam Prep", etc.]
            $table->integer('views')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_announcement')->default(false);
            $table->timestamps();
            $table->softDeletes(); // Allows "hiding" posts instead of permanent delete

            // Indexes for fast searching/filtering
            $table->index(['unit_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['is_pinned', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
    }
};
