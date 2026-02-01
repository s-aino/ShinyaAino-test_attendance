<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StaffUserSeeder extends Seeder
{
    public function run(): void
    {
        $staffs = [
            ['name' => '山田 太郎', 'email' => 'yamada@mail'],
            ['name' => '渡辺 よしこ', 'email' => 'watanabe@mail'],
            ['name' => '鈴木 一郎', 'email' => 'suzuki@mail'],
            ['name' => '高橋 花子', 'email' => 'takahashi@mail'],
            ['name' => '鈴木 次郎', 'email' => 'suzukijiro@mail'],
            ['name' => '相野', 'email' => 'aino@mail'],
        ];

        foreach ($staffs as $staff) {

            User::updateOrCreate(
                ['email' => $staff['email']],
                [
                    'name' => $staff['name'],
                    'password' => Hash::make('00000000'),
                    'role' => 'staff',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
