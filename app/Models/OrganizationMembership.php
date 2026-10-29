<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationMembership extends Model
{
    protected $fillable = ['organization_id', 'user_id', 'role', 'status', 'invited_by', 'joined_at'];
    protected $casts = ['joined_at' => 'datetime'];
    public function organization() { return $this->belongsTo(Organization::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function inviter() { return $this->belongsTo(User::class, 'invited_by'); }
}

