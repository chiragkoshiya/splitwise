<?php

namespace Database\Factories;

use App\Models\Balance;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BalanceFactory extends Factory
{
    protected $model = Balance::class;

    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'from_user_id' => User::factory(),
            'to_user_id' => User::factory(),
            'amount' => 0,
        ];
    }
}
