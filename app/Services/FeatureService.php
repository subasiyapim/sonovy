<?php

namespace App\Services;

use App\Enums\FeaturePeriodEnum;

class FeatureService
{

    public static function getPeriodsFromInputFormat(): array
    {
        $data = FeaturePeriodEnum::getTitles();

        $periods = [];
        foreach ($data as $key => $value) {
            $periods[] = [
                'value' => $key,
                'label' => $value,
            ];
        }

        return $periods;
    }
}
