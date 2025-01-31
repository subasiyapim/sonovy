<?php

namespace App\Enums;

use ReflectionClass;

enum SongTypeEnum: int
{
    case SOUND = 1;
    case VIDEO = 2;
    case RINGTONE = 3;
    case APPLE_VIDEO = 4;

    public static function getTitles(): array
    {
        return array_map(
            fn(SongTypeEnum $enum) => $enum->title(),
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
            self::SOUND => __('control.song.enums.type_sound'),
            self::VIDEO => __('control.song.enums.type_video'),
            self::RINGTONE => __('control.song.enums.type_ringtone'),
            self::APPLE_VIDEO => __('control.song.enums.type_apple_video'),
        };
    }

    public static function getTitlesFromInputFormat(): array
    {
        return array_map(
            fn(SongTypeEnum $enum) => ['label' => $enum->title(), 'value' => $enum->value],
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

}
