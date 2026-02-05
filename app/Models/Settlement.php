<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settlement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'group_id',
        'paid_from',
        'paid_to',
        'amount',
        'payment_mode',
        'note',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get the group that owns the settlement.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user who paid (paid_from).
     */
    public function paidFromUser()
    {
        return $this->belongsTo(User::class, 'paid_from');
    }

    /**
     * Get the user who received payment (paid_to).
     */
    public function paidToUser()
    {
        return $this->belongsTo(User::class, 'paid_to');
    }

    /**
     * Get the user who created the settlement.
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
