<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ① 正常ログイン（admin）
     */
    public function test_admin_can_login()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/attendance/list');
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * ② メール未入力
     * → バリデーションメッセージ
     */
    public function test_email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * ③ パスワード未入力
     * → バリデーションメッセージ
     */
    public function test_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * ④ パスワード誤り
     * → ログイン失敗
     */
    public function test_login_fails_with_wrong_password()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * ⑤ 存在しないメール
     * → ログイン失敗
     */
    public function test_login_fails_with_unregistered_email()
    {
        $response = $this->post('/login', [
            'email' => 'none@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * ⑥ staffはadminとしてログイン不可
     */
    public function test_staff_cannot_access_admin_page()
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => $staff->email,
            'password' => 'password',
        ]);

        // staffは通常画面へ
        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($staff);
    }
}
