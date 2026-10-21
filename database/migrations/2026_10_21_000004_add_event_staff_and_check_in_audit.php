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
        Schema::create('event_security_officer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['event_id', 'user_id']);
        });

        Schema::create('check_in_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('attendee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference', 255)->nullable();
            $table->string('outcome', 40)->index();
            $table->string('message', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('attempted_at')->useCurrent()->index();
            $table->timestamps();
        });

        DB::table('attendees')->whereNull('qr_id')->orWhere('qr_id', '')->orderBy('id')->eachById(function ($attendee) {
            DB::table('attendees')->where('id', $attendee->id)->update(['qr_id' => Str::upper(Str::random(32))]);
        });

        $duplicateReferences = DB::table('attendees')
            ->select('qr_id')->whereNotNull('qr_id')->groupBy('qr_id')->havingRaw('COUNT(*) > 1')->pluck('qr_id');
        foreach ($duplicateReferences as $reference) {
            DB::table('attendees')->where('qr_id', $reference)->orderBy('id')->skip(1)->each(function ($attendee) {
                DB::table('attendees')->where('id', $attendee->id)->update(['qr_id' => Str::upper(Str::random(32))]);
            });
        }

        Schema::table('attendees', function (Blueprint $table) {
            $table->unique('qr_id');
        });
    }

    public function down(): void
    {
        Schema::table('attendees', fn (Blueprint $table) => $table->dropUnique(['qr_id']));
        Schema::dropIfExists('check_in_attempts');
        Schema::dropIfExists('event_security_officer');
    }
};
