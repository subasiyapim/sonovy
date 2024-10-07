<?php

namespace App\Services;

use App\Models\Timezone;

class TimezoneService
{

    public static function getFromInputFormat()
    {
        return Timezone::get()->map(function ($timezone) {
            return
                [
                    'value' => $timezone->id,
                    'label' => $timezone->zone . ' - ' . $timezone->gmt
                ];
        })->toArray();
    }
}
