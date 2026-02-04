<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DateTimeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ✅ 現在の日時情報が画面に正しい形式で表示される
     * （例：時刻が "23:30" 形式で表示される）
     */
    public function test_現在時刻が画面に表示される()
    {
        // 画面に出る「現在時刻」を固定
        Carbon::setTestNow('2026-02-02 23:30:00');

        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);

        // ✅ UIが "23:30" 表示ならこれ（あなたのスクショがこの形式）
        $response->assertSee('23:30');

        // ✅ 日付も表示してるなら（例：2026年2月2日）
        // 表示形式が違うなら、この文字列はあなたのBladeに合わせて調整してOK
        $response->assertSee('2026年2月2日');
    }
}
