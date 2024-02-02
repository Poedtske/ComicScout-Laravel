<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'user_id' => $this->faker->numberBetween(1, 12),
            'date' => $this->faker->date('Y-m-d'),
            'time' => $this->faker->time('H:i'),
        ];
    }
}
