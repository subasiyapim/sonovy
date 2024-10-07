<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Translations\RoleTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{

    public static array $roles = [
        [
            'code' => 'maintenance',
            'tr' => ['name' => 'Bakım',],
            'en' => ['name' => 'Maintenance',],
        ],
        [
            'code' => 'super_admin',
            'tr' => ['name' => 'Süper Admin',],
            'en' => ['name' => 'Super Admin',],
        ],
        [
            'code' => 'admin',
            'tr' => ['name' => 'Admin',],
            'en' => ['name' => 'Admin',],
        ],
        [
            'code' => 'user',
            'tr' => ['name' => 'Kullanıcı',],
            'en' => ['name' => 'User',],
        ]

    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        foreach (self::$roles as $role) {
            Role::firstOrCreate(
                [
                    'code' => $role['code'],
                ],
                [
                    'tr' => $role['tr'],
                    'en' => $role['en'],
                ]
            );
        }
    }
}
