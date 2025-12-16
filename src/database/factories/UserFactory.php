<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // テスト用固定
            'role' => 'staff', // staff / admin
            'remember_token' => Str::random(10),
        ];
    }

    // 管理者用
    public function admin(): static
    {
        return $this->state(fn() => [
            'role' => 'admin',
        ]);
    }
}
