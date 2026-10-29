<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAllowanceRequest extends Model
{
    public const STATUSES = ['pending', 'under_review', 'approved', 'rejected', 'cancelled'];

    protected $fillable = ['organization_id', 'requested_by', 'requested_quantity', 'justification', 'status', 'reviewed_by', 'approved_quantity', 'internal_notes', 'reviewed_at', 'cancelled_at'];
    protected $casts = ['requested_quantity' => 'integer', 'approved_quantity' => 'integer', 'reviewed_at' => 'datetime', 'cancelled_at' => 'datetime'];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
