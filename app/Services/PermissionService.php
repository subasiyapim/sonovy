<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str;

class PermissionService
{


    public static function getGroupedPermissions($user = null): array
    {
        $permissions = Permission::with('roles')->get()->keyBy('id');
        $groupedPermissions = [];

        if ($user) {
            $userPermissions = $user->roles()->with('permissions')->get()->pluck('permissions')->flatten()->keyBy('id');
        }


        foreach ($permissions as $id => $value) {
            $group = Str::beforeLast($value->code, '_');

            $group = str_replace('_', ' ', $group);

            if (!isset($groupedPermissions[$group])) {
                $groupedPermissions[$group] = [];
            }

            $groupedPermissions[$group][] = [
                'id' => $id,
                'name' => $value->name,
                'code' => $value->code,
                'checked' => $user ? $userPermissions->has($id) : false,
            ];
        }

        return $groupedPermissions;
    }

    public static function getPermissionsFromInputFormat()
    {
        $permissions = Permission::pluck('id')->toArray();

        foreach ($permissions as $id => $name) {
            $permissions[$id] = [
                'value' => $id,
                'label' => $name,
            ];
        }
        return $permissions;
    }

}
