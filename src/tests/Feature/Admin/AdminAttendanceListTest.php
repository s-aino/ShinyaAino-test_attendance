<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;
    public function test_管理者が勤怠一覧ページを表示できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/attendance/list')
            ->assertStatus(200);
    }
    public function test_当日出勤している従業員のみ表示される()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 出勤者
        $worker = User::factory()->create(['name' => '山田 太郎']);

        Attendance::factory()->create([
            'user_id' => $worker->id,
            'date' => today(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        // 未出勤者
        $absent = User::factory()->create(['name' => '欠勤 花子']);

        $this->actingAs($admin)
            ->get('/admin/attendance/list')
            ->assertSee('山田 太郎')
            ->assertDontSee('欠勤 花子'); // ← これが超重要
    }
    public function test_前日の勤怠一覧へ移動できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $yesterday = today()->subDay()->format('Y-m-d');

        $this->actingAs($admin)
            ->get("/admin/attendance/list?date={$yesterday}")
            ->assertStatus(200);
    }
}
