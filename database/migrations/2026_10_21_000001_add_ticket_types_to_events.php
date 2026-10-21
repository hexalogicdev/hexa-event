<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('ticketing_type')->default('free')->after('price');
        });

        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('attendees', function (Blueprint $table) {
            $table->foreignId('ticket_type_id')->nullable()->after('event_id')->constrained('ticket_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ticket_type_id');
        });
        Schema::dropIfExists('ticket_types');
        Schema::table('events', fn (Blueprint $table) => $table->dropColumn('ticketing_type'));
    }
};
