<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('lessons', 'slug')) {
            // Drop the foreign key on module_id first, then drop the unique index, then re-add the FK
            // MySQL won't let us drop the unique index while a FK depends on it
            $fks = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'lessons' 
                  AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            ");

            // Drop all foreign keys on lessons table
            foreach ($fks as $fk) {
                DB::statement("ALTER TABLE lessons DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            }

            // Now safely drop the unique index and column
            DB::statement("ALTER TABLE lessons DROP INDEX `lessons_module_id_slug_unique`");
            DB::statement("ALTER TABLE lessons DROP COLUMN `slug`");

            // Re-add the module_id foreign key
            DB::statement("ALTER TABLE lessons ADD CONSTRAINT `lessons_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE");
        }

        if (Schema::hasColumn('labs', 'slug')) {
            DB::statement("ALTER TABLE labs DROP INDEX `labs_slug_unique`");
            DB::statement("ALTER TABLE labs DROP COLUMN `slug`");
        }
    }

    public function down(): void
    {
        Schema::table('lessons', function ($table) {
            $table->string('slug')->nullable()->after('module_id');
            $table->unique(['module_id', 'slug']);
        });

        Schema::table('labs', function ($table) {
            $table->string('slug')->nullable()->unique()->after('module_id');
        });
    }
};
