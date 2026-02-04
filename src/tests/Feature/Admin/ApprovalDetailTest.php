<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApprovalDetailTest extends TestCase
{
    use RefreshDatabase;

    private function admin()
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_申請詳細が正しく表示される()
    {
        $admin = $this->admin();

        $attendance = Attendance::factory()->create();

        $request = AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $attendance->user_id,
            'status' => 'pending',
            'requested_data' => [
                'clock_in' => '09:00',
                'clock_out' => '18:30',
                'reason' => 'テスト理由',
            ],
        ]);

        $response = $this->actingAs($admin)
            ->get(route('stamp_correction_request.approve', $request->id));

        $response->assertStatus(200);
        $response->assertSee('テスト理由');
    }
}
