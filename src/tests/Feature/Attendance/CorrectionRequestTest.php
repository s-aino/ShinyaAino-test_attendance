<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | ① 出勤 > 退勤 エラー
    |--------------------------------------------------------------------------
    */
    public function test_出勤時間が退勤時間より後ならエラー()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('attendance.correction.store', $attendance->id), [
                'clock_in'  => '20:00',
                'clock_out' => '09:00',
                'reason'    => '修正',
            ])
            ->assertSessionHasErrors('clock_in');
    }

    /*
    |--------------------------------------------------------------------------
    | ② 休憩開始 > 退勤 エラー
    |--------------------------------------------------------------------------
    */
    public function test_休憩開始が退勤より後ならエラー()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('attendance.correction.store', $attendance->id), [
                'clock_in'  => '09:00',
                'clock_out' => '18:00',
                'breaks' => [
                    ['start' => '19:00', 'end' => '19:30'],
                ],
                'reason' => '修正',
            ])
            ->assertSessionHasErrors();
    }

    /*
    |--------------------------------------------------------------------------
    | ③ 休憩終了 > 退勤 エラー
    |--------------------------------------------------------------------------
    */
    public function test_休憩終了が退勤より後ならエラー()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('attendance.correction.store', $attendance->id), [
                'clock_in'  => '09:00',
                'clock_out' => '18:00',
                'breaks' => [
                    ['start' => '12:00', 'end' => '19:00'],
                ],
                'reason' => '修正',
            ])
            ->assertSessionHasErrors();
    }

    /*
    |--------------------------------------------------------------------------
    | ④ 備考未入力 エラー
    |--------------------------------------------------------------------------
    */
    public function test_備考未入力ならエラー()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('attendance.correction.store', $attendance->id), [
                'clock_in'  => '09:00',
                'clock_out' => '18:00',
            ])
            ->assertSessionHasErrors('reason');
    }

    /*
    |--------------------------------------------------------------------------
    | ⑤ 修正申請保存
    |--------------------------------------------------------------------------
    */
    public function test_修正申請が保存される()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('attendance.correction.store', $attendance->id), [
                'clock_in'  => '10:00',
                'clock_out' => '19:00',
                'reason'    => '打刻忘れ',
            ]);

        $this->assertDatabaseHas('attendance_correction_requests', [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ⑥ 承認待ち一覧表示
    |--------------------------------------------------------------------------
    */
    public function test_承認待ち一覧ページが表示できる()
    {
        $user = User::factory()->create();

        AttendanceCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'status'  => 'pending',
        ]);

        $this->actingAs($user)
            ->get(route('correction_requests.index', ['status' => 'pending']))
            ->assertStatus(200);
    }
    
    /*
    |--------------------------------------------------------------------------
    | ⑦ 承認済み一覧表示
    |--------------------------------------------------------------------------
    */
    public function test_承認済み一覧ページが表示できる()
    {
        $user = User::factory()->create();

        AttendanceCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'status'  => 'approved',
        ]);

        $this->actingAs($user)
            ->get(route('correction_requests.index', ['status' => 'approved']))
            ->assertStatus(200);
    }    
    
    /*
    |--------------------------------------------------------------------------
    | ⑧ 詳細ボタンで勤怠詳細へ遷移
    |--------------------------------------------------------------------------
    */
    public function test_詳細ボタンで勤怠詳細ページへ遷移できる()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('attendance.show', $attendance->id))
            ->assertStatus(200);
    }
}
