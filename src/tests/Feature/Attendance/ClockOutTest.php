<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ✅ 退勤成功
     */
    public function test_user_can_clock_out()
    {
        // 退勤する時刻を固定（18:00）
        Carbon::setTestNow('2026-02-02 18:00:00');

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // すでに出勤済みの状態を作る（clock_out は null）
        Attendance::create([
            'user_id'   => $user->id,
            'date'      => today(),
            'clock_in'  => Carbon::parse('2026-02-02 09:00:00'),
            'clock_out' => null,
        ]);

        $this->actingAs($user);

        $response = $this->post('/attendance/end');

        $response->assertRedirect('/attendance');

        $this->assertDatabaseHas('attendances', [
            'user_id'   => $user->id,
            'date'      => today()->toDateString(),
            'clock_out' => '2026-02-02 18:00:00',
        ]);
    }

    /**
     * ✅ 出勤してない日は退勤できない
     */
    public function test_user_cannot_clock_out_without_clock_in()
    {
        Carbon::setTestNow('2026-02-02 18:00:00');

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->post('/attendance/end');

        // コントローラが return back() なので 302 で元に戻る想定
        $response->assertStatus(302);

        // DBに何も増えてないことを確認してもOK（任意）
        $this->assertDatabaseMissing('attendances', [
            'user_id' => $user->id,
            'date'    => today()->toDateString(),
        ]);
    }

    /**
     * ✅ 同日に2回退勤できない（すでに clock_out があるなら変化しない）
     */
    public function test_user_cannot_clock_out_twice_in_same_day()
    {
        Carbon::setTestNow('2026-02-02 18:00:00');

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Attendance::create([
            'user_id'   => $user->id,
            'date'      => today(),
            'clock_in'  => Carbon::parse('2026-02-02 09:00:00'),
            'clock_out' => Carbon::parse('2026-02-02 17:00:00'), // 既に退勤済み
        ]);

        $this->actingAs($user);

        $response = $this->post('/attendance/end');
        $response->assertStatus(302);

        // 17:00のまま（上書きされてない）を確認
        $this->assertDatabaseHas('attendances', [
            'user_id'   => $user->id,
            'date'      => today()->toDateString(),
            'clock_out' => '2026-02-02 17:00:00',
        ]);
    }

    /**
     * ✅ 勤怠一覧で退勤時刻が確認できる
     */
    public function test_clock_out_time_is_visible_on_list()
    {
        Carbon::setTestNow('2026-02-02 18:00:00');

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Attendance::create([
            'user_id'   => $user->id,
            'date'      => today(),
            'clock_in'  => Carbon::parse('2026-02-02 09:00:00'),
            'clock_out' => Carbon::parse('2026-02-02 18:00:00'),
        ]);

        $this->actingAs($user);

        // AttendanceListController が month パラメータを見るので合わせる
        $response = $this->get('/attendance/list?month=2026-02');

        $response->assertStatus(200);
        $response->assertSee('18:00');
    }

}
