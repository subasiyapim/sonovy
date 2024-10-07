<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Support\Str;

class PermissionService
{


    public static function getGroupedPermissions(): array
    {
        $permissions = Permission::with('roles')->get()->keyBy('id');

        $groupedPermissions = [];

        foreach ($permissions as $id => $value) {
            $group = Str::beforeLast($value->code, '_');

            $group = str_replace('_', ' ', $group);

            if (!isset($groupedPermissions[$group])) {
                $groupedPermissions[$group] = [];
            }

            $groupedPermissions[$group][$id] = $value->name;
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
