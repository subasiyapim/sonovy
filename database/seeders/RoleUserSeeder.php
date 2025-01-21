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
        $admin = User::where('email', 'admin@admin.com')?->first();

        $super_admin_role = Role::where('code', 'super_admin')->first();
        $super_admin = User::where('email', 'superadmin@admin.com')?->first();

        $user_role = Role::where('code', 'user')->first();
        $user = User::where('email', 'user@user.com')?->first();

        $super_admin?->roles()->syncWithoutDetaching($super_admin_role);
        $admin?->roles()->syncWithoutDetaching($admin_role);
        $user?->roles()->syncWithoutDetaching($user_role);

    }
}
