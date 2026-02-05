<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'paid_by' => User::factory(),
            'created_by' => function (array $attributes) {
                return $attributes['paid_by'];
            },
            'title' => $this->faker->sentence(3),
            'total_amount' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
