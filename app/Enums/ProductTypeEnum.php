<?php

namespace App\Enums;

use ReflectionClass;

enum ProductTypeEnum: int
{
    case SOUND = 1;
    case VIDEO = 2;
    case RINGTONE = 3;

    public static function getTitles(): array
    {
        return array_map(
            fn(ProductTypeEnum $enum) => $enum->title(),
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public static function getKeys(): array
    {
        return array_column(self::cases(), 'name');
    }

    public function title()
    {
        return match ($this) {
            self::SOUND => __('control.song.enums.type_sound'),
            self::VIDEO => __('control.song.enums.type_video'),
            self::RINGTONE => __('control.song.enums.type_ringtone'),
        };
    }
}
