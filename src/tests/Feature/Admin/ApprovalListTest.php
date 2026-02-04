<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApprovalListTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | 管理者作成ヘルパ
    |--------------------------------------------------------------------------
    */
    private function admin()
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 承認待ち一覧
    |--------------------------------------------------------------------------
    */

    public function test_承認待ちタブでpendingのみ表示される()
    {
        $admin = $this->admin();

        $attendance = Attendance::factory()->create();

        $pending = AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'status' => 'pending',
            'requested_data' => [
                'reason' => '承認待ち理由',
            ],
        ]);

        $approved = AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'status' => 'approved',
            'requested_data' => [
                'reason' => '承認済み理由',
            ],
        ]);

        $response = $this->actingAs($admin)
            ->get(route('correction_requests.index'));

        $response->assertStatus(200);

        // pending は表示
        $response->assertSee($pending->reason);
        // approved は表示されない
        $response->assertDontSee($approved->reason);
    }

    /*
    |--------------------------------------------------------------------------
    | 承認済み一覧
    |--------------------------------------------------------------------------
    */

    public function test_承認済みタブでapprovedのみ表示される()
    {
        $admin = $this->admin();

        $attendance = Attendance::factory()->create();

        $approved = AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'status' => 'approved',
            'requested_data' => [
                'reason' => '承認済み理由',
            ],
        ]);

        $pending = AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'status' => 'pending',
            'requested_data' => [
                'reason' => '承認待ち理由',
            ],
        ]);

        $response = $this->actingAs($admin)
            ->get(route('correction_requests.index', [
                'status' => 'approved'
            ]));

        $response->assertStatus(200);

        // approved は表示
        $response->assertSee($approved->reason);
        // pending は表示されない
        $response->assertDontSee($pending->reason);
    }
}
