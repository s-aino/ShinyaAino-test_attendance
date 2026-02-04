<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApprovalActionTest extends TestCase
{
    use RefreshDatabase;

    private function admin()
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_承認処理でステータスがapprovedになり勤怠が更新される()
    {
        $admin = $this->admin();

        $attendance = Attendance::factory()->create([
            'clock_in' => '09:00:00',
        ]);

        $request = AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $attendance->user_id,
            'status' => 'pending',
            'requested_data' => [
                'clock_in' => '09:00',
                'clock_out' => '18:30',
            ],
        ]);

        $this->actingAs($admin)
            ->put(route('stamp_correction_request.approve.update', $request->id));

        $request->refresh();
        $attendance->refresh();

        $this->assertEquals('approved', $request->status);
        $this->assertEquals('09:00:00', $attendance->clock_in->format('H:i:s'));
    }
}
