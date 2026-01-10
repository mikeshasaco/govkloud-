<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('labs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('estimated_minutes')->default(30);
            $table->integer('ttl_minutes')->default(180);
            $table->string('workbench_image');
            $table->string('validator_image')->nullable();
            $table->json('lab_config_json')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labs');
    }
};
