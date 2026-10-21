<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('attendees')->whereNull('order_id')->orderBy('id')->eachById(function ($attendee) {
            $amount = is_numeric($attendee->amount) ? (float) $attendee->amount : 0;
            $orderId = DB::table('orders')->insertGetId([
                'event_id' => $attendee->event_id,
                'reference' => 'ORD-' . Str::upper(Str::random(12)),
                'purchaser_name' => $attendee->name,
                'purchaser_email' => $attendee->email,
                'purchaser_phone' => $attendee->phone,
                'currency' => 'GBP', 'subtotal' => $amount, 'total' => $amount,
                'status' => $attendee->status === 'confirmed' ? 'completed' : 'cancelled',
                'payment_status' => $amount > 0 ? 'legacy' : 'not_required',
                'completed_at' => $attendee->status === 'confirmed' ? ($attendee->created_at ?? now()) : null,
                'created_at' => $attendee->created_at ?? now(), 'updated_at' => $attendee->updated_at ?? now(),
            ]);
            $ticketType = $attendee->ticket_type_id ? DB::table('ticket_types')->find($attendee->ticket_type_id) : null;
            $itemId = DB::table('order_items')->insertGetId([
                'order_id' => $orderId, 'ticket_type_id' => $attendee->ticket_type_id,
                'ticket_name' => $ticketType?->name ?? 'General admission',
                'ticket_description' => $ticketType?->description,
                'unit_price' => $amount, 'quantity' => 1, 'line_total' => $amount,
                'created_at' => $attendee->created_at ?? now(), 'updated_at' => $attendee->updated_at ?? now(),
            ]);
            DB::table('attendees')->where('id', $attendee->id)->update(['order_id' => $orderId, 'order_item_id' => $itemId]);
            DB::table('tickets')->insert([
                'event_id' => $attendee->event_id, 'order_id' => $orderId, 'order_item_id' => $itemId,
                'attendee_id' => $attendee->id, 'token' => $attendee->qr_id,
                'status' => $attendee->status === 'confirmed' ? 'valid' : 'revoked',
                'issued_at' => $attendee->created_at ?? now(),
                'revoked_at' => $attendee->status === 'confirmed' ? null : now(),
                'revocation_reason' => $attendee->status === 'confirmed' ? null : 'Legacy registration was not confirmed.',
                'created_at' => $attendee->created_at ?? now(), 'updated_at' => $attendee->updated_at ?? now(),
            ]);
        });
    }

    public function down(): void
    {
        // Backfilled orders are intentionally retained to avoid destructive rollback of live registrations.
    }
};
