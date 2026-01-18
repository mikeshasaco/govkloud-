<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lab_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', [
                'provisioning',
                'running',
                'validating',
                'passed',
                'failed',
                'expired',
                'destroyed',
                'error'
            ])->default('provisioning');
            $table->string('host_namespace')->unique();
            $table->string('vcluster_release_name')->nullable();
            $table->string('workbench_release_name')->nullable();
            $table->string('session_token', 64);
            $table->text('code_url')->nullable();
            $table->dateTime('expires_at');
            $table->dateTime('last_activity_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'module_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_sessions');
    }
};
