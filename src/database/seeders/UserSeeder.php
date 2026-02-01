<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 👑 管理者
        User::create([
            'name' => '会長',
            'email' => 'kaicho@mail',
            'password' => Hash::make('11111111'),
            'role' => 'admin',
            'email_verified_at' => now(), // ⭐ 超重要
        ]);

        // 👤 一般スタッフ
        User::create([
            'name' => '上田',
            'email' => 'ueda@mail',
            'password' => Hash::make('00000000'),
            'role' => 'staff',
            'email_verified_at' => now(), // ⭐ 超重要
        ]);
    }
}
