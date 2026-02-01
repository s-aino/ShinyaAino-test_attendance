<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => '社長',
                'email' => 'syacho@mail',
            ],
            [
                'name' => '課長',
                'email' => 'kacho@mail',
            ],
            [
                'name' => '管理者',
                'email' => 'admin@mail',
            ],

        ];

        foreach ($admins as $admin) {

            User::updateOrCreate(
                ['email' => $admin['email']], // 重複防止
                [
                    'name' => $admin['name'],
                    'password' => Hash::make('11111111'),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
