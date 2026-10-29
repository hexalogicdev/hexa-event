<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id', 'name', 'slug', 'status', 'email', 'phone', 'country', 'timezone', 'logo', 'settings', 'activated_at', 'suspended_at', 'archived_at'];
    protected $casts = ['settings' => 'array', 'activated_at' => 'datetime', 'suspended_at' => 'datetime', 'archived_at' => 'datetime'];

    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function memberships() { return $this->hasMany(OrganizationMembership::class); }
    public function users() { return $this->belongsToMany(User::class, 'organization_memberships')->withPivot(['role', 'status', 'invited_by', 'joined_at'])->withTimestamps(); }
    public function events() { return $this->hasMany(Event::class); }
    public function invitations() { return $this->hasMany(OrganizationInvitation::class); }
    public function allowanceAdjustments() { return $this->hasMany(OrganizationEventAllowanceAdjustment::class); }
    public function allowanceRequests() { return $this->hasMany(EventAllowanceRequest::class); }
}
