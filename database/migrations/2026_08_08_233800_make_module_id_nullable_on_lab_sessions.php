<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make module_id nullable so lab_sessions can be used for
     * standalone problems (which don't belong to a module).
     */
    public function up(): void
    {
        Schema::table('lab_sessions', function (Blueprint $table) {
            // Drop the foreign key first, then modify the column
            $table->dropForeign(['module_id']);
            $table->foreignId('module_id')->nullable()->change();
            // Re-add foreign key with nullOnDelete
            $table->foreign('module_id')->references('id')->on('modules')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lab_sessions', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->foreignId('module_id')->nullable(false)->change();
            $table->foreign('module_id')->references('id')->on('modules')->cascadeOnDelete();
        });
    }
};
