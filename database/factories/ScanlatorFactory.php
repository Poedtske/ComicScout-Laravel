<?php

namespace Database\Factories;

use App\Models\Scanlator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ScanlatorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Scanlator::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'url' => $this->faker->sentence,
            'logo'=> $this->faker->sentence,
        ];
    }
}
