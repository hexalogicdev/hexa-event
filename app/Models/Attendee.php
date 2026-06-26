<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
        'checked_in_by',
        'custom_answers',
    ];
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }
    protected $casts = [
        'checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
        'custom_answers' => 'array',
    ];
}
