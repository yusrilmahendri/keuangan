<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id'       => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'amount'            => $this->faker->randomFloat(2, 50000, 1000000),
            'transaction_date'  => $this->faker->date(),
            'description'       => $this->faker->sentence(),
        ];
    }
}
