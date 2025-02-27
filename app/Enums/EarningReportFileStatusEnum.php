<?php

namespace App\Enums;

enum EarningReportFileStatusEnum: int
{
    case PENDING = 1;
    case PROCESSING = 2;
    case COMPLETED = 3;
    case COMPLETED_WITH_ERRORS = 4;
    case FAILED = 5;

    public static function getTitles(): array
    {
        return array_map(
            fn(EarningReportFileStatusEnum $enum) => $enum->title(),
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public static function getKeys(): array
    {
        return array_column(self::cases(), 'name');
    }

    public function title(): string
    {
        return match ($this) {
            self::PENDING => 'Beklemede',
            self::PROCESSING => 'İşleniyor',
            self::COMPLETED => 'Tamamlandı',
            self::COMPLETED_WITH_ERRORS => 'Hatalarla Tamamlandı',
            self::FAILED => 'Başarısız'
        };
    }
}
