<?php

namespace App\Enums;

use ReflectionClass;

enum AlbumTypeEnum: int
{
    case SINGLE = 1;
    case ALBUM = 2;
    case EP = 3;


    public static function getTitles(): array
    {
        return array_map(
            fn(AlbumTypeEnum $enum) => $enum->title(),
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
            self::SINGLE => __('control.product.type_single'),
            self::ALBUM => __('control.product.type_album'),
            self::EP => __('control.product.type_ep'),
        };
    }
}
