<?php

namespace App\Enums;

use ReflectionClass;

enum EarningReportStatusEnum: int
{
    case PENDING = 1;
    case PROCESSING = 2;
    case COMPLETED = 3;

    case FAILED = 4;


    public static function getTitles(): array
    {
        return array_map(
            fn(EarningReportStatusEnum $enum) => $enum->title(),
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
            self::PENDING => 'Bekliyor',
            self::PROCESSING => 'İşleniyor',
            self::COMPLETED => 'Tamamlandı',
            self::FAILED => 'Başarısız',
        };
    }
}
