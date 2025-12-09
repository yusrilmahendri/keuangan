<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id'   => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'amount'        => $this->faker->randomFloat(2, 1000000, 5000000),
            'amount_saldo'  => 0,
            'periode'       => $this->faker->date(),
            'description'   => $this->faker->sentence(),
        ];
    }
}
