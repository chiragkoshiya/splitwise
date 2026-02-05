<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'related_type',
        'related_id',
        'from_user_id',
        'to_user_id',
        'amount',
        'type',
        'balance_before',
        'balance_after',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Get the group that owns the financial log.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user who paid (from_user_id).
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the user who received (to_user_id).
     */
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * Get the related model (polymorphic).
     */
    public function related()
    {
        return $this->morphTo('related', 'related_type', 'related_id');
    }
}
