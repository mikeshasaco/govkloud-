<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Quiz JSON structure:
            // [
            //   {
            //     "question": "What command creates a pod?",
            //     "type": "text" | "multiple_choice",
            //     "options": ["kubectl run", "kubectl create", ...],  // for multiple choice
            //     "correct_answer": "kubectl run nginx --image=nginx",
            //     "explanation": "The kubectl run command..."
            //   }
            // ]
            $table->json('quiz_json')->nullable()->after('reading_md');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('quiz_json');
        });
    }
};
