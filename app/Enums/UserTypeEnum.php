<?php

namespace App\Enums;

use ReflectionClass;

enum UserTypeEnum: int
{
    case ARTIST = 1;
    case LABEL = 2;
    case DISTRIBUTOR = 3;
    case ADMIN = 4;


    public static function getTitles(): array
    {
        return array_map(
            fn(UserTypeEnum $enum) => $enum->title(),
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
            self::ARTIST => __('panel.user.type_artist'),
            self::LABEL => __('panel.user.type_label'),
            self::DISTRIBUTOR => __('panel.user.type_distributor'),
            self::ADMIN => __('panel.user.type_admin'),
        };
    }
}
