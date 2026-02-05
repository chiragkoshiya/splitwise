<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'created_by',
    ];

    /**
     * Get the user who created the group.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users that belong to the group.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    /**
     * Get the group_users pivot records.
     */
    public function groupUsers()
    {
        return $this->hasMany(GroupUser::class);
    }

    /**
     * Get the expenses for the group.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the balances for the group.
     */
    public function balances()
    {
        return $this->hasMany(Balance::class);
    }

    /**
     * Get the settlements for the group.
     */
    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }
}
