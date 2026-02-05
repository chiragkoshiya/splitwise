<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseSplit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expense_id',
        'user_id',
        'share_amount',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'share_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the expense that owns the split.
     */
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * Get the user who has this split.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
