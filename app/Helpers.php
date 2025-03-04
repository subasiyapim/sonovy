<?php

use Illuminate\Support\Number;

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
                            ? $item[$iconKey]['thumb'] ?? asset('assets/images/avatar.png')
                            : $item[$iconKey]
                        : null
                ];
            }
        }


        return $result;
    }
}

if (!function_exists('maskString')) {
    function maskString($string)
    {
        $length = mb_strlen($string);
        if ($length <= 2) {
            return $string;
        }
        return substr_replace($string, str_repeat('*', $length - 2), 0, $length - 2);
    }
}

if (!function_exists('emailMasking')) {
    function emailMasking(&$email)
    {
        $email_arr = explode('@', $email);

        $masked_local = maskString($email_arr[0]);
        $masked_domain = maskString($email_arr[1]);

        $masked_email = $masked_local.'@'.$masked_domain;

        $email = $masked_email;

        return $email;
    }
}

if (!function_exists('totalDuration')) {
    function totalDuration($songs, $formatted = false)
    {
        $totalSeconds = $songs->sum(function ($song) {
            $duration = $song->details['details']['duration'] ?? 0;
            return is_numeric($duration) ? (float) $duration : 0;
        });

        if ($formatted) {
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $seconds = $totalSeconds % 60;

            $formattedDuration = '';
            if ($hours > 0) {
                $formattedDuration .= sprintf('%d saat ', $hours);
            }
            $formattedDuration .= sprintf('%d dakika %d saniye', $minutes, $seconds);

            return $formattedDuration;
        }

        return $totalSeconds;
    }
}
