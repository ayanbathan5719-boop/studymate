<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->string('video_url')->nullable();
            $table->text('content')->nullable();
            $table->json('attachments')->nullable();
            $table->integer('estimated_minutes')->nullable();
            $table->timestamps();
            
            // Index for quick filtering
            $table->index(['unit_code', 'order']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('topics');
    }
};