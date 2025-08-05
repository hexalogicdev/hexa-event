<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    use HasFactory;
    protected $fillable = [
        'event_id',
        'name',
        'email',
        'phone',
        'stripe_payment_id',
        'status',
        'amount',
        'qr_id',
        'checked_in',
        'checked_in_at',
    ];
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    protected $casts = [
        'checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
    ];
}
