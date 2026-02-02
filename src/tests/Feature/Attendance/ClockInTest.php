<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ClockInTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ① 出勤成功
     */
    public function test_user_can_clock_in()
    {
        Carbon::setTestNow('2026-02-02 09:00:00');

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->post('/attendance/start');

        $response->assertRedirect('/attendance');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => '2026-02-02',
            'clock_in' => '2026-02-02 09:00:00',
        ]);
    }


    /**
     * ② 同日2回押せない
     */
    public function test_user_cannot_clock_in_twice_in_same_day()
    {
        Carbon::setTestNow('2026-02-02 09:00:00');

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $this->post('/attendance/start');
        $this->post('/attendance/start');

        $this->assertEquals(
            1,
            Attendance::where('user_id', $user->id)->count()
        );
    }


    /**
     * ③ 未ログインは出勤不可
     */
    public function test_guest_cannot_clock_in()
    {
        $response = $this->post('/attendance/start');

        $response->assertRedirect('/login');
    }


    /**
     * ④ 勤怠一覧で確認
     */
    public function test_clock_in_time_is_visible_on_list()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        Attendance::create([
            'user_id' => $user->id,
            'date' => today(),
            'clock_in' => '09:00:00',
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $response->assertSee('09:00');
    }
}
