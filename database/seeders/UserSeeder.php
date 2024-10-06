<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    private static array $users = [
        [
            'name' => 'MD Admin',
            'email' => 'admin@admin.com',
            'password' => 'password',
            'phone' => '5325805080',
            'is_verified' => 1,
        ],
        [
            'name' => 'MD User',
            'email' => 'user@user.com',
            'password' => 'password',
            'phone' => '5325805080',
            'is_verified' => 1,
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::$users as $user) {
            \App\Models\User::updateOrCreate(
                [
                    'email' => $user['email']
                ],
                [
                    'name' => $user['name'],
                    'remember_token' => Str::random(10),
                    'email_verified_at' => now(),
                    'password' => Hash::make($user['password']),
                    'phone' => $user['phone'] ?? '',
                    'is_verified' => 1,
                ]
            );
        }
    }
}
