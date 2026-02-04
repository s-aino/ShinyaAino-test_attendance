<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ① 名前がログインユーザーの氏名になっている
     */
    public function test_名前がログインユーザーの氏名になっている()
    {
        $user = User::factory()->create([
            'name' => '山田太郎',
            'email_verified_at' => now(),
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        $this->actingAs($user)
            ->get(route('attendance.show', $attendance->id))
            ->assertSee('山田太郎');
    }

    /**
     * ② 日付が選択した日付になっている
     */
    public function test_日付が選択した日付になっている()
    {
        Carbon::setTestNow('2026-02-02');

        $user = User::factory()->create(['email_verified_at' => now()]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-02-02',
        ]);

        $this->actingAs($user)
            ->get(route('attendance.show', $attendance->id))
            ->assertSee('2026年')
            ->assertSee('2月')
            ->assertSee('2日');
    }

    /**
     * ③ 出勤・退勤が打刻と一致
     */
    public function test_出勤退勤が打刻と一致している()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'date'      => today(),
            'clock_in'  => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $this->actingAs($user)
            ->get(route('attendance.show', $attendance->id))
            ->assertSee('09:00')
            ->assertSee('18:00');
    }

    /**
     * ④ 休憩が打刻と一致
     */
    public function test_休憩が打刻と一致している()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->setTime(12, 0),
            'break_end'   => now()->setTime(13, 0),
        ]);

        $this->actingAs($user)
            ->get(route('attendance.show', $attendance->id))
            ->assertSee('12:00')
            ->assertSee('13:00');
    }
}
