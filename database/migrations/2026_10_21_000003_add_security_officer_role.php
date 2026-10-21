<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(['name' => 'security_officer'], ['created_at' => now(), 'updated_at' => now()]);
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organizer_id')->nullable()->after('role_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('organizer_id'));
        DB::table('roles')->where('name', 'security_officer')->delete();
    }
};
