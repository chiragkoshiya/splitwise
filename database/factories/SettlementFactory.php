<?php

namespace Database\Factories;

use App\Models\Settlement;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettlementFactory extends Factory
{
    protected $model = Settlement::class;

    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'paid_from' => User::factory(),
            'paid_to' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 100),
            'payment_mode' => 'cash',
            'created_by' => User::factory(),
        ];
    }
}
