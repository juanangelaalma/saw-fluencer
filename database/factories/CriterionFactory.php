<?php

namespace Database\Factories;

use App\Models\Criterion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Criterion>
 */
class CriterionFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'code' => Str::upper(fake()->unique()->bothify('C##')),
            'name' => Str::title($name),
            'weight' => fake()->numberBetween(1, 100),
            'type' => fake()->randomElement([Criterion::TYPE_BENEFIT, Criterion::TYPE_COST]),
        ];
    }
}
