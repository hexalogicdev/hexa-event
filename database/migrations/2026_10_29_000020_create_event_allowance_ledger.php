<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_event_allowance_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('adjusted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('quantity');
            $table->string('reason', 255);
            $table->text('notes')->nullable();
            $table->string('agreement_reference', 100)->nullable();
            $table->date('effective_date');
            $table->timestamps();
            $table->index(['organization_id', 'effective_date'], 'org_allowance_effective_index');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->uuid('creation_token')->nullable()->unique()->after('organization_id');
        });

        $now = now();
        DB::table('organizations')->orderBy('id')->eachById(function ($organization) use ($now) {
            $existingEvents = DB::table('events')->where('organization_id', $organization->id)->count();

            if ($existingEvents > 0) {
                DB::table('organization_event_allowance_adjustments')->insert([
                    'organization_id' => $organization->id,
                    'adjusted_by' => null,
                    'quantity' => $existingEvents,
                    'reason' => 'Opening allowance for existing events',
                    'notes' => 'System-generated during allowance ledger introduction.',
                    'agreement_reference' => null,
                    'effective_date' => $now->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropUnique(['creation_token']);
            $table->dropColumn('creation_token');
        });
        Schema::dropIfExists('organization_event_allowance_adjustments');
    }
};
