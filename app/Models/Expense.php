<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'group_id',
        'title',
        'total_amount',
        'paid_by',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the group that owns the expense.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user who paid for the expense.
     */
    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Get the user who created the expense.
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the participants for the expense.
     */
    public function participants()
    {
        return $this->hasMany(ExpenseParticipant::class);
    }

    /**
     * Get the users participating in the expense.
     */
    public function participantUsers()
    {
        return $this->belongsToMany(User::class, 'expense_participants');
    }

    /**
     * Get the splits for the expense.
     */
    public function splits()
    {
        return $this->hasMany(ExpenseSplit::class);
    }

    /**
     * Get the users with their split amounts.
     */
    public function splitUsers()
    {
        return $this->belongsToMany(User::class, 'expense_splits')
            ->withPivot('share_amount');
    }
}
