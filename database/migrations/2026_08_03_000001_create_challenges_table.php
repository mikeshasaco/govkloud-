<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description'); // Markdown challenge prompt
            $table->string('category'); // kubernetes, terraform, docker
            $table->string('difficulty'); // beginner, medium, hard
            $table->integer('estimated_minutes')->default(15);
            $table->integer('order_index')->default(0);
            $table->boolean('is_published')->default(false);

            // Code Editor Config
            $table->json('initial_files_json')->nullable(); // {"pod.yaml": "# Write here\n"}
            $table->json('file_language_map')->nullable();  // {"pod.yaml": "yaml"}

            // Terminal Simulator Config
            $table->json('command_flows_json')->nullable(); // Command→output mappings + validation
            $table->json('initial_state_json')->nullable(); // Pre-existing cluster state

            // Solution
            $table->json('solution_files_json')->nullable(); // Correct file contents
            $table->text('solution_explanation')->nullable(); // Markdown explanation
            $table->json('hints_json')->nullable(); // Array of progressive hints

            // Tutorial Video
            $table->string('video_url')->nullable(); // YouTube/embed URL
            $table->string('video_file')->nullable(); // Azure Blob path

            // Tags
            $table->json('tags')->nullable(); // ["pods", "secrets"]

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
