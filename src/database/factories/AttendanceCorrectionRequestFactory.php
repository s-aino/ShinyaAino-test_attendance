<?php

namespace Database\Factories;

use App\Models\AttendanceCorrectionRequest;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceCorrectionRequestFactory extends Factory
{
    protected $model = AttendanceCorrectionRequest::class;

    public function definition(): array
    {
        return [
            'attendance_id' => Attendance::factory(),
            'user_id' => User::factory(),
            'requested_clock_in' => '09:30:00',
            'requested_clock_out' => '18:30:00',
            'requested_breaks' => [
                [
                    'break_start' => '12:30:00',
                    'break_end' => '13:30:00',
                ],
            ],
            'reason' => '打刻漏れのため',
            'status' => 'pending',
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
        ]);
    }
}