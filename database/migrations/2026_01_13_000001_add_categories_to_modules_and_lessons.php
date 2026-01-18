<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add category to modules (e.g., DevSecOps, Cloud Engineer, SRE)
        Schema::table('modules', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
        });

        // Add subcategory to lessons (e.g., Kubernetes, Terraform, Docker)
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('subcategory')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('subcategory');
        });
    }
};
