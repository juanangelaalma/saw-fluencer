<?php

namespace Database\Factories;

use App\Models\Criterion;
use App\Models\SubCriterion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubCriterion>
 */
class SubCriterionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'criterion_id' => Criterion::factory(),
            'level' => fake()->numberBetween(1, 5),
            'label' => fake()->word(),
            'min_value' => fake()->numberBetween(0, 50),
            'max_value' => fake()->numberBetween(51, 100),
        ];
    }
}
