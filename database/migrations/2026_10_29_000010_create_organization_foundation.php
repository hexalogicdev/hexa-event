<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status', 30)->default('pending')->index();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('timezone', 64)->default('UTC');
            $table->string('logo')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });

        Schema::create('organization_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 40)->index();
            $table->string('status', 30)->default('active')->index();
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['organization_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('organization_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('role', 40);
            $table->string('token_hash', 64)->unique();
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->index();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'email']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->index(['organization_id', 'status', 'event_start_date'], 'events_org_status_start_index');
        });

        $now = now();
        DB::table('users')->where('user_type', 'organizer')->orderBy('id')->eachById(function ($organizer) use ($now) {
            $organizationId = DB::table('organizations')->insertGetId([
                'owner_id' => $organizer->id,
                'name' => $organizer->name,
                'slug' => Str::slug($organizer->name) . '-' . $organizer->id,
                'status' => $organizer->status ? 'active' : 'suspended',
                'email' => $organizer->email,
                'country' => $organizer->country,
                'timezone' => $organizer->timezone ?: 'UTC',
                'activated_at' => $organizer->status ? $now : null,
                'suspended_at' => $organizer->status ? null : $now,
                'created_at' => $organizer->created_at ?: $now,
                'updated_at' => $now,
            ]);

            DB::table('organization_memberships')->insert([
                'organization_id' => $organizationId,
                'user_id' => $organizer->id,
                'role' => 'owner',
                'status' => $organizer->status ? 'active' : 'suspended',
                'joined_at' => $organizer->created_at ?: $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('events')->where('user_id', $organizer->id)->update(['organization_id' => $organizationId]);

            DB::table('users')->where('user_type', 'security_officer')->where('organizer_id', $organizer->id)
                ->orderBy('id')->eachById(function ($officer) use ($organizationId, $now) {
                    DB::table('organization_memberships')->updateOrInsert(
                        ['organization_id' => $organizationId, 'user_id' => $officer->id],
                        ['role' => 'security_officer', 'status' => $officer->status ? 'active' : 'suspended', 'joined_at' => $officer->created_at ?: $now, 'updated_at' => $now, 'created_at' => $now]
                    );
                });
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropIndex('events_org_status_start_index');
            $table->dropColumn('organization_id');
        });
        Schema::dropIfExists('organization_invitations');
        Schema::dropIfExists('organization_memberships');
        Schema::dropIfExists('organizations');
    }
};

