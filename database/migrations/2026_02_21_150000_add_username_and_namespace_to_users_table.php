<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable()->unique()->after('name');
            $table->string('k8s_namespace', 50)->nullable()->unique()->after('username');
        });

        // Generate usernames for existing users from email prefix
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $baseUsername = strtolower(preg_replace('/[^a-z0-9]/', '', explode('@', $user->email)[0]));
            $username = substr($baseUsername, 0, 20) ?: 'user' . $user->id;

            // Ensure uniqueness
            $candidate = $username;
            $suffix = 1;
            while (DB::table('users')->where('username', $candidate)->exists()) {
                $candidate = substr($username, 0, 17) . $suffix;
                $suffix++;
            }

            DB::table('users')->where('id', $user->id)->update([
                'username' => $candidate,
                'k8s_namespace' => 'gk-user-' . $candidate,
            ]);
        }

        // Now make columns non-nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable(false)->change();
            $table->string('k8s_namespace', 50)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'k8s_namespace']);
        });
    }
};
