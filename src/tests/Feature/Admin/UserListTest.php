<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 管理者：スタッフ一覧ページが表示できる
     */
    public function test_スタッフ一覧ページが表示できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.staff.list'))
            ->assertStatus(200);
    }

    /**
     * 管理者：全スタッフの氏名とメールアドレスが表示される
     */
    public function test_スタッフの氏名とメールアドレスが表示される()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.staff.list'));

        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }
}
