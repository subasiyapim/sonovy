<?php

namespace App\Enums;

use ReflectionClass;

enum FeaturePeriodEnum: string
{
    case ONE_TIME = '1';
    case MONTHLY = '2';
    case ANNUAL = '3';


    public static function getTitles(): array
    {
        return array_map(
            fn(FeaturePeriodEnum $enum) => $enum->title(),
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public static function getKeys(): array
    {
        return array_keys(self::cases());
    }


    public function title()
    {
        return match ($this) {
            self::ONE_TIME => __('panel.feature.enums.period_one_time'),
            self::MONTHLY => __('panel.feature.enums.period_monthly'),
            self::ANNUAL => __('panel.feature.enums.period_annual'),
        };
    }
}
