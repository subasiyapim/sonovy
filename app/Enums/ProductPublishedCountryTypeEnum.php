<?php

namespace App\Enums;

use ReflectionClass;

enum ProductPublishedCountryTypeEnum: int
{
    case ALL = 1;
    case EXCEPT_SELECTED = 2;
    case ONLY_SELECTED = 3;


    public static function getTitles(): array
    {
        return array_map(
            fn(ProductPublishedCountryTypeEnum $enum) => $enum->title(),
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
            self::ALL => __('control.product.publish_country_type_all'),
            self::EXCEPT_SELECTED => __('control.product.publish_country_type_except_selected'),
            self::ONLY_SELECTED => __('control.product.publish_country_type_only_selected'),
        };
    }
}
