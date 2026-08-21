<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 指定された５件を定義
        $usersData = [
            [
                'name' => '山田太郎',
                'email' => 'yamada@example.com',
                'password' => 'password',
            ],
            [
                'name' => '鈴木花子',
                'email' => 'suzuki@example.com',
                'password' => 'password',
            ],
            [
                'name' => '田中一郎',
                'email' => 'tanaka@example.com',
                'password' => 'password',
            ],
            [
                'name' => '佐藤美咲',
                'email' => 'sato@example.com',
                'password' => 'password',
            ],
            [
                'name' => '高橋健太',
                'email' => 'takahashi@example.com',
                'password' => 'password',
            ],
        ];

        // ループ処理でテーブルに投入
        foreach ($usersData as $userData) {
            User::firstOrCreate(
                [
                    'email' => $userData['email'],
                ],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                ]
            );
        }

    }
}
