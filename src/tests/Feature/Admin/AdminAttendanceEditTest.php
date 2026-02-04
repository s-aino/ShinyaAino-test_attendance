<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAttendanceEditTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | 管理者：勤怠詳細表示
    |--------------------------------------------------------------------------
    */

    public function test_勤怠詳細ページが表示できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $attendance = Attendance::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.attendance.show', $attendance->id))
            ->assertStatus(200);
    }

    /*
    |--------------------------------------------------------------------------
    | 出勤 > 退勤 → エラー
    |--------------------------------------------------------------------------
    */

    public function test_出勤が退勤より後ならエラー()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $attendance = Attendance::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.attendance.update', $attendance->id), [
                'clock_in'  => '18:00',
                'clock_out' => '09:00',
                'reason'    => '修正',
            ])
            ->assertSessionHasErrors('clock_in');
    }

    /*
    |--------------------------------------------------------------------------
    | 休憩開始 > 退勤 → エラー
    |--------------------------------------------------------------------------
    */

    public function test_休憩開始が退勤より後ならエラー()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $attendance = Attendance::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.attendance.update', $attendance->id), [
                'clock_in'  => '09:00',
                'clock_out' => '18:00',
                'breaks' => [
                    ['start' => '19:00', 'end' => '19:30']
                ],
                'reason' => '修正',
            ])
            ->assertSessionHasErrors();
    }

    /*
    |--------------------------------------------------------------------------
    | 備考未入力 → エラー
    |--------------------------------------------------------------------------
    */

    public function test_備考未入力ならエラー()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $attendance = Attendance::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.attendance.update', $attendance->id), [
                'clock_in'  => '09:00',
                'clock_out' => '18:00',
            ])
            ->assertSessionHasErrors('reason');
    }
}
