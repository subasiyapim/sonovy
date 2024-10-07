<?php

namespace App\Enums;

use ReflectionClass;

enum SongTypeEnum: int
{
    case SOUND = 1;
    case VIDEO = 2;
    case RINGTONE = 3;

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
            self::SOUND => __('panel.song.enums.type_sound'),
            self::VIDEO => __('panel.song.enums.type_video'),
            self::RINGTONE => __('panel.song.enums.type_ringtone'),
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
