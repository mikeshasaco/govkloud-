<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Drop the composite unique index first, then the column
            $table->dropUnique(['module_id', 'slug']);
            $table->dropColumn('slug');
        });

        Schema::table('labs', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('module_id');
            $table->unique(['module_id', 'slug']);
        });

        Schema::table('labs', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('module_id');
        });
    }
};
