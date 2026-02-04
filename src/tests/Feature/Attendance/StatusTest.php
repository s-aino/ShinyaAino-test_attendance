<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class StatusTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ✅ 勤務外の場合、勤怠ステータスが正しく表示される
     */
    public function test_勤務外の場合ステータスが勤務外と表示される()
    {
        Carbon::setTestNow('2026-02-02 09:00:00');

        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    /**
     * ✅ 出勤中の場合、勤怠ステータスが正しく表示される
     */
    public function test_出勤中の場合ステータスが出勤中と表示される()
    {
        Carbon::setTestNow('2026-02-02 09:00:00');

        $user = User::factory()->create(['email_verified_at' => now()]);

        Attendance::create([
            'user_id'  => $user->id,
            'date'     => today(),
            'clock_in' => now(),
            'clock_out' => null,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    /**
     * ✅ 休憩中の場合、勤怠ステータスが正しく表示される
     */
    public function test_休憩中の場合ステータスが休憩中と表示される()
    {
        Carbon::setTestNow('2026-02-02 12:00:00');

        $user = User::factory()->create(['email_verified_at' => now()]);

        $attendance = Attendance::create([
            'user_id'  => $user->id,
            'date'     => today(),
            'clock_in' => '09:00:00',
            'clock_out' => null,
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start'   => now(),
            'break_end'     => null, // ←休憩中
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    /**
     * ✅ 退勤済の場合、勤怠ステータスが正しく表示される
     */
    public function test_退勤済の場合ステータスが退勤済と表示される()
    {
        Carbon::setTestNow('2026-02-02 18:00:00');

        $user = User::factory()->create(['email_verified_at' => now()]);

        Attendance::create([
            'user_id'   => $user->id,
            'date'      => today(),
            'clock_in'  => '09:00:00',
            'clock_out' => now(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }
}
