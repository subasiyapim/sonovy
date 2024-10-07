<?php

namespace App\Enums;

use ReflectionClass;

enum ConvertAudioEnum: int
{
    case PENDING = 1;
    case CONVERTING = 2;
    case COMPLETED = 3;
    case FAILED = 4;


    public static function getTitles(): array
    {
        return array_map(
            fn(ConvertAudioEnum $enum) => $enum->title(),
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
            self::PENDING => 'Pending',
            self::CONVERTING => 'Converting',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }
}
