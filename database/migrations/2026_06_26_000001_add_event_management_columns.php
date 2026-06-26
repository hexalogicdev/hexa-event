<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('checkin_password');
            $table->string('visibility')->default('public')->after('status');
            $table->string('banner_image')->nullable()->after('visibility');
            $table->json('registration_fields')->nullable()->after('banner_image');
        });

        Schema::table('attendees', function (Blueprint $table) {
            $table->json('custom_answers')->nullable()->after('amount');
            $table->foreignId('checked_in_by')->nullable()->after('checked_in_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('checked_in_by');
            $table->dropColumn('custom_answers');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['status', 'visibility', 'banner_image', 'registration_fields']);
        });
    }
};
