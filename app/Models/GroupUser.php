<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupUser extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'group_id',
        'user_id',
        'joined_at',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    /**
     * Get the group that owns the membership.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user who is a member.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
