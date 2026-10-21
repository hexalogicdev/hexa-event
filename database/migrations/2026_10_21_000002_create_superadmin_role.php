<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(
            ['name' => 'superadmin'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $superadminRoleId = DB::table('roles')->where('name', 'superadmin')->value('id');
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');

        if ($adminRoleId) {
            DB::table('users')->where('role_id', $adminRoleId)
                ->update(['role_id' => $superadminRoleId, 'user_type' => 'superadmin', 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $superadminRoleId = DB::table('roles')->where('name', 'superadmin')->value('id');

        if ($adminRoleId && $superadminRoleId) {
            DB::table('users')->where('role_id', $superadminRoleId)
                ->update(['role_id' => $adminRoleId, 'user_type' => 'admin', 'updated_at' => now()]);
        }

        DB::table('roles')->where('name', 'superadmin')->delete();
    }
};
