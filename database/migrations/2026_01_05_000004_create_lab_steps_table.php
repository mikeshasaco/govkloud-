<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lab_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_id')->constrained()->cascadeOnDelete();
            $table->integer('order_index');
            $table->enum('type', ['instruction', 'quiz', 'task', 'validate']);
            $table->json('payload_json');
            $table->timestamps();

            $table->index(['lab_id', 'order_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_steps');
    }
};
