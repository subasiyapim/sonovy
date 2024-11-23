<?php

namespace App\Enums;

use ReflectionClass;

enum SongStatusEnum: int
{
    //1: Onay bekliyor
    // 2: Yayınlandı
    case  WAITING_FOR_APPROVAL = 1;
    case  PUBLISHED = 2;

    public static function getTitles(): array
    {
        return array_map(
            fn(SongStatusEnum $enum) => $enum->title(),
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
            self::WAITING_FOR_APPROVAL => 'Onay Bekliyor',
            self::PUBLISHED => 'Yayınlandı',
        };
    }

    public static function getTitlesFromInputFormat(): array
    {
        return array_map(
            fn(SongStatusEnum $enum) => ['label' => $enum->title(), 'value' => $enum->value],
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

}
