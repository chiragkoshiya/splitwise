<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseSplitFactory extends Factory
{
    protected $model = ExpenseSplit::class;

    public function definition(): array
    {
        return [
            'expense_id' => Expense::factory(),
            'user_id' => User::factory(),
            'share_amount' => 0,
        ];
    }
}
