<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * With persistent per-user namespaces, multiple lab sessions will share
     * the same host_namespace. Remove the unique constraint.
     */
    public function up(): void
    {
        Schema::table('lab_sessions', function (Blueprint $table) {
            $table->dropUnique(['host_namespace']);
            $table->index('host_namespace'); // keep an index for lookups
        });
    }

    public function down(): void
    {
        Schema::table('lab_sessions', function (Blueprint $table) {
            $table->dropIndex(['host_namespace']);
            $table->unique('host_namespace');
        });
    }
};
