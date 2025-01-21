<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    private static array $users = [
/*         [
            'name' => 'MD Süper Admin',
            'email' => 'superadmin@admin.com',
            'password' => 'password',
            'phone' => '5325805080',
            'is_verified' => 1,
            'commission_rate' => 10,
            'status' => 1,
            'deleted_at' => null,
        ], */
        [
            'name' => 'MD Admin',
            'email' => 'admin@admin.com',
            'password' => 'password',
            'phone' => '5325805080',
            'is_verified' => 1,
            'commission_rate' => 7,
            'status' => 1,
            'deleted_at' => null,
        ],
/*         [
            'name' => 'MD User',
            'email' => 'user@user.com',
            'password' => 'password',
            'phone' => '5325805080',
            'is_verified' => 1,
            'commission_rate' => 18,
            'status' => 1,
            'deleted_at' => null,
        ] */
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::$users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => Hash::make($user['password']),
                    'phone' => $user['phone'],
                    'is_verified' => $user['is_verified'],
                    'commission_rate' => $user['commission_rate'],
                    'status' => $user['status'],
                    'deleted_at' => $user['deleted_at'],
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'uuid' => Str::uuid(),
                ]
            );
        }

//        User::factory(10)->create()->each(function ($user) {
//            User::factory(rand(1, 3))->create(['parent_id' => $user->id])->each(function ($user) {
//                User::factory(rand(1, 2))->create(['parent_id' => $user->id])->each(function ($user) {
//                    User::factory(rand(1, 2))->create(['parent_id' => $user->id]);
//                });
//            });
//        });
    }
}
