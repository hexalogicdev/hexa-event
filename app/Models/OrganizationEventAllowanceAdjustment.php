<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

class OrganizationEventAllowanceAdjustment extends Model
{
    protected $fillable = ['organization_id', 'adjusted_by', 'quantity', 'reason', 'notes', 'agreement_reference', 'effective_date'];
    protected $casts = ['effective_date' => 'date', 'quantity' => 'integer'];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Allowance adjustments are immutable.'));
        static::deleting(fn () => throw new LogicException('Allowance adjustments are immutable.'));
    }

    public function organization() { return $this->belongsTo(Organization::class); }
    public function administrator() { return $this->belongsTo(User::class, 'adjusted_by'); }
}
