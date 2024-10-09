<?php

namespace App\Enums;

use ReflectionClass;

enum UserGenderEnum: int
{
    case MALE = 1;
    case FEMALE = 2;
    case OTHER = 3;


    public static function getTitles(): array
    {
        return array_map(
            fn(UserGenderEnum $enum) => $enum->title(),
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
            self::MALE => __('control.user.enum.male'),
            self::FEMALE => __('control.user.enum.female'),
            self::OTHER => __('control.user.enum.other'),
        };
    }
}
