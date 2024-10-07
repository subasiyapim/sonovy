<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public static function getRolesFromInputFormat()
    {
        $items = Role::whereNot('code', 'admin')
            ->get();

        $data = [];

        foreach ($items as $item) {
            $data[$item->id] = [
                'value' => $item->id,
                'label' => self::getTranslation($item, 'name')
            ];
        }

        return $data;
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
