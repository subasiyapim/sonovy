<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class LocaleService
{

    public static function getLanguageFile(string $locale = null, array $keysToExtract = []): array
    {
        $locale = $locale ?? (Session::has('appLocale') ? Session::get('appLocale') : App::getLocale());

        $translationFiles = File::files(base_path('lang/' . $locale));

        return collect($translationFiles)->map(function ($file) use ($keysToExtract, $locale) {
            $key = $file->getFilenameWithoutExtension();

            if (empty($keysToExtract) || in_array($key, $keysToExtract)) {
                return [$key => Lang::get($key, [], $locale)];
            }
        })->filter()->collapse()->toArray();
    }
    
    public static function getLocalizationList()
    {
        return collect(File::directories(lang_path()))->map(function ($directory) {
            return basename($directory);
        })->toArray();
    }

    public static function getLocalizationListFromInputFormat()
    {
        $languages = [];
        $directories = glob(lang_path('*'), GLOB_ONLYDIR);

        foreach ($directories as $directory) {
            $languages[] = basename($directory);
        }

        return array_map(function ($item) {
            return [
                'value' => $item,
                'label' => strtoupper($item)
            ];
        }, $languages);
    }

    public static function switchLocale($locale)
    {
        if (in_array($locale, self::getLocalizationList())) {
            session()->put('appLocale', $locale);
            app()->setLocale($locale);
        }

        return redirect()->back();
    }
}
