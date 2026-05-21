<?php

namespace Database\Factories;

use App\Models\ExamSession;
use App\Models\ProctoringLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProctoringLog>
 */
class ProctoringLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_session_id' => ExamSession::factory(),
            'event_type' => $this->faker->randomElement(['fullscreen_exit', 'tab_switch', 'blur']),
            'event_timestamp' => now(),
            'metadata' => [],
        ];
    }
}
