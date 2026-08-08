<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('challenges', function (Blueprint $table) {
            // Problem type: troubleshoot, build, debug, scenario, quiz
            $table->string('problem_type')->default('build')->after('difficulty');

            // Real cluster scenario: YAML manifests applied when problem starts
            $table->json('scenario_manifests_json')->nullable()->after('initial_state_json')
                ->comment('YAML manifests applied to vcluster when problem starts');

            // Auto-grading: validation rules checked against real cluster
            $table->json('validation_rules_json')->nullable()->after('scenario_manifests_json')
                ->comment('Rules checked against real cluster state on Submit');

            // Multiple choice options (for quiz-type problems)
            $table->json('quiz_options_json')->nullable()->after('validation_rules_json')
                ->comment('[{text, is_correct}] for quiz problems');

            // Starter YAML pre-loaded in editor (for debug problems)
            $table->text('starter_yaml')->nullable()->after('quiz_options_json')
                ->comment('Pre-loaded YAML shown in editor for debug problems');

            // Time limit (optional)
            $table->integer('time_limit_minutes')->nullable()->after('estimated_minutes')
                ->comment('Optional enforced time limit');

            // Points value
            $table->integer('points')->default(10)->after('order_index')
                ->comment('Point value for completing this problem');

            // Acceptance rate (calculated)
            $table->decimal('acceptance_rate', 5, 2)->nullable()->after('points')
                ->comment('Completions / attempts percentage');

            // Whether this problem needs a real cluster (vs simulated/quiz)
            $table->boolean('requires_cluster')->default(false)->after('is_published')
                ->comment('Whether to provision a real vcluster');
        });

        Schema::table('challenge_attempts', function (Blueprint $table) {
            // Link to lab session (vcluster)
            $table->uuid('lab_session_id')->nullable()->after('challenge_id');
            $table->foreign('lab_session_id')->references('id')->on('lab_sessions')->nullOnDelete();

            // Detailed validation results
            $table->json('validation_results_json')->nullable()->after('commands_executed')
                ->comment('Per-check pass/fail results from auto-grading');

            // When they submitted
            $table->timestamp('submitted_at')->nullable()->after('completed_at');

            // Points earned (reduced if hints used)
            $table->integer('points_earned')->default(0)->after('hints_used');
        });
    }

    public function down(): void
    {
        Schema::table('challenges', function (Blueprint $table) {
            $table->dropColumn([
                'problem_type',
                'scenario_manifests_json',
                'validation_rules_json',
                'quiz_options_json',
                'starter_yaml',
                'time_limit_minutes',
                'points',
                'acceptance_rate',
                'requires_cluster',
            ]);
        });

        Schema::table('challenge_attempts', function (Blueprint $table) {
            $table->dropForeign(['lab_session_id']);
            $table->dropColumn([
                'lab_session_id',
                'validation_results_json',
                'submitted_at',
                'points_earned',
            ]);
        });
    }
};
