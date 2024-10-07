<?php

namespace App\Enums;

use ReflectionClass;

enum YoutubeChannelThemeEnum: int
{
    case THEME1 = 1;
    case THEME2 = 2;
    case THEME3 = 3;
    case THEME4 = 4;
    case THEME5 = 5;


    public static function getTitles(): array
    {
        return array_map(
            fn(YoutubeChannelThemeEnum $enum) => $enum->title(),
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public static function getTitlesFromArray(): array
    {
        return array_map(
            fn(YoutubeChannelThemeEnum $enum) => ["label" => $enum->title(), "value" => $enum->value],
            array_combine(array_column(self::cases(), "value"), self::cases())
        );
    }

    public static function getKeys(): array
    {
        return array_keys(self::cases());
    }


    public function title()
    {
        return match ($this) {
            self::THEME1 => 'Theme 1',
            self::THEME2 => 'Theme 2',
            self::THEME3 => 'Theme 3',
            self::THEME4 => 'Theme 4',
            self::THEME5 => 'Theme 5',
        };
    }
}
