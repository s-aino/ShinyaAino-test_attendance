<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\VerifyEmail;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | 認証メール送信される
    |--------------------------------------------------------------------------
    */
    public function test_登録時に認証メールが送信される()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        event(new Registered($user));

        Notification::assertSentTo($user, VerifyEmail::class);
    }


    /*
    |--------------------------------------------------------------------------
    | 未認証はアクセス不可
    |--------------------------------------------------------------------------
    */
    public function test_未認証ユーザーは勤怠ページにアクセスできない()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertRedirect('/email/verify');
    }
    /*
    |--------------------------------------------------------------------------
    | 認証誘導ページが表示される
    |--------------------------------------------------------------------------
    */
    public function test_認証誘導ページが表示される()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->get('/email/verify')
            ->assertOk()
            ->assertSee('認証はこちらから'); 
    }


    /*
    |--------------------------------------------------------------------------
    | 認証リンクで verified になる
    |--------------------------------------------------------------------------
    */
    public function test_認証リンクでメール認証が完了する()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirectContains('/attendance');

        $this->assertTrue(
            $user->fresh()->hasVerifiedEmail()
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ④ 認証済みは入れる
    |--------------------------------------------------------------------------
    */
    public function test_認証済みユーザーは勤怠ページにアクセスできる()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertOk();
    }
}
