<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->string('video_url')->nullable();
            $table->longText('reading_md')->nullable();
            $table->integer('order_index')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->unique(['module_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
