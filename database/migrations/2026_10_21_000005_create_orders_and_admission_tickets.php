<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 32)->unique();
            $table->string('purchaser_name');
            $table->string('purchaser_email');
            $table->string('purchaser_phone', 30)->nullable();
            $table->string('currency', 3)->default('GBP');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('status', 30)->default('pending')->index();
            $table->string('payment_status', 30)->default('not_required')->index();
            $table->string('idempotency_key', 100)->nullable()->unique();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
            $table->index(['event_id', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ticket_name');
            $table->text('ticket_description')->nullable();
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('attendees', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->after('event_id')->constrained()->nullOnDelete();
            $table->foreignId('order_item_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendee_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('token', 64)->unique();
            $table->string('status', 30)->default('valid')->index();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('revocation_reason')->nullable();
            $table->timestamps();
            $table->index(['event_id', 'status']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::table('attendees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_item_id');
            $table->dropConstrainedForeignId('order_id');
        });
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
