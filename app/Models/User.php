<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the groups that the user belongs to.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_users')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    /**
     * Get the expenses where the user paid.
     */
    public function paidExpenses()
    {
        return $this->hasMany(Expense::class, 'paid_by');
    }

    /**
     * Get the expenses created by the user.
     */
    public function createdExpenses()
    {
        return $this->hasMany(Expense::class, 'created_by');
    }

    /**
     * Get the expense participants for the user.
     */
    public function expenseParticipants()
    {
        return $this->hasMany(ExpenseParticipant::class);
    }

    /**
     * Get the expense splits for the user.
     */
    public function expenseSplits()
    {
        return $this->hasMany(ExpenseSplit::class);
    }

    /**
     * Get balances where user owes money (from_user_id = user_id).
     */
    public function balancesOwed()
    {
        return $this->hasMany(Balance::class, 'from_user_id');
    }

    /**
     * Get balances where user is owed money (to_user_id = user_id).
     */
    public function balancesOwedTo()
    {
        return $this->hasMany(Balance::class, 'to_user_id');
    }

    /**
     * Get settlements where user paid (paid_from = user_id).
     */
    public function settlementsPaid()
    {
        return $this->hasMany(Settlement::class, 'paid_from');
    }

    /**
     * Get settlements where user received (paid_to = user_id).
     */
    public function settlementsReceived()
    {
        return $this->hasMany(Settlement::class, 'paid_to');
    }

    /**
     * Get settlements created by the user.
     */
    public function createdSettlements()
    {
        return $this->hasMany(Settlement::class, 'created_by');
    }

    /**
     * Get groups created by the user.
     */
    public function createdGroups()
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    /**
     * Get activity logs for the user.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get auth logs for the user.
     */
    public function authLogs()
    {
        return $this->hasMany(AuthLog::class);
    }
}
