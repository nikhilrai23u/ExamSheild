<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_id' => Exam::factory(),
            'question_text' => $this->faker->sentence().'?',
            'type' => 'mcq',
            'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
            'correct_answer' => '0',
            'points' => 1,
        ];
    }
}
