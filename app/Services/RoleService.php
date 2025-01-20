<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public static function getRolesFromInputFormat()
    {
        return Role::all()->map(function ($role) {
            return [
                'label' => $role->name,
                'value' => $role->id
            ];
        })->toArray();
    }

    public static function getTranslation($model, $attribute)
    {
        $translation = DB::table('role_translations')
            ->where('role_id', $model->id)
            ->where(function ($query) {
                $query->where('locale', app()->getLocale())
                    ->orWhere('locale', config('app.fallback_locale'));
            })
            ->first();

        if ($translation) {
            return $translation->$attribute;
        }

        return $model->$attribute;
    }
}
