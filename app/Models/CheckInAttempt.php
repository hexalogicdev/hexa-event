<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckInAttempt extends Model
{
    protected $fillable = [
        'event_id', 'attendee_id', 'user_id', 'reference', 'outcome',
        'message', 'ip_address', 'user_agent', 'attempted_at',
    ];

    protected $casts = ['attempted_at' => 'datetime'];

    public function event() { return $this->belongsTo(Event::class); }
    public function attendee() { return $this->belongsTo(Attendee::class); }
    public function officer() { return $this->belongsTo(User::class, 'user_id'); }
}
