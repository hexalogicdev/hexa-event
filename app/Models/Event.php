<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'location',
        'event_start_date',
        'event_end_date',
        'booking_start_date',
        'booking_end_date',
        'max_attendees',
        'event_code',
        'checkin_password',
        'status',
        'visibility',
        'banner_image',
        'registration_fields',
        'price',
        'is_active'
    ];
    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }
    protected $casts = [
        'event_start_date' => 'datetime',
        'event_end_date' => 'datetime',
        'booking_start_date' => 'datetime',
        'booking_end_date' => 'datetime',
        'registration_fields' => 'array',
        'is_active' => 'boolean',
    ];
}
