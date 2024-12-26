<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    private static array $users = [
        [
            'name' => 'MD Süper Admin',
            'email' => 'superadmin@admin.com',
            'password' => 'password',
            'phone' => '5325805080',
            'is_verified' => 1,
            'commission_rate' => 10,
            'status' => 1,
        ],
        [
            'name' => 'MD Admin',
            'email' => 'admin@admin.com',
            'password' => 'password',
            'phone' => '5325805080',
            'is_verified' => 1,
            'commission_rate' => 7,
            'status' => 1,
        ],
        [
            'name' => 'MD User',
            'email' => 'user@user.com',
            'password' => 'password',
            'phone' => '5325805080',
            'is_verified' => 1,
            'commission_rate' => 18,
            'status' => 1,
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::upsert(self::$users, ['email'], ['name', 'password', 'phone', 'is_verified', 'commission_rate']);

        User::factory(100)->create()->each(function ($user) {
            User::factory(rand(1, 5))->create(['parent_id' => $user->id])->each(function ($user) {
                User::factory(rand(1, 3))->create(['parent_id' => $user->id])->each(function ($user) {
                    User::factory(rand(1, 2))->create(['parent_id' => $user->id]);
                });
            });
        });
    }
}
