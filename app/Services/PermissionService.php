<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str;

class PermissionService
{


    public static function getGroupedPermissions($user = null): array
    {
        $groupedPermissions = [];

        if ($user) {
            $userPermissions = self::getUserPermissions($user, 'id');
        }

        $permissions = Permission::with('roles')->get()->keyBy('id');


        foreach ($permissions as $id => $value) {
            $group = Str::beforeLast($value->code, '_');

            $group = Str::title(Str::replace('_', ' ', $group));

            if (!isset($groupedPermissions[$group])) {
                $groupedPermissions[$group] = [];
            }

            $groupedPermissions[$group][] = [
                'id' => $id,
                'name' => $value->name,
                'code' => $value->code,
                'checked' => $user && $userPermissions && in_array($id, $userPermissions),
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

    public static function getUserPermissions(User $user, $key = 'code')
    {
        $excludedPermissions = $user->permissions->pluck('id')->toArray();

        return $user->roles()
            ->with('permissions')
            ->get()
            ->flatMap(function ($role) {
                return $role->permissions;
            })
            ->whereNotIn('id', $excludedPermissions)
            ->pluck($key)
            ->unique()
            ->values()
            ->all();


    }

}
