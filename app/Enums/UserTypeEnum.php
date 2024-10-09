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
            self::ARTIST => __('control.user.type_artist'),
            self::LABEL => __('control.user.type_label'),
            self::DISTRIBUTOR => __('control.user.type_distributor'),
            self::ADMIN => __('control.user.type_admin'),
        };
    }
}
