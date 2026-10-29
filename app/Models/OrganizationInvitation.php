<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationInvitation extends Model
{
    protected $fillable = ['organization_id', 'name', 'email', 'role', 'token_hash', 'invited_by', 'expires_at', 'accepted_at', 'revoked_at'];
    protected $casts = ['expires_at' => 'datetime', 'accepted_at' => 'datetime', 'revoked_at' => 'datetime'];
    public function organization() { return $this->belongsTo(Organization::class); }
    public function inviter() { return $this->belongsTo(User::class, 'invited_by'); }
    public function isUsable(): bool { return ! $this->accepted_at && ! $this->revoked_at && $this->expires_at->isFuture(); }
}

