<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ① 正常ログイン
     */
    public function test_staff_can_login()
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * ② メール未入力
     * → バリデーションエラー表示
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
     * → バリデーションエラー表示
     */
    public function test_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * ④ パスワード誤り
     * → 認証失敗
     */
    public function test_login_fails_with_wrong_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(); // 認証失敗エラー
        $this->assertGuest();
    }

    /**
     * ⑤ 存在しないメール
     * → 認証失敗
     */
    public function test_login_fails_with_unregistered_email()
    {
        $response = $this->post('/login', [
            'email' => 'notfound@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}
