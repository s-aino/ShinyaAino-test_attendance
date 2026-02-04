<?php

namespace Database\Factories;

use App\Models\AttendanceCorrection;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceCorrectionRequestFactory extends Factory
{
    protected $model = AttendanceCorrection::class;

    public function definition(): array
    {
        return [
            'attendance_id' => Attendance::factory(),
            'user_id' => User::factory(),

            'requested_data' => [
                'clock_in' => '09:30',
                'clock_out' => '18:30',
                'breaks' => [
                    [
                        'start' => now()->format('Y-m-d 12:30:00'),
                        'end'   => now()->format('Y-m-d 13:30:00'),
                    ],
                ],
                'reason' => '打刻漏れのため',
            ],

            'status' => 'pending',
        ];
    }

    public function approve()
    {
        return $this->state(fn () => [
            'status' => 'approved',
        ]);
    }
}
