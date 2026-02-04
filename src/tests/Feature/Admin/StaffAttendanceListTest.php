<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class StaffAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 管理者：スタッフ別勤怠一覧ページが表示できる
     */
    public function test_スタッフ別勤怠一覧ページが表示できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.attendance.staff.show', $staff->id))
            ->assertStatus(200);
    }

    /**
     * 管理者：当月の勤怠情報が表示される
     */
    public function test_当月の勤怠情報が表示される()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $staff->id,
            'date' => Carbon::now()->startOfMonth(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.attendance.staff.show', $staff->id))
            ->assertSee($attendance->date->format('m/d'));
    }

    /**
     * 管理者：前月ボタン押下で前月の勤怠が表示される
     */
    public function test_前月の勤怠情報が表示される()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create();

        $lastMonth = Carbon::now()->subMonth();

        Attendance::factory()->create([
            'user_id' => $staff->id,
            'date' => $lastMonth,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.attendance.staff.show', [
                'id' => $staff->id,
                'month' => $lastMonth->format('Y-m')
            ]))
            ->assertSee($lastMonth->format('m/d'));
    }

    /**
     * 管理者：詳細ボタン押下で勤怠詳細ページへ遷移できる
     */
    public function test_詳細ボタン押下で勤怠詳細ページへ遷移できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $staff->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.attendance.show', $attendance->id))
            ->assertStatus(200);
    }
}
