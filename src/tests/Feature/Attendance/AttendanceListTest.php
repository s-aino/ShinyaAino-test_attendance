<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | ① 一覧ページ表示
    |--------------------------------------------------------------------------
    */
    public function test_一覧ページが表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('attendance.list'))
            ->assertStatus(200);
    }

    /*
    |--------------------------------------------------------------------------
    | ② その月の全日（1ヶ月分）表示される
    |--------------------------------------------------------------------------
    */
    public function test_1ヶ月分の日付が全て表示される()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('attendance.list'));

        $days = now()->daysInMonth;

        for ($i = 1; $i <= $days; $i++) {
            $response->assertSee(str_pad($i, 2, '0', STR_PAD_LEFT));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ③ 自分の勤怠がすべて表示される
    |--------------------------------------------------------------------------
    */
    public function test_複数日の勤怠がすべて表示される()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'clock_in' => '09:00:00',
        ]);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today()->addDay(),
            'clock_in' => '10:00:00',
        ]);

        $this->actingAs($user)
            ->get(route('attendance.list'))
            ->assertSee('09:00')
            ->assertSee('10:00');
    }

    /*
    |--------------------------------------------------------------------------
    | ④ 休憩合計・勤務合計が表示される
    |--------------------------------------------------------------------------
    */
    public function test_休憩時間と勤務時間の合計が表示される()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => today()->setTime(12, 0),
            'break_end'   => today()->setTime(13, 0),
        ]);

        $this->actingAs($user)
            ->get(route('attendance.list'))
            ->assertSee('1:00')   // 休憩合計
            ->assertSee('8:00');  // 勤務時間
    }

    /*
    |--------------------------------------------------------------------------
    | ⑤ 前月表示
    |--------------------------------------------------------------------------
    */
    public function test_前月ボタンで前月が表示される()
    {
        $user = User::factory()->create();

        $prev = now()->subMonth()->format('Y/m');

        $this->actingAs($user)
            ->get(route('attendance.list', ['month' => now()->subMonth()->format('Y-m')]))
            ->assertSee($prev);
    }

    /*
    |--------------------------------------------------------------------------
    | ⑥ 翌月表示
    |--------------------------------------------------------------------------
    */
    public function test_翌月ボタンで翌月が表示される()
    {
        $user = User::factory()->create();

        $next = now()->addMonth()->format('Y/m');

        $this->actingAs($user)
            ->get(route('attendance.list', ['month' => now()->addMonth()->format('Y-m')]))
            ->assertSee($next);
    }

    /*
    |--------------------------------------------------------------------------
    | ⑦ 詳細ページ遷移
    |--------------------------------------------------------------------------
    */
    public function test_詳細ページに遷移できる()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        $this->actingAs($user)
            ->get(route('attendance.show', $attendance->id))
            ->assertStatus(200);
    }
}
