<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Saldo>
 */
class SaldoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount'        => $this->faker->randomFloat(2, 1000000, 5000000),
            'amount_count'  => 0,
            'periode_saldo' => $this->faker->date(),
            'description'   => $this->faker->sentence(),
        ];
    }
}
