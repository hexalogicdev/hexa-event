<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'event_id', 'order_id', 'order_item_id', 'attendee_id', 'token', 'status',
        'issued_at', 'revoked_at', 'revocation_reason',
    ];
    protected $casts = ['issued_at' => 'datetime', 'revoked_at' => 'datetime'];

    public function event() { return $this->belongsTo(Event::class); }
    public function order() { return $this->belongsTo(Order::class); }
    public function orderItem() { return $this->belongsTo(OrderItem::class); }
    public function attendee() { return $this->belongsTo(Attendee::class); }
}
