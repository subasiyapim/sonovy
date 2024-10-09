<?php
if (!function_exists('enumToSelectInputFormat')) {
    function enumToSelectInputFormat($data, $capitalize = false): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $label = $capitalize ? ucfirst($value) : $value;
            $result[] = ['value' => $key, 'label' => $label];
        }
        return $result;
    }
}


//deprecated
/**
 * @deprecated
 */
if (!function_exists('getLocalizationList')) {
    function getLocalizationList(): array
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
}

if (!function_exists('getDataFromInputFormat')) {
    /**
     * @param $data
     * @param  [string, integer]  $key
     * @param  string  $label
     * @param $iconKey
     * @param  boolean  $isEnum
     * @return array
     */
    function getDataFromInputFormat(
        $data,
        $key = 'id',
        string $label = 'name',
        $iconKey = null,
        bool $isEnum = false
    ): array {
        $result = [];

        if ($isEnum) {
            foreach ($data as $key => $item) {
                $result[] = [
                    'value' => $key,
                    'label' => $item,
                ];
            }
        } else {
            foreach ($data as $item) {
                $result[] = [
                    'value' => $item[$key],
                    'label' => $item[$label],
                    'iconKey' => $iconKey
                        ? $iconKey === 'image'
                            ? $item[$iconKey]['thumb']
                            : $item[$iconKey]
                        : null
                ];
            }
        }


        return $result;
    }
}


