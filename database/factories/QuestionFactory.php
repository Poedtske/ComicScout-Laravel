<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'question' => $this->faker->paragraph,
            'anwser' => $this->faker->paragraph,
            'user_id' => $this->faker->numberBetween(1, 12),
            'category_id'=>$this->faker->numberBetween(1, 12),
        ];
    }
}
