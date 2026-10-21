<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'ticket_type_id', 'ticket_name', 'ticket_description', 'unit_price', 'quantity', 'line_total'];
    protected $casts = ['unit_price' => 'decimal:2', 'line_total' => 'decimal:2', 'quantity' => 'integer'];

    public function order() { return $this->belongsTo(Order::class); }
    public function ticketType() { return $this->belongsTo(TicketType::class); }
    public function attendees() { return $this->hasMany(Attendee::class); }
    public function tickets() { return $this->hasMany(Ticket::class); }
}
