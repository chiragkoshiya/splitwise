<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseParticipant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expense_id',
        'user_id',
    ];

    public $timestamps = false;

    /**
     * Get the expense that owns the participant.
     */
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * Get the user who is a participant.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
