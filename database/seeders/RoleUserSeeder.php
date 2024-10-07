<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin_role = Role::where('code', 'admin')->first();
        $admin = User::where('email', 'admin@admin.com')->first();

        $user_role = Role::where('code', 'user')->first();
        $user = User::where('email', 'user@user.com')->first();

        // Sync roles eğer varsa ekleme yapmaz. ve var olanları sime! syncWithoutDetaching
        $admin->roles()->syncWithoutDetaching($admin_role);
        $user->roles()->syncWithoutDetaching($user_role);

    }
}
