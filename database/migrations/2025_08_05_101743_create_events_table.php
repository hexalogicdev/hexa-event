<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('event_start_date');
            $table->dateTime('Event_end_date');
            $table->date('booking_start_date')->nullable();
            $table->date('booking_end_date')->nullable();
            $table->integer('max_attendees')->default(100); // default max attendees
            $table->string('event_code')->unique(); // for registration
            $table->string('checkin_password');     // for security at check-in
            $table->decimal('price', 8, 2);          // event price
            $table->boolean('is_active')->default(true); // to enable/disable event
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
