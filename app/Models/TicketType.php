<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'quantity',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    public function getRemainingAttribute(): ?int
    {
        return $this->quantity === null
            ? null
            : max($this->quantity - ($this->attendees_count ?? $this->attendees()->count()), 0);
    }
}
