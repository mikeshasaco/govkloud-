<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Move lab configuration directly into lessons table.
     * A lesson with has_lab=true becomes both a lesson AND a lab environment.
     */
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->boolean('has_lab')->default(false)->after('is_published');
            $table->string('workbench_image')->nullable()->after('has_lab');
            $table->integer('ttl_minutes')->nullable()->default(180)->after('workbench_image');
            $table->integer('estimated_minutes')->nullable()->default(30)->after('ttl_minutes');
            $table->json('lab_config_json')->nullable()->after('estimated_minutes');
        });

        // Add lesson_id to lab_sessions so sessions can reference the lesson directly
        Schema::table('lab_sessions', function (Blueprint $table) {
            $table->foreignId('lesson_id')->nullable()->after('lab_id')->constrained('lessons')->nullOnDelete();
        });

        // Migrate existing lab_id references: for each lesson that has a lab_id,
        // copy the lab's config into the lesson fields
        $lessons = \DB::table('lessons')->whereNotNull('lab_id')->get();
        foreach ($lessons as $lesson) {
            $lab = \DB::table('labs')->where('id', $lesson->lab_id)->first();
            if ($lab) {
                \DB::table('lessons')->where('id', $lesson->id)->update([
                    'has_lab' => true,
                    'workbench_image' => $lab->workbench_image,
                    'ttl_minutes' => $lab->ttl_minutes,
                    'estimated_minutes' => $lab->estimated_minutes,
                    'lab_config_json' => $lab->lab_config_json,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('lab_sessions', function (Blueprint $table) {
            $table->dropForeign(['lesson_id']);
            $table->dropColumn('lesson_id');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn([
                'has_lab',
                'workbench_image',
                'ttl_minutes',
                'estimated_minutes',
                'lab_config_json',
            ]);
        });
    }
};
