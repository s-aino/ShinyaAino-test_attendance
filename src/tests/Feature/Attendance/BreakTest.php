<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class BreakTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 休憩開始できる
     */
    public function test_user_can_start_break()
    {
        Carbon::setTestNow('2026-02-02 12:00:00');

        $user = User::factory()->create(['email_verified_at' => now()]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => today(),
            'clock_in' => now(),
        ]);

        $this->actingAs($user);

        $this->post('/attendance/break/start');

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_start' => '2026-02-02 12:00:00',
        ]);
    }


    /**
     * 休憩中は再度休憩できない
     */
    public function test_user_cannot_start_break_twice()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        $this->actingAs($user);

        $this->post('/attendance/break/start');
        $this->post('/attendance/break/start');

        $this->assertEquals(
            1,
            BreakTime::count()
        );
    }


    /**
     * 休憩終了できる
     */
    public function test_user_can_end_break()
    {
        Carbon::setTestNow('2026-02-02 13:00:00');

        $user = User::factory()->create(['email_verified_at' => now()]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        $break = BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->subHour(),
        ]);

        $this->actingAs($user);

        $this->post('/attendance/break/end');

        $this->assertDatabaseHas('breaks', [
            'id' => $break->id,
            'break_end' => '2026-02-02 13:00:00',
        ]);
    }


    /**
     * ⭐ 勤怠一覧で休憩時刻が確認できる
     */
    public function test_break_time_is_visible_on_attendance_list()
    {
        Carbon::setTestNow('2026-02-02 09:00:00');

        $user = User::factory()->create(['email_verified_at' => now()]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => today(),
            'clock_in' => now()->setTime(9, 0),
            'clock_out' => now()->setTime(18, 0),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->setTime(12, 0),
            'break_end' => now()->setTime(13, 0),
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list?month=2026-02');

        // ⭐ 合計1時間表示を確認
        $response->assertSee('1:00');
    }
}
