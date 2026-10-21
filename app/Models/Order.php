<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'event_id', 'reference', 'purchaser_name', 'purchaser_email', 'purchaser_phone',
        'currency', 'subtotal', 'total', 'status', 'payment_status', 'idempotency_key',
        'completed_at', 'expires_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2', 'total' => 'decimal:2',
        'completed_at' => 'datetime', 'expires_at' => 'datetime',
    ];

    public function event() { return $this->belongsTo(Event::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function attendees() { return $this->hasMany(Attendee::class); }
    public function tickets() { return $this->hasMany(Ticket::class); }
}
