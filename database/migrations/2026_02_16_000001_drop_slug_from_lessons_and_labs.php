<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Disable FK checks so we can freely drop indexes
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropUnique(['module_id', 'slug']);
            $table->dropColumn('slug');
        });

        Schema::table('labs', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
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
