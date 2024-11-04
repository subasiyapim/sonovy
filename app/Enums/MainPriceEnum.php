<?php

namespace App\Enums;

use ReflectionClass;

enum MainPriceEnum: int
{
    case BALANCE = 1;
    case MIDDLE = 2;
    case FULL = 3;
    case PRIM = 4;

    public static function getTitles(): array
    {
        return array_map(
            fn(MainPriceEnum $enum) => $enum->title(),
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
            self::BALANCE => __('control.enums.main_price.balance'),
            self::MIDDLE => __('control.enums.main_price.middle'),
            self::FULL => __('control.enums.main_price.full'),
            self::PRIM => __('control.enums.main_price.prim'),
        };
    }
}
