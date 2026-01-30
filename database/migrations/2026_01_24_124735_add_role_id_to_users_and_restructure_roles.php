<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add role_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('username');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });

        // 2. Migrate existing user-role relationships from pivot table to users.role_id
        $userRoles = DB::table('user_roles')->get();
        foreach ($userRoles as $userRole) {
            DB::table('users')
                ->where('id', $userRole->user_id)
                ->update(['role_id' => $userRole->role_id]);
        }

        // 3. Drop the old pivot table
        Schema::dropIfExists('user_roles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate pivot table
        Schema::create('user_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->primary(['user_id', 'role_id']);
        });

        // Migrate data back from users.role_id to pivot table
        $users = DB::table('users')->whereNotNull('role_id')->get();
        foreach ($users as $user) {
            DB::table('user_roles')->insert([
                'user_id' => $user->id,
                'role_id' => $user->role_id,
            ]);
        }

        // Remove role_id from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
