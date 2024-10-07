<?php

namespace App\Services;

use App\Http\Resources\Panel\LanguageResource;
use App\Models\Language;

class LanguageServices
{
    public static function search($search): mixed
    {
        return Language::whereAny(
            ['name', 'iso2', 'phone_code'],
            'LIKE',
            "%$search%"
        )
            ->get();
    }

    public static function get()
    {
        return LanguageResource::collection(Language::active()->get())->resolve();
    }

    public static function getActiveLanguagesFromInputFormat()
    {
        $languages = self::get();

        $result = [];
        foreach ($languages as $language) {
            $result[] = [
                'value' => $language['id'],
                'label' => $language['name'],
                'iconKey' => $language['emoji'],
            ];
        }
        return $result;

    }
}
